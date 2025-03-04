<!-- resources/views/invoice.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->id }}</title>
    <link rel="stylesheet" href="{{ public_path('css/pdf.css') }}">
</head>
<body>
    <div class="box">
    <div class="header">
        {{-- <img src="images/logo1.png" alt="logo" class="logo"> --}}
        <p class="inv">DINULA FASHION</p>
        <p class="sub">The art of the Kids Garments...</p>
        <p class="sub1">BORALASGAMUWA</p>
    </div>
    <div class="container">
        <div class="left">
            <p><strong>Invoice No:</strong> {{ $invoice->id }}</p>
            <p><strong>Date:</strong> {{ $invoice->created_at->format('F j, Y') }}</p>
            <p><strong>Customer Name:</strong> <strong>{{ $invoice->customer->title }}{{ $invoice->customer->name }}</strong></p>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th style="width:10%; text-align: center; font-size: 11px;">No</th>
                <th style="width:40%; text-align: left; font-size: 11px;">Description</th>
                <th style="width:20%; text-align: right; font-size: 11px;">Unit Price(LKR)</th>
                <th style="width:10%; text-align: center; font-size: 11px;">Qty</th>
                <th style="width:20%; text-align: right; font-size: 11px;">Total(LKR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoiceItems as $index => $item)<!-- Check if it's an item -->
                    <tr>
                        <td style="width:10%; text-align: center; font-size: 11px;">{{ $index + 1 }}</td>
                        <td style="width:40%; text-align: left; font-size: 11px;">
                            {{ $item->item->name ?? 'N/A' }}
                        </td>
                        <td style="width:20%; text-align: right; font-size: 11px;">{{ number_format($item->item->cost, 2) }}</td>
                        <td style="width:10%; text-align: center; font-size: 11px;">
                            {{ $item->quantity }}
                        </td>
                        <td style="width:20%; text-align: right; font-size: 11px;">{{ number_format($item->total_amount, 2) }}</td>
                    </tr>
                    @foreach($item->item->itemParts as $part) <!-- Loop through item parts -->
                        <tr>
                            <td style="width:10%; text-align: center; font-size: 11px;"></td>
                            <td style="width:40%; text-align: right; font-size: 11px;">{{ $part->description ?? 'N/A' }} - {{ $part->price ?? 'N/A' }}</td>
                            <td style="width:20%; text-align: center; font-size: 11px;"></td>
                            <td style="width:10%; text-align: center; font-size: 11px;"></td>
                            <td style="width:20%; text-align: center; font-size: 11px;"></td>
                        </tr>
                    @endforeach
            @endforeach
            @if($showGrandTotal)
                <tr>
                    <td colspan="4" style="text-align: right; font-weight: bold; font-size: 12px;">Grand Total:</td>
                    <td colspan="1" style="text-align: right; font-weight: bold; font-size: 12px;">{{ number_format($invoice->amount, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: right; font-weight: bold; font-size: 12px;">Paid Amount:</td>
                    <td colspan="1" style="text-align: right; font-weight: bold; font-size: 12px;">{{ number_format($totalPaid, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: right; font-weight: bold; font-size: 12px;">Balance:</td>
                    <td colspan="1" style="text-align: right; font-weight: bold; font-size: 12px;">{{ number_format($invoice->credit_balance, 2) }}</td>
                </tr>
            @endif
        </tbody>
    </table>
    <div class="footer">
        <div class="leftf">
            <p>DELIVERY BY</p>
        </div>
        <div class="rightf">
            <p>RECIEVED BY</p>
        </div>
    </div>
    </div>
</body>
</html>
