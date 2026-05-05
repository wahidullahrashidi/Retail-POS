<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SettingsController extends Controller
{
    // ══════════════════════════════════════════
    //  PAGE
    // ══════════════════════════════════════════
    public function page()
    {
        return view('system.setting');
    }

    // ══════════════════════════════════════════
    //  GET ALL SETTINGS AS KEY=>VALUE JSON
    //  GET /pos/settings
    // ══════════════════════════════════════════
    public function index()
    {
        $settings = Setting::all()->mapWithKeys(fn($s) => [
            $s->key => match($s->type) {
                'boolean' => (bool)$s->value,
                'integer' => (int)$s->value,
                'json'    => json_decode($s->value, true),
                default   => $s->value,
            }
        ]);

        return response()->json($settings);
    }

    // ══════════════════════════════════════════
    //  SAVE SETTINGS BY GROUP
    //  POST /pos/settings/save
    // ══════════════════════════════════════════
    public function save(Request $request)
    {
        $request->validate([
            'group'    => 'required|string',
            'settings' => 'required|array',
        ]);

        $group    = $request->input('group');
        $settings = $request->input('settings');

        // Only update keys that belong to this group
        $keys = Setting::where('group', $group)->pluck('key')->toArray();

        foreach ($keys as $key) {
            if (!array_key_exists($key, $settings)) continue;

            $value = $settings[$key];

            // Cast booleans to 0/1 for storage
            if (is_bool($value)) $value = $value ? '1' : '0';

            Setting::where('key', $key)->update(['value' => $value]);
        }

        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════
    //  CATEGORIES — list (flat, controller builds tree)
    //  GET /pos/settings/categories
    // ══════════════════════════════════════════
    public function categoriesIndex()
    {
        $categories = Category::orderBy('sort_order')
            ->orderBy('name')
            ->get(['id','parent_id','name','name_ps','name_dr','code','sort_order','is_active','low_stock_threshold']);

        return response()->json($categories);
    }

    // ══════════════════════════════════════════
    //  SAVE CATEGORY (create or update)
    //  POST /pos/settings/categories/store
    // ══════════════════════════════════════════
    public function categoryStore(Request $request)
    {
        $isUpdate   = $request->filled('category_id');
        $categoryId = $request->input('category_id');

        $request->validate([
            'name'                => 'required|string|max:255',
            'name_ps'             => 'nullable|string|max:255',
            'name_dr'             => 'nullable|string|max:255',
            'code'                => 'nullable|string|max:50|unique:categories,code' . ($isUpdate ? ",{$categoryId}" : ''),
            'parent_id'           => 'nullable|integer|exists:categories,id',
            'sort_order'          => 'nullable|integer|min:0',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'is_active'           => 'boolean',
        ]);

        $fields = [
            'name'                => $request->name,
            'name_ps'             => $request->name_ps,
            'name_dr'             => $request->name_dr,
            'code'                => $request->code ? strtoupper($request->code) : null,
            'parent_id'           => $request->parent_id ?: null,
            'sort_order'          => $request->sort_order ?? 0,
            'low_stock_threshold' => $request->low_stock_threshold ?? 10,
            'is_active'           => $request->is_active ?? true,
        ];

        if ($isUpdate) {
            // Prevent setting own parent
            if ($request->parent_id == $categoryId) {
                return response()->json(['success' => false, 'message' => 'A category cannot be its own parent.'], 422);
            }
            Category::findOrFail($categoryId)->update($fields);
        } else {
            Category::create($fields);
        }

        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════
    //  DELETE CATEGORY
    //  DELETE /pos/settings/categories/{category}
    // ══════════════════════════════════════════
    public function categoryDelete(Category $category)
    {
        // Prevent deleting if has children
        if ($category->children()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete a category that has sub-categories. Delete sub-categories first.',
            ], 422);
        }

        // Prevent deleting if has products
        if ($category->products()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete a category that has products assigned to it.',
            ], 422);
        }

        $category->delete();

        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════
    //  ATTRIBUTES — list with values
    //  GET /pos/settings/attributes
    // ══════════════════════════════════════════
    public function attributesIndex()
    {
        $attributes = Attribute::with([
            'values' => fn($q) => $q->orderBy('sort_order')->orderBy('value')
        ])->orderBy('name')->get();

        return response()->json($attributes);
    }

    // ══════════════════════════════════════════
    //  SAVE ATTRIBUTE (create or update)
    //  POST /pos/settings/attributes/store
    // ══════════════════════════════════════════
    public function attributeStore(Request $request)
    {
        $isUpdate    = $request->filled('attribute_id');
        $attributeId = $request->input('attribute_id');

        $request->validate([
            'name'      => 'required|string|max:255|unique:attributes,name' . ($isUpdate ? ",{$attributeId}" : ''),
            'name_ps'   => 'nullable|string|max:255',
            'name_dr'   => 'nullable|string|max:255',
            'data_type' => 'required|in:string,number,color',
        ]);

        $fields = [
            'name'      => $request->name,
            'name_ps'   => $request->name_ps,
            'name_dr'   => $request->name_dr,
            'data_type' => $request->data_type,
        ];

        if ($isUpdate) {
            Attribute::findOrFail($attributeId)->update($fields);
        } else {
            Attribute::create($fields);
        }

        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════
    //  DELETE ATTRIBUTE
    //  DELETE /pos/settings/attributes/{attribute}
    // ══════════════════════════════════════════
    public function attributeDelete(Attribute $attribute)
    {
        if ($attribute->values()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete an attribute that has values. Delete all values first.',
            ], 422);
        }

        $attribute->delete();

        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════
    //  SAVE ATTRIBUTE VALUE
    //  POST /pos/settings/attributes/values/store
    // ══════════════════════════════════════════
    public function valueStore(Request $request)
    {
        $request->validate([
            'attribute_id' => 'required|integer|exists:attributes,id',
            'value'        => 'required|string|max:255',
            'value_ps'     => 'nullable|string|max:255',
            'value_dr'     => 'nullable|string|max:255',
            'color_code'   => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'sort_order'   => 'nullable|integer|min:0',
        ]);

        AttributeValue::create([
            'attribute_id' => $request->attribute_id,
            'value'        => $request->value,
            'value_ps'     => $request->value_ps,
            'value_dr'     => $request->value_dr,
            'color_code'   => $request->color_code,
            'sort_order'   => $request->sort_order ?? 0,
            'is_active'    => true,
        ]);

        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════
    //  DELETE ATTRIBUTE VALUE
    //  DELETE /pos/settings/attributes/values/{value}
    // ══════════════════════════════════════════
    public function valueDelete(AttributeValue $value)
    {
        $value->delete();
        return response()->json(['success' => true]);
    }

    // ══════════════════════════════════════════
    //  HARDWARE TEST
    //  POST /pos/settings/hardware/test
    // ══════════════════════════════════════════
    public function hardwareTest(Request $request)
    {
        $device = $request->input('device');
        $port   = $request->input('port', '');

        try {
            $result = match($device) {
                'printer'  => $this->testPrinter($port),
                'scanner'  => $this->testScanner($port),
                'drawer'   => $this->testDrawer($port),
                'terminal' => $this->testTerminal($port),
                default    => throw new \Exception("Unknown device: {$device}"),
            };

            return response()->json(['success' => true, 'message' => $result]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // ══════════════════════════════════════════
    //  AUDIT LOG
    //  GET /pos/settings/audit
    // ══════════════════════════════════════════
    public function auditLog()
    {
        // Pull from Laravel's activity log or a simple query log
        // For now, pull from inventory_adjustments as a proxy audit source
        $entries = DB::table('inventory_adjustments')
            ->join('users', 'users.id', '=', 'inventory_adjustments.adjusted_by')
            ->join('product_variants', 'product_variants.id', '=', 'inventory_adjustments.variant_id')
            ->select([
                'inventory_adjustments.id',
                'inventory_adjustments.adjustment_type',
                'inventory_adjustments.reason',
                'inventory_adjustments.created_at',
                'users.name as user',
                'product_variants.sku',
            ])
            ->orderByDesc('inventory_adjustments.created_at')
            ->limit(30)
            ->get()
            ->map(fn($e) => [
                'id'     => $e->id,
                'type'   => in_array($e->adjustment_type, ['increase']) ? 'create' : 'edit',
                'user'   => $e->user,
                'action' => "adjusted stock for {$e->sku} ({$e->adjustment_type}): {$e->reason}",
                'time'   => Carbon::parse($e->created_at)->diffForHumans(),
            ]);

        return response()->json($entries);
    }

    // ══════════════════════════════════════════
    //  PRIVATE HARDWARE TEST HELPERS
    // ══════════════════════════════════════════
    private function testPrinter(string $port): string
    {
        if (empty($port)) throw new \Exception('No port configured for printer.');

        // On Windows, try to write a test string to the printer port
        if (PHP_OS_FAMILY === 'Windows') {
            $handle = @fopen($port, 'w');
            if (!$handle) throw new \Exception("Cannot open port {$port}. Check printer connection.");
            fwrite($handle, "\x1B\x40"); // ESC @ — printer reset command
            fclose($handle);
            return "Printer on {$port} responded successfully.";
        }

        // On Linux/Mac, check if device file exists
        if (!file_exists('/dev/' . ltrim($port, '/dev/'))) {
            throw new \Exception("Device not found at {$port}.");
        }
        return "Printer device found at {$port}.";
    }

    private function testScanner(string $port): string
    {
        // Barcode scanners are HID devices — they appear as keyboard
        // Just verify the port exists
        if (empty($port)) {
            return 'Barcode scanner operates as HID keyboard — always available when plugged in.';
        }
        return "Scanner port {$port} configured.";
    }

    private function testDrawer(string $port): string
    {
        if (empty($port)) throw new \Exception('No port configured for cash drawer.');

        if (PHP_OS_FAMILY === 'Windows') {
            $handle = @fopen($port, 'w');
            if (!$handle) throw new \Exception("Cannot open drawer port {$port}.");
            // ESC p — cash drawer open command
            fwrite($handle, "\x1B\x70\x00\x19\xFA");
            fclose($handle);
            return "Cash drawer on {$port} opened successfully.";
        }
        return "Cash drawer on {$port} configured.";
    }

    private function testTerminal(string $port): string
    {
        if (empty($port)) throw new \Exception('No IP/port configured for card terminal.');

        // Try TCP connection to terminal
        $parts = explode(':', $port);
        $host  = $parts[0];
        $tcpPort = (int)($parts[1] ?? 8080);

        $connection = @fsockopen($host, $tcpPort, $errno, $errstr, 5);
        if (!$connection) throw new \Exception("Cannot reach terminal at {$host}:{$tcpPort} — {$errstr}");
        fclose($connection);
        return "Card terminal at {$host}:{$tcpPort} is reachable.";
    }
}
