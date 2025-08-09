<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->number }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
        .header { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .client, .company { width: 48%; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f3f4f6; }
        .totals { margin-top: 10px; width: 40%; float: right; }
        .totals td { border: none; }
    </style>
</head>
<body>
<div class="header">
    <div class="company">
        <h2>Invoice</h2>
        <div>Number: {{ $invoice->number }}</div>
        <div>Issued: {{ optional($invoice->issued_at)->format('Y-m-d') }}</div>
        <div>Due: {{ optional($invoice->due_at)->format('Y-m-d') }}</div>
    </div>
    <div class="client">
        <h3>Bill To</h3>
        <div>{{ $invoice->client->name }}</div>
        @if($invoice->client->email)<div>{{ $invoice->client->email }}</div>@endif
        @if($invoice->client->phone)<div>{{ $invoice->client->phone }}</div>@endif
        @if($invoice->client->address)<div>{{ $invoice->client->address }}</div>@endif
    </div>
</div>

<table>
    <thead>
    <tr>
        <th>Description</th>
        <th style="width: 80px;">Qty</th>
        <th style="width: 120px;">Unit Price</th>
        <th style="width: 120px;">Total</th>
    </tr>
    </thead>
    <tbody>
    @foreach($invoice->items as $item)
        <tr>
            <td>{{ $item->description }}</td>
            <td>{{ number_format((float)$item->quantity, 2) }}</td>
            <td>{{ $invoice->currency }} {{ number_format((float)$item->unit_price, 2) }}</td>
            <td>{{ $invoice->currency }} {{ number_format((float)$item->total, 2) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<table class="totals">
    <tr><td>Subtotal:</td><td>{{ $invoice->currency }} {{ number_format((float)$invoice->subtotal, 2) }}</td></tr>
    <tr><td>Tax:</td><td>{{ $invoice->currency }} {{ number_format((float)$invoice->tax, 2) }}</td></tr>
    <tr><td><strong>Grand Total:</strong></td><td><strong>{{ $invoice->currency }} {{ number_format((float)$invoice->total, 2) }}</strong></td></tr>
</table>

@if($invoice->notes)
    <div style="clear: both; margin-top: 40px;">
        <strong>Notes</strong>
        <div>{{ $invoice->notes }}</div>
    </div>
@endif
</body>
</html>
