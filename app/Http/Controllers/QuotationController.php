<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Quotation\ConvertToInvoice as ConvertQuotationToInvoice;
use App\Actions\Quotation\CreateQuotation;
use App\Models\Quotation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

final class QuotationController extends Controller
{
    public function index(): JsonResponse
    {
        $quotes = Quotation::with('client')->latest()->paginate(20);

        return Inertia::render('quotations/Index', [
            'quotations' => $quotes,
        ])->toResponse(request());
    }

    public function store(Request $request, CreateQuotation $createQuotation): Response
    {
        $data = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'valid_until' => ['nullable', 'date'],
            'currency' => ['nullable', 'string', 'size:3'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:1000'],
            'items.*.quantity' => ['required', 'numeric', 'min:0'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'tax_rate' => ['nullable', 'numeric', 'min:0'],
        ]);

        $quote = $createQuotation->handle($data);

        return response($quote, 201);
    }

    public function show(Quotation $quotation): Response
    {
        return response($quotation->load('client', 'items'));
    }

    public function convertToInvoice(Quotation $quotation, Request $request, ConvertQuotationToInvoice $convertQuotationToInvoice): Response
    {
        $invoice = $convertQuotationToInvoice->handle($quotation, $request->input('due_at'));

        return response($invoice, 201);
    }
}
