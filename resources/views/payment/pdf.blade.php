<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Commission Payment Details</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 5px;
        }
        .header h2 {
            margin-bottom: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th {
            background-color: #f2f2f2;
            text-align: left;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .no-data {
            text-align: center;
            padding: 20px;
        }
    </style>
</head>
<body>
<div class="header">
    <h2>Commission Payment Details</h2>
    <p>Transaction ID: {{ $id }}</p>
</div>

<table>
    <thead>
    <tr>
        <th>Payment Date</th>
        <th>Amount</th>
        <th>Payment Method</th>
        <th>Notes</th>
    </tr>
    </thead>
    <tbody>
    @if($commissionPayments->isNotEmpty())
        @foreach($commissionPayments as $payment)
            <tr>
                <td>{{ $payment->paid_on }}</td>
                <td>PHP {{ number_format($payment->amount, 2) }}</td>
                <td>{{ $payment->method }}</td>
                <td>{{ $payment->note }}</td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="4" class="no-data">No commission payments found</td>
        </tr>
    @endif
    </tbody>
</table>
</body>
</html>