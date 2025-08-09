<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

final class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invoice $invoice) {}

    public function build(): self
    {
        $subject = 'Invoice '.$this->invoice->number;

        $mail = $this->subject($subject)
            ->view('emails.invoice', [
                'invoice' => $this->invoice,
            ]);

        if ($this->invoice->pdf_path && file_exists(storage_path('app/'.$this->invoice->pdf_path))) {
            $mail->attach(storage_path('app/'.$this->invoice->pdf_path), [
                'as' => 'Invoice-'.$this->invoice->number.'.pdf',
                'mime' => 'application/pdf',
            ]);
        }

        return $mail;
    }
}
