<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Invoice\BuildWhatsAppLink;
use App\Actions\Invoice\CreateInvoice;
use App\Actions\Invoice\GenerateInvoicePdf;
use App\Actions\Invoice\SendInvoiceEmail;
use App\Http\Requests\StoreInvoiceRequest;
use App\Models\Invoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

final class InvoiceController extends Controller
{
    public function index(): JsonResponse
    {
        $invoices = Invoice::with('client')->latest()->paginate(20);

        return Inertia::render('invoices/Index', [
            'invoices' => $invoices,
        ])->toResponse(request());
    }

    public function store(StoreInvoiceRequest $request, CreateInvoice $createInvoice): Response|RedirectResponse
    {
        $data = $request->validated();
        $invoice = $createInvoice->handle($data);

        // If the request comes from an Inertia visit, redirect to an Inertia page
        if ($request->hasHeader('X-Inertia')) {
            // redirect to the invoices list (Inertia page) with a flash message
            return redirect()->route('invoices.index')->with('success', 'Invoice created successfully.');
        }

        // Fallback for non-Inertia/API consumers
        return response($invoice, 201);
    }

    public function show(Invoice $invoice): Response
    {
        return response($invoice->load('client', 'items'));
    }

    public function downloadPdf(Invoice $invoice, GenerateInvoicePdf $generateInvoicePdf): Response
    {
        $path = $generateInvoicePdf->handle($invoice);

        return response()->download(storage_path('app/'.$path), 'Invoice-'.$invoice->number.'.pdf');
    }

    public function sendEmail(Invoice $invoice, Request $request, SendInvoiceEmail $sendInvoiceEmail): Response
    {
        $request->validate([
            'email' => ['nullable', 'email'],
        ]);

        $result = $sendInvoiceEmail->handle($invoice, $request->input('email'));

        return response($result);
    }

    public function sendWhatsApp(Invoice $invoice, Request $request, GenerateInvoicePdf $generateInvoicePdf, BuildWhatsAppLink $buildWhatsAppLink): Response
    {
        $request->validate([
            'phone' => ['nullable', 'string'],
        ]);

        // Ensure we have a fresh PDF and build a public download URL
        $generateInvoicePdf->handle($invoice);
        $publicUrl = route('invoices.download', $invoice);

        $result = $buildWhatsAppLink->handle($invoice, $request->input('phone'), $publicUrl);

        return response($result);
    }
}
