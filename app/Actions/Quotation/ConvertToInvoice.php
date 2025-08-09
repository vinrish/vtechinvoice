<?php

declare(strict_types=1);

namespace App\Actions\Quotation;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Quotation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final readonly class ConvertToInvoice
{
    /**
     * Converts a quotation into an invoice and marks the quotation as converted.
     * Returns the created Invoice with relations loaded.
     */
    public function handle(Quotation $quotation, ?string $dueAt): Invoice
    {
        abort_unless(in_array($quotation->status, ['draft', 'sent'], true), 422, 'Cannot convert this quotation.');

        return DB::transaction(function () use ($quotation, $dueAt) {
            $number = 'INV-'.now()->format('Ymd').'-'.Str::upper(Str::random(5));

            $invoice = Invoice::create([
                'client_id' => $quotation->client_id,
                'number' => $number,
                'status' => 'draft',
                'issued_at' => now()->toDateString(),
                'due_at' => $dueAt,
                'currency' => $quotation->currency,
                'subtotal' => $quotation->subtotal,
                'tax' => $quotation->tax,
                'total' => $quotation->total,
                'notes' => $quotation->notes,
            ]);

            foreach ($quotation->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total' => $item->total,
                ]);
            }

            $quotation->status = 'converted';
            $quotation->save();

            return $invoice->load('client', 'items');
        });
    }
}
