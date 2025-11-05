<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ledger Report Print</title>
    <style>
        @page {
            margin: 15mm 10mm 15mm 10mm;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
            background-color: white;
            color: black;
            font-size: 12px;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .print-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        
        .print-header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        
        .print-header p {
            margin: 3px 0 0;
            font-size: 12px;
        }
        
        .company-info {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .company-info h2 {
            margin: 0;
            font-size: 18px;
        }
        
        .company-info p {
            margin: 3px 0;
            font-size: 12px;
        }
        
        .customer-container {
            margin-bottom: 15px;
            break-after: page;
            page-break-after: always;
            position: relative;
        }
        
        .customer-container:last-child {
            break-after: auto;
            page-break-after: auto;
            margin-bottom: 30px; /* Space for footer */
        }
        
        .customer-details {
            margin-bottom: 15px;
            border: 1px solid #ddd;
            padding: 10px;
            background-color: #f9f9f9;
        }
        
        .detailed-records {
            margin-bottom: 15px;
        }
        
        .customer-details h3,
        .customer-summary h3,
        .detailed-records h3 {
            margin-top: 0;
            margin-bottom: 5px;
            font-size: 14px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
            color: #333;
        }
        
        .print-customer-table,
        .print-summary-table,
        .print-details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            table-layout: fixed;
        }
        
        .print-customer-table th,
        .print-customer-table td {
            padding: 5px;
            text-align: left;
            border: none;
        }
        
        .print-customer-table th {
            width: 120px;
            font-weight: bold;
            color: #555;
        }
        
        .print-summary-table th,
        .print-summary-table td,
        .print-details-table th,
        .print-details-table td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: right;
            font-size: 11px;
            word-wrap: break-word;
        }
        
        .print-summary-table th,
        .print-details-table th {
            background-color: #f2f2f2 !important;
            font-weight: bold;
            text-align: center;
            color: #333;
        }
        
        .print-details-table td:nth-child(1),
        .print-details-table td:nth-child(2),
        .print-details-table td:nth-child(3) {
            text-align: left;
        }
        
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9 !important;
        }
        
        .text-danger {
            color: #dc3545 !important;
        }
        
        .text-success {
            color: #28a745 !important;
        }
        
        .report-footer {
            text-align: center;
            font-size: 9px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 5px;
            margin-top: 20px;
            width: 100%;
        }
        
        @media print {
            html, body {
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
            }
            
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            .customer-container {
                break-after: page;
                page-break-after: always;
                margin-bottom: 0;
                display: block;
                clear: both;
            }
            
            .customer-container:last-child {
                break-after: auto;
                page-break-after: auto;
            }
            
            .print-header {
                margin-bottom: 10px;
            }
            
            .customer-details {
                margin-bottom: 8px;
            }
            
            .print-summary-table,
            .print-details-table {
                margin-bottom: 10px;
                page-break-inside: avoid;
                break-inside: avoid;
            }
            
            .report-footer {
                margin-top: 20px;
            }
            
            table { page-break-inside:auto }
            tr { page-break-inside:avoid; page-break-after:auto }
            thead { display:table-header-group }
            tfoot { display:table-footer-group }
        }
    </style>
</head>
<body>
    <div class="print-header">
        <h1>Ledger Report</h1>
        <p>Period: {{ $request->input('start_date') }} to {{ $request->input('end_date') }}</p>
    </div>
    

    @if($ledgerData->count() > 0)
        @foreach($ledgerData as $customerId => $data)
            <div class="customer-container">
                <div class="customer-details">
                    <h3>Customer Information</h3>
                    <table class="print-customer-table">
                        <tr>
                            <th>Name:</th>
                            <td>{{ $data['customer']->name }}</td>
                        </tr>
                        <tr>
                            <th>Mobile:</th>
                            <td>{{ $data['customer']->mobile }}</td>
                        </tr>
                        <tr>
                            <th>Address:</th>
                            <td>{{ $data['customer']->address }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $data['customer']->email }}</td>
                        </tr>
                    </table>
                </div>
                
                <div class="customer-summary">
                    <h3>Customer Summary</h3>
                    <table class="print-summary-table">
                        <thead>
                            <tr>
                                <th>Invoice Amount</th>
                                <th>Payment Amount</th>
                                <th>Delivery Amount</th>
                                <th>Cancellation Amount</th>
                                <th>Refund Amount</th>
                                <th>Balance/Due</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ number_format($data['invoice_amount'], 2) }}</td>
                                <td>{{ number_format($data['payment_amount'], 2) }}</td>
                                <td>{{ number_format($data['delivery_amount'], 2) }}</td>
                                <td>{{ number_format($data['cancellation_amount'], 2) }}</td>
                                <td>{{ number_format($data['refund_amount'], 2) }}</td>
                                <td class="{{ $data['balance'] > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format(abs($data['balance']), 2) }}
                                    {{ $data['balance'] > 0 ? '(Due)' : '(Advance)' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="detailed-records">
                    <h3>Detailed Records</h3>
                    <table class="print-details-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Details</th>
                                <th>Debit</th>
                                <th>Credit</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $runningBalance = 0;
                            @endphp
                            
                            @foreach($data['records'] as $record)
                                @php
                                    $isDebit = in_array($record['type'], ['invoice', 'delivery']);
                                    $isCredit = in_array($record['type'], ['payment', 'cancellation', 'refund']);
                                    
                                    if ($isDebit) {
                                        $runningBalance += $record['amount'];
                                    } else if ($isCredit) {
                                        $runningBalance -= $record['amount'];
                                    }
                                    
                                    $formattedDate = $record['date'] instanceof \Carbon\Carbon 
                                        ? $record['date']->format('Y-m-d') 
                                        : date('Y-m-d', strtotime($record['date']));
                                @endphp
                                
                                <tr>
                                    <td>{{ $formattedDate }}</td>
                                    <td>{{ ucfirst($record['type']) }}</td>
                                    <td>{{ $record['details'] }}</td>
                                    <td>{{ $isDebit ? number_format($record['amount'], 2) : '' }}</td>
                                    <td>{{ $isCredit ? number_format($record['amount'], 2) : '' }}</td>
                                    <td>{{ number_format($runningBalance, 2) }}</td>
                                </tr>
                            @endforeach
                            
                            <tr class="total-row">
                                <td colspan="3" style="text-align: right;"><strong>Total</strong></td>
                                <td>{{ number_format($data['invoice_amount'] + $data['delivery_amount'], 2) }}</td>
                                <td>{{ number_format($data['payment_amount'] + $data['cancellation_amount'] + $data['refund_amount'], 2) }}</td>
                                <td class="{{ $runningBalance > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format(abs($runningBalance), 2) }}
                                    {{ $runningBalance > 0 ? '(Due)' : '(Advance)' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
        
        @if($ledgerData->count() > 1)
            <div class="customer-summary">
                <h3>Grand Total</h3>
                <table class="print-summary-table">
                    <thead>
                        <tr>
                            <th>Invoice Amount</th>
                            <th>Payment Amount</th>
                            <th>Delivery Amount</th>
                            <th>Cancellation Amount</th>
                            <th>Refund Amount</th>
                            <th>Balance/Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ number_format($totals['invoice'], 2) }}</td>
                            <td>{{ number_format($totals['payment'], 2) }}</td>
                            <td>{{ number_format($totals['delivery'], 2) }}</td>
                            <td>{{ number_format($totals['cancellation'], 2) }}</td>
                            <td>{{ number_format($totals['refund'], 2) }}</td>
                            <td class="{{ $totals['balance'] > 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format(abs($totals['balance']), 2) }}
                                {{ $totals['balance'] > 0 ? '(Due)' : '(Advance)' }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endif
    @else
        <div class="no-data">
            <p>No data available for the selected filters.</p>
        </div>
    @endif
    
    <div class="report-footer">
        <p>Generated: {{ now()->format('Y-m-d H:i:s') }} | Computer-generated report, no signature required</p>
    </div>
    
    <script>
        // Auto-print when page loads with a slight delay to ensure styles are fully loaded
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
