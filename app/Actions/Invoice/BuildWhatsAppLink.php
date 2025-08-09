<?php

declare(strict_types=1);

namespace App\Actions\Invoice;

use App\Models\Invoice;

final readonly class BuildWhatsAppLink
{
    /**
     * Builds a WhatsApp share URL for the given invoice and optional phone.
     * Returns an array with 'whatsapp_url'.
     */
    public function handle(Invoice $invoice, ?string $phone, string $publicDownloadUrl): array
    {
        $message = urlencode('Hello '.$invoice->client->name.', here is your invoice '.$invoice->number.': '.$publicDownloadUrl);

        $finalPhone = $phone !== null && $phone !== '' && $phone !== '0' ? $phone : ($invoice->client->phone ?? '');
        $waUrl = 'https://wa.me/'.preg_replace('/\D+/', '', (string) $finalPhone).'?text='.$message;

        return ['whatsapp_url' => $waUrl];
    }
}
