<?php

declare(strict_types=1);

namespace App\Actions\Invoice;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final readonly class CreateInvoice
{
    /**
     * Create an invoice with items and computed totals.
     *
     * @param array{
     *   client_id:int,
     *   issued_at?:string|null,
     *   due_at?:string|null,
     *   currency?:string|null,
     *   notes?:string|null,
     *   items: array<int, array{ description:string, quantity:numeric, unit_price:numeric }>,
     *   tax_rate?: numeric|null
     * } $data
     */
    public function handle(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            $number = 'INV-'.now()->format('Ymd').'-'.Str::upper(Str::random(5));
            $currency = $data['currency'] ?? 'USD';

            $subtotal = 0.0;
            foreach ($data['items'] as $it) {
                $subtotal += (float) $it['quantity'] * (float) $it['unit_price'];
            }
            $taxRate = (float) ($data['tax_rate'] ?? 0);
            $tax = round($subtotal * $taxRate / 100, 2);
            $total = round($subtotal + $tax, 2);

            $invoice = Invoice::create([
                'client_id' => $data['client_id'],
                'number' => $number,
                'status' => 'draft',
                'issued_at' => $data['issued_at'] ?? now()->toDateString(),
                'due_at' => $data['due_at'] ?? null,
                'currency' => $currency,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $it) {
                $lineTotal = (float) $it['quantity'] * (float) $it['unit_price'];
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $it['description'],
                    'quantity' => $it['quantity'],
                    'unit_price' => $it['unit_price'],
                    'total' => $lineTotal,
                ]);
            }

            return $invoice->load('items', 'client');
        });
    }
}
