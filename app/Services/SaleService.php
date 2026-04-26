<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\ProductVariant;
use App\Models\Loan;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SaleService
{
    public function createSale(array $data, array $cartItems, int $userId, ?int $shiftId)
    {
        return DB::transaction(function () use ($data, $cartItems, $userId, $shiftId) {
            // Calculate totals
            $subtotal = collect($cartItems)->sum(fn($item) => $item['price'] * $item['qty']);
            $discount = $data['discount'] ?? 0;
            $total = $subtotal - $discount;

            // Create sale
            $sale = Sale::create([
                'local_id' => (string) Str::uuid(),
                'shift_id' => $shiftId,
                'user_id' => $userId,
                'customer_id' => $data['customer_id'] ?? null,
                'sale_type' => $data['payment_method'] === 'loan' ? 'loan' : 'regular',
                'status' => 'completed',
                'subtotal' => $subtotal,
                'discount_amount' => $discount,
                'tax_amount' => 0,
                'total_amount' => $total,
                'payment_method' => $data['payment_method'],
                'amount_paid' => $data['amount_paid'] ?? $total,
                'change_amount' => $data['change_amount'] ?? 0,
                'receipt_printed' => true,
                'sync_status' => 'pending',
            ]);

            // Create sale items and deduct stock
            foreach ($cartItems as $item) {
                $variant = ProductVariant::find($item['id']);
                
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'variant_id' => $item['id'],
                    'quantity' => $item['qty'],
                    'unit_price' => $item['price'],
                    'cost_price' => $variant->cost_price ?? $variant->product->cost_price,
                    'line_total' => $item['price'] * $item['qty'],
                ]);

                // Deduct stock
                $variant->decrement('stock_quantity', $item['qty']);
            }

            // Create loan if payment method is loan
            if ($data['payment_method'] === 'loan' && ($data['amount_paid'] ?? 0) < $total) {
                $loan = Loan::create([
                    'sale_id' => $sale->id,
                    'customer_id' => $data['customer_id'],
                    'original_amount' => $total,
                    'amount_paid' => $data['amount_paid'] ?? 0,
                    'remaining_balance' => $total - ($data['amount_paid'] ?? 0),
                    'due_date' => $data['due_date'] ?? now()->addDays(30),
                    'status' => 'active',
                ]);

                $sale->update(['loan_id' => $loan->id]);
            }

            return $sale;
        });
    }
}