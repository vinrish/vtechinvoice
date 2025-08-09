<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Invoice</title></head>
<body>
<p>Hello {{ $invoice->client->name }},</p>
<p>Please find attached your invoice <strong>{{ $invoice->number }}</strong> totaling <strong>{{ $invoice->currency }} {{ number_format((float)$invoice->total, 2) }}</strong>.</p>
<p>You can also view it online here: <a href="{{ $viewUrl ?? '#' }}">View Invoice</a></p>
<p>Thank you.</p>
</body>
</html>
