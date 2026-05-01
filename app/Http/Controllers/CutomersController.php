<?php

namespace App\Http\Controllers;


use App\Models\Customer;

use Illuminate\Http\Request;


class CutomersController extends Controller
{
    // public function storeCustomer(Request $request)
    // {
    //     $request->validate([
    //         'name'  => 'required|string|max:255',
    //         'phone' => 'required|string|unique:customers,phone|max:20',
    //         'city'  => 'nullable|string|max:100',
    //         'notes' => 'nullable|string|max:500',
    //     ]);

    //     $customer = Customer::create([
    //         'name'      => trim($request->name),
    //         'phone'     => trim($request->phone),
    //         'city'      => $request->city,
    //         'notes'     => $request->notes,
    //         'is_active' => true,
    //     ]);

    //     return response()->json([
    //         'success'  => true,
    //         'customer' => [
    //             'id'           => $customer->id,
    //             'name'         => $customer->name,
    //             'phone'        => $customer->phone,
    //             'city'         => $customer->city,
    //             'loan_balance' => 0,
    //         ],
    //     ]);
    // }

}