<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    public function openForm()
    {
        // Check if already has open shift
        $existing = Shift::where('user_id', Auth::id())
            ->where('is_closed', false)
            ->first();

        if ($existing) {
            return redirect()->route('pos.index');
        }

        return view('shifts.open');
    }

    public function open(Request $request)
    {
        $request->validate([
            'starting_cash' => 'required|numeric|min:0',
        ]);

        // Check again for existing shift
        $existing = Shift::where('user_id', Auth::id())
            ->where('is_closed', false)
            ->first();

        if ($existing) {
            return redirect()->route('pos.index');
        }

        Shift::create([
            'user_id' => Auth::id(),
            'opened_at' => now(),
            'starting_cash' => $request->starting_cash,
            'is_closed' => false,
        ]);

        return redirect()->route('pos.index')->with('success', 'Shift opened successfully');
    }

    public function closeForm()
    {
        $shift = Shift::where('user_id', Auth::id())
            ->where('is_closed', false)
            ->firstOrFail();

        // Calculate expected cash (you'll implement sales logic later)
        $cashSales = 0; // Placeholder - will calculate from sales
        $expectedCash = $shift->starting_cash + $cashSales;

        return view('shifts.close', compact('shift', 'expectedCash'));
    }

    public function close(Request $request)
    {
        $request->validate([
            'actual_cash' => 'required|numeric|min:0',
            'discrepancy_note' => 'nullable|string',
        ]);

        $shift = Shift::where('user_id', Auth::id())
            ->where('is_closed', false)
            ->firstOrFail();

        $expectedCash = $shift->starting_cash; // + cash sales when implemented
        $actualCash = $request->actual_cash;
        $discrepancy = $actualCash - $expectedCash;

        $shift->update([
            'closed_at' => now(),
            'expected_cash' => $expectedCash,
            'actual_cash' => $actualCash,
            'discrepancy' => $discrepancy,
            'discrepancy_note' => $request->discrepancy_note,
            'is_closed' => true,
            'closed_by' => Auth::id(),
        ]);

        return redirect()->route('login')->with('success', 'Shift closed. Please login again.');
    }

    public function report($id)
    {
        $shift = Shift::with(['user', 'closer'])->findOrFail($id);
        return view('shifts.report', compact('shift'));
    }
}