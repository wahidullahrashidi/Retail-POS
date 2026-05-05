<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Shift;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserController extends Controller
{
    // ══════════════════════════════════════════
    //  PAGE — blade with stats
    // ══════════════════════════════════════════
    public function page()
    {
        $roles = Role::orderBy('name')->get();

        $stats = [
            'total'    => User::count(),
            'active'   => User::where('is_active', true)->count(),
            'admins'   => User::whereHas('role', fn($q) => $q->where('name', 'admin'))->count(),
            'managers' => User::whereHas('role', fn($q) => $q->where('name', 'manager'))->count(),
            'cashiers' => User::whereHas('role', fn($q) => $q->where('name', 'cashier'))->count(),
        ];

        return view('system.users', compact('stats', 'roles'));
    }

    // ══════════════════════════════════════════
    //  INDEX — JSON user list
    //  GET /pos/users
    // ══════════════════════════════════════════
    public function index(Request $request)
    {
        $q    = $request->input('q', '');
        $role = $request->input('role', '');
        $tab  = $request->input('tab', 'all');

        $query = User::query()
            ->leftJoin('roles', 'roles.id', '=', 'users.role_id')
            ->leftJoin(
                DB::raw('(SELECT user_id, COUNT(*) as sale_count, SUM(total_amount) as total_sales
                          FROM sales WHERE status = "completed" GROUP BY user_id) as sa'),
                'sa.user_id', '=', 'users.id'
            )
            ->leftJoin(
                DB::raw('(SELECT user_id, COUNT(*) as shift_count
                          FROM shifts GROUP BY user_id) as sh'),
                'sh.user_id', '=', 'users.id'
            )
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.photo',
                'users.role_id',
                'users.is_active',
                'users.created_at',
                DB::raw('users.pin_code IS NOT NULL AND users.pin_code != "" as has_pin'),
                DB::raw('COALESCE(roles.name, "user") as role_name'),
                DB::raw('COALESCE(roles.display_name, "User") as role_display'),
                'roles.permissions',
                DB::raw('COALESCE(sa.sale_count, 0) as sale_count'),
                DB::raw('COALESCE(sa.total_sales, 0) as total_sales'),
                DB::raw('COALESCE(sh.shift_count, 0) as shift_count'),
            ]);

        // ── Search ──
        if ($q) {
            $query->where(fn($qb) =>
                $qb->where('users.name',  'like', "%{$q}%")
                   ->orWhere('users.email', 'like', "%{$q}%")
            );
        }

        // ── Role filter ──
        if ($role) $query->where('users.role_id', $role);

        // ── Tab filter ──
        match ($tab) {
            'active'   => $query->where('users.is_active', true),
            'inactive' => $query->where('users.is_active', false),
            default    => null,
        };

        $users = $query->orderBy('users.name')->get()->map(fn($u) => [
            'id'           => $u->id,
            'name'         => $u->name,
            'email'        => $u->email,
            'photo'        => $u->photo,
            'role_id'      => $u->role_id,
            'role_name'    => $u->role_name,
            'role_display' => $u->role_display,
            'permissions'  => is_string($u->permissions)
                              ? json_decode($u->permissions, true)
                              : ($u->permissions ?? []),
            'has_pin'      => (bool)$u->has_pin,
            'is_active'    => (bool)$u->is_active,
            'sale_count'   => (int)$u->sale_count,
            'total_sales'  => (float)$u->total_sales,
            'shift_count'  => (int)$u->shift_count,
            'last_login'   => null, // add last_login_at column if needed
            'created_at'   => Carbon::parse($u->created_at)->format('d M Y'),
        ]);

        return response()->json($users);
    }

    // ══════════════════════════════════════════
    //  STORE — create or update user
    //  POST /pos/users/store
    // ══════════════════════════════════════════
    public function store(Request $request)
    {
        $isUpdate = $request->filled('user_id');
        $userId   = $request->input('user_id');

        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email' . ($isUpdate ? ",{$userId}" : ''),
            'role_id'     => 'required|integer|exists:roles,id',
            'password'    => $isUpdate ? 'nullable|min:8' : 'required|min:8',
            'pin_code'    => 'nullable|digits:4',
            'permissions' => 'nullable|string',
            'photo'       => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $photoPath = null;

            // ── Handle photo upload ──────────────
            if ($request->hasFile('photo')) {
                $file      = $request->file('photo');
                $filename  = 'users/user_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->storeAs('public/users', $filename);
                $photoPath = $filename;

                // Delete old photo if updating
                if ($isUpdate) {
                    $oldUser = User::find($userId);
                    if ($oldUser?->photo) {
                        Storage::delete('public/users/' . $oldUser->photo);
                    }
                }
            }

            // ── Parse permissions ────────────────
            $permissions = [];
            if ($request->filled('permissions')) {
                $permissions = json_decode($request->input('permissions'), true) ?? [];
            }

            // ── Prepare fields ───────────────────
            $fields = [
                'name'        => $request->name,
                'email'       => $request->email,
                'role_id'     => $request->role_id,
                'permissions' => json_encode($permissions),
            ];

            if ($photoPath)             $fields['photo']    = $photoPath;
            if ($request->filled('password')) $fields['password'] = Hash::make($request->password);
            if ($request->filled('pin_code')) $fields['pin_code'] = Hash::make($request->pin_code);

            if ($isUpdate) {
                $user = User::findOrFail($userId);
                $user->update($fields);
            } else {
                $fields['is_active'] = true;
                $user = User::create($fields);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'user'    => [
                    'id'           => $user->id,
                    'name'         => $user->name,
                    'email'        => $user->email,
                    'photo'        => $user->photo,
                    'role_id'      => $user->role_id,
                    'is_active'    => $user->is_active,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    // ══════════════════════════════════════════
    //  DETAIL — user detail + recent shifts
    //  GET /pos/users/{user}/detail
    // ══════════════════════════════════════════
    public function detail(User $user)
    {
        $recentShifts = Shift::where('user_id', $user->id)
            ->orderByDesc('opened_at')
            ->limit(5)
            ->get()
            ->map(function ($s) {
                $totalSales = Sale::where('shift_id', $s->id)
                    ->where('status', 'completed')
                    ->sum('total_amount');

                $duration = '—';
                if ($s->closed_at) {
                    $mins = Carbon::parse($s->opened_at)->diffInMinutes(Carbon::parse($s->closed_at));
                    $duration = $mins >= 60
                        ? floor($mins / 60) . 'h ' . ($mins % 60) . 'm'
                        : $mins . 'm';
                }

                return [
                    'id'          => $s->id,
                    'opened_at'   => Carbon::parse($s->opened_at)->format('d M Y, H:i'),
                    'duration'    => $duration,
                    'total_sales' => (float)$totalSales,
                    'is_closed'   => (bool)$s->is_closed,
                ];
            });

        $saleCount   = Sale::where('user_id', $user->id)->where('status', 'completed')->count();
        $totalSales  = Sale::where('user_id', $user->id)->where('status', 'completed')->sum('total_amount');
        $shiftCount  = Shift::where('user_id', $user->id)->count();

        $role        = $user->role;
        $permissions = is_string($role?->permissions)
            ? json_decode($role->permissions, true)
            : ($role?->permissions ?? []);

        return response()->json([
            'user' => [
                'id'           => $user->id,
                'name'         => $user->name,
                'email'        => $user->email,
                'photo'        => $user->photo,
                'role_id'      => $user->role_id,
                'role_name'    => $role?->name,
                'role_display' => $role?->display_name,
                'permissions'  => $permissions,
                'has_pin'      => !empty($user->pin_code),
                'is_active'    => (bool)$user->is_active,
                'sale_count'   => $saleCount,
                'total_sales'  => (float)$totalSales,
                'shift_count'  => $shiftCount,
                'created_at'   => Carbon::parse($user->created_at)->format('d M Y'),
                'last_login'   => null,
            ],
            'shifts' => $recentShifts,
        ]);
    }

    // ══════════════════════════════════════════
    //  RESET PASSWORD
    //  POST /pos/users/password
    // ══════════════════════════════════════════
    public function resetPassword(Request $request)
    {
        $request->validate([
            'user_id'  => 'required|integer|exists:users,id',
            'password' => 'required|string|min:8',
        ]);

        // Prevent resetting own password through this endpoint
        if ($request->user_id == auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Use your profile settings to change your own password.',
            ], 422);
        }

        User::findOrFail($request->user_id)->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════
    //  TOGGLE ACTIVE
    //  POST /pos/users/{user}/toggle
    // ══════════════════════════════════════════
    public function toggle(User $user)
    {
        // Prevent deactivating yourself
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot deactivate your own account.',
            ], 422);
        }

        $user->update(['is_active' => !$user->is_active]);

        return response()->json([
            'success'   => true,
            'is_active' => $user->is_active,
        ]);
    }
}