<?php

declare(strict_types=1);

namespace App\Actions\Invoice;

use App\Mail\InvoiceMail;
use App\Models\Invoice;
use Illuminate\Support\Facades\Mail;

final readonly class SendInvoiceEmail
{
    public function __construct(private GenerateInvoicePdf $generateInvoicePdf) {}

    /**
     * Send invoice via email. If $email is null, uses client's email.
     * Returns array with 'sent' and 'email'.
     */
    public function handle(Invoice $invoice, ?string $email = null): array
    {
        $this->generateInvoicePdf->handle($invoice);

        $to = $email !== null && $email !== '' && $email !== '0' ? $email : ($invoice->client->email ?? null);
        if (! $to) {
            abort(422, 'No email provided for client.');
        }

        $mailable = new InvoiceMail($invoice);
        $mailable->with(['viewUrl' => route('invoices.show', $invoice)]);

        Mail::to($to)->send($mailable);

        $invoice->status = 'sent';
        $invoice->save();

        return ['sent' => true, 'email' => $to];
    }
}
