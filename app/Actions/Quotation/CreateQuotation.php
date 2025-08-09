<?php

declare(strict_types=1);

namespace App\Actions\Quotation;

use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final readonly class CreateQuotation
{
    /**
     * @param array{
     *   client_id:int,
     *   valid_until?:string|null,
     *   currency?:string|null,
     *   notes?:string|null,
     *   items: array<int, array{ description:string, quantity:numeric, unit_price:numeric }>,
     *   tax_rate?: numeric|null
     * } $data
     */
    public function handle(array $data): Quotation
    {
        return DB::transaction(function () use ($data) {
            $number = 'Q-'.now()->format('Ymd').'-'.Str::upper(Str::random(5));
            $currency = $data['currency'] ?? 'USD';

            $subtotal = 0.0;
            foreach ($data['items'] as $it) {
                $subtotal += (float) $it['quantity'] * (float) $it['unit_price'];
            }
            $taxRate = (float) ($data['tax_rate'] ?? 0);
            $tax = round($subtotal * $taxRate / 100, 2);
            $total = round($subtotal + $tax, 2);

            $quotation = Quotation::create([
                'client_id' => $data['client_id'],
                'number' => $number,
                'status' => 'draft',
                'valid_until' => $data['valid_until'] ?? null,
                'currency' => $currency,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $it) {
                $lineTotal = (float) $it['quantity'] * (float) $it['unit_price'];
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'description' => $it['description'],
                    'quantity' => $it['quantity'],
                    'unit_price' => $it['unit_price'],
                    'total' => $lineTotal,
                ]);
            }

            return $quotation->load('items', 'client');
        });
    }
}
