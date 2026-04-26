<?php

namespace App\Services;

use App\Models\Sale;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;

class ReceiptService
{
    private $printer;
    private $connector;

    public function __construct()
    {
        // For Windows USB printer (adjust printer name as needed)
        // Common names: "USB001", "POS-58", "EPSON TM-T82III"
        try {
            $this->connector = new WindowsPrintConnector("USB001");
            $this->printer = new Printer($this->connector);
        } catch (\Exception $e) {
            // Fallback to file for testing (saves to file instead of printing)
            $this->connector = new FilePrintConnector(storage_path('app/receipt.txt'));
            $this->printer = new Printer($this->connector);
        }
    }

    public function printSaleReceipt(Sale $sale)
    {
        $storeName = config('pos.store_name', 'Afghan POS Store');
        $storeAddress = config('pos.store_address', 'Kabul, Afghanistan');
        $storePhone = config('pos.store_phone', '070-000-0000');

        try {
            // Header
            $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            $this->printer->setEmphasis(true);
            $this->printer->text($storeName . "\n");
            $this->printer->setEmphasis(false);
            $this->printer->text($storeAddress . "\n");
            $this->printer->text("Phone: " . $storePhone . "\n");
            $this->printer->text(str_repeat("-", 32) . "\n");

            // Receipt Info
            $this->printer->setJustification(Printer::JUSTIFY_LEFT);
            $this->printer->text("Receipt #: " . substr($sale->local_id, 0, 8) . "\n");
            $this->printer->text("Date: " . $sale->created_at->format('Y-m-d H:i') . "\n");
            $this->printer->text("Cashier: " . $sale->user->name . "\n");
            
            if ($sale->customer) {
                $this->printer->text("Customer: " . $sale->customer->name . "\n");
            }
            
            $this->printer->text(str_repeat("-", 32) . "\n");

            // Items
            $this->printer->text(sprintf("%-15s %3s %8s\n", "Item", "Qty", "Price"));
            $this->printer->text(str_repeat("-", 32) . "\n");

            foreach ($sale->items as $item) {
                $name = substr($item->variant->product->name, 0, 15);
                $qty = $item->quantity;
                $price = number_format($item->line_total);
                $this->printer->text(sprintf("%-15s %3d %8s\n", $name, $qty, $price));
            }

            $this->printer->text(str_repeat("-", 32) . "\n");

            // Totals
            $this->printer->setJustification(Printer::JUSTIFY_RIGHT);
            $this->printer->text(sprintf("%-20s %10s\n", "Subtotal:", number_format($sale->subtotal) . " ؋"));
            
            if ($sale->discount_amount > 0) {
                $this->printer->text(sprintf("%-20s %10s\n", "Discount:", "-" . number_format($sale->discount_amount) . " ؋"));
            }
            
            $this->printer->setEmphasis(true);
            $this->printer->text(sprintf("%-20s %10s\n", "TOTAL:", number_format($sale->total_amount) . " ؋"));
            $this->printer->setEmphasis(false);

            // Payment Info
            $this->printer->text(str_repeat("-", 32) . "\n");
            $this->printer->setJustification(Printer::JUSTIFY_LEFT);
            $this->printer->text("Payment: " . strtoupper($sale->payment_method) . "\n");
            
            if ($sale->payment_method === 'cash') {
                $this->printer->text("Tendered: " . number_format($sale->amount_paid) . " ؋\n");
                $this->printer->text("Change: " . number_format($sale->change_amount) . " ؋\n");
            } elseif ($sale->loan_id) {
                $this->printer->text("Paid: " . number_format($sale->amount_paid) . " ؋\n");
                $this->printer->text("Remaining: " . number_format($sale->loan->remaining_balance) . " ؋\n");
                $this->printer->text("Due Date: " . $sale->loan->due_date . "\n");
            }

            // Footer
            $this->printer->setJustification(Printer::JUSTIFY_CENTER);
            $this->printer->text(str_repeat("-", 32) . "\n");
            $this->printer->text("Thank you for shopping!\n");
            $this->printer->text("Please come again\n");
            $this->printer->text("\n\n");

            // Cut paper
            $this->printer->cut();

            return true;

        } catch (\Exception $e) {
            \Log::error('Print error: ' . $e->getMessage());
            return false;
        }
    }

    public function __destruct()
    {
        if ($this->printer) {
            $this->printer->close();
        }
    }
}