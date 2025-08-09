<?php

declare(strict_types=1);

namespace App\Actions\Invoice;

use App\Models\Invoice;
use Illuminate\Support\Facades\Storage;
use PDF;

final readonly class GenerateInvoicePdf
{
    /**
     * Ensure a PDF is generated and stored; returns the storage path.
     */
    public function handle(Invoice $invoice): string
    {
        // Ensure directory exists
        $dir = 'invoices';
        if (! Storage::disk('local')->exists($dir)) {
            Storage::disk('local')->makeDirectory($dir);
        }

        if (! $invoice->relationLoaded('items')) {
            $invoice->load('client', 'items');
        }

        $pdf = PDF::loadView('pdf.invoice', ['invoice' => $invoice]);
        $path = 'invoices/'.$invoice->number.'.pdf';
        Storage::disk('local')->put($path, $pdf->output());

        if ($invoice->pdf_path !== $path) {
            $invoice->pdf_path = $path;
            $invoice->save();
        }

        return $path;
    }
}
