<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Delivery;
use App\Models\Cancellation;
use App\Models\Refund;
use App\Models\Bank;
use Carbon\Carbon;

class LedgerReportController extends Controller
{
    /**
     * Display the ledger report page with filters
     */
    public function index(Request $request)
    {
        $customers = Customer::orderBy('name')->get();
        $banks = Bank::orderBy('name')->get();
        
        // Initialize with empty data
        $ledgerData = collect();
        $totals = [
            'invoice' => 0,
            'payment' => 0,
            'delivery' => 0,
            'cancellation' => 0,
            'refund' => 0,
            'balance' => 0
        ];
        
        // Process filters if form submitted
        if ($request->has('filter')) {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $customerId = $request->input('customer_id');
            $bankId = $request->input('bank_id');
            $recordType = $request->input('record_type');
            
            // Build query for each record type based on filters
            $invoiceQuery = Invoice::query();
            $paymentQuery = Payment::query();
            $deliveryQuery = Delivery::query();
            $cancellationQuery = Cancellation::query();
            $refundQuery = Refund::query();
            
            // Apply date filters if provided
            if ($startDate && $endDate) {
                $invoiceQuery->whereBetween('created_at', [$startDate, $endDate]);
                $paymentQuery->whereBetween('payment_date', [$startDate, $endDate]);
                $deliveryQuery->whereBetween('delivery_date', [$startDate, $endDate]);
                $cancellationQuery->whereBetween('cancellation_date', [$startDate, $endDate]);
                $refundQuery->whereBetween('refund_date', [$startDate, $endDate]);
            }
            
            // Apply customer filter if provided
            if ($customerId) {
                $invoiceQuery->where('customer_id', $customerId);
                $paymentQuery->where('customer_id', $customerId);
                $deliveryQuery->where('customer_id', $customerId);
                $cancellationQuery->where('customer_id', $customerId);
                $refundQuery->where('customer_id', $customerId);
            }
            
            // Apply bank filter if provided
            if ($bankId) {
                $paymentQuery->where('bank_id', $bankId);
                $refundQuery->where('bank_id', $bankId);
            }
            
            // Get data based on record type filter
            $invoices = ($recordType == 'all' || $recordType == 'invoice') ? $invoiceQuery->with('customer')->get() : collect();
            $payments = ($recordType == 'all' || $recordType == 'payment') ? $paymentQuery->with('customer', 'bank')->get() : collect();
            $deliveries = ($recordType == 'all' || $recordType == 'delivery') ? $deliveryQuery->with('customer')->get() : collect();
            $cancellations = ($recordType == 'all' || $recordType == 'cancellation') ? $cancellationQuery->with('customer')->get() : collect();
            $refunds = ($recordType == 'all' || $recordType == 'refund') ? $refundQuery->with('customer', 'bank')->get() : collect();
            
            // Group data by customer
            $customerData = [];
            
            // Process invoices
            foreach ($invoices as $invoice) {
                $customerId = $invoice->customer_id;
                if (!isset($customerData[$customerId])) {
                    $customerData[$customerId] = [
                        'customer' => $invoice->customer,
                        'invoice_amount' => 0,
                        'payment_amount' => 0,
                        'delivery_amount' => 0,
                        'cancellation_amount' => 0,
                        'refund_amount' => 0,
                        'balance' => 0,
                        'records' => []
                    ];
                }
                
                $customerData[$customerId]['invoice_amount'] += $invoice->invoice_value;
                $customerData[$customerId]['records'][] = [
                    'type' => 'invoice',
                    'date' => $invoice->created_at,
                    'amount' => $invoice->invoice_value,
                    'details' => 'Invoice #' . $invoice->invoice_id,
                    'record' => $invoice
                ];
                
                $totals['invoice'] += $invoice->invoice_value;
            }
            
            // Process payments
            foreach ($payments as $payment) {
                $customerId = $payment->customer_id;
                if (!isset($customerData[$customerId])) {
                    $customerData[$customerId] = [
                        'customer' => $payment->customer,
                        'invoice_amount' => 0,
                        'payment_amount' => 0,
                        'delivery_amount' => 0,
                        'cancellation_amount' => 0,
                        'refund_amount' => 0,
                        'balance' => 0,
                        'records' => []
                    ];
                }
                
                $customerData[$customerId]['payment_amount'] += $payment->amount;
                $customerData[$customerId]['records'][] = [
                    'type' => 'payment',
                    'date' => $payment->payment_date,
                    'amount' => $payment->amount,
                    'details' => 'Payment to ' . ($payment->bank ? $payment->bank->name : 'Unknown Bank'),
                    'record' => $payment
                ];
                
                $totals['payment'] += $payment->amount;
            }
            
            // Process deliveries
            foreach ($deliveries as $delivery) {
                $customerId = $delivery->customer_id;
                if (!isset($customerData[$customerId])) {
                    $customerData[$customerId] = [
                        'customer' => $delivery->customer,
                        'invoice_amount' => 0,
                        'payment_amount' => 0,
                        'delivery_amount' => 0,
                        'cancellation_amount' => 0,
                        'refund_amount' => 0,
                        'balance' => 0,
                        'records' => []
                    ];
                }
                
                $customerData[$customerId]['delivery_amount'] += $delivery->delivery_value;
                $customerData[$customerId]['records'][] = [
                    'type' => 'delivery',
                    'date' => $delivery->delivery_date,
                    'amount' => $delivery->delivery_value,
                    'details' => 'Delivery #' . $delivery->shipment_no,
                    'record' => $delivery
                ];
                
                $totals['delivery'] += $delivery->delivery_value;
            }
            
            // Process cancellations
            foreach ($cancellations as $cancellation) {
                $customerId = $cancellation->customer_id;
                if (!isset($customerData[$customerId])) {
                    $customerData[$customerId] = [
                        'customer' => $cancellation->customer,
                        'invoice_amount' => 0,
                        'payment_amount' => 0,
                        'delivery_amount' => 0,
                        'cancellation_amount' => 0,
                        'refund_amount' => 0,
                        'balance' => 0,
                        'records' => []
                    ];
                }
                
                $customerData[$customerId]['cancellation_amount'] += $cancellation->cancellation_value;
                $customerData[$customerId]['records'][] = [
                    'type' => 'cancellation',
                    'date' => $cancellation->cancellation_date,
                    'amount' => $cancellation->cancellation_value,
                    'details' => 'Cancellation',
                    'record' => $cancellation
                ];
                
                $totals['cancellation'] += $cancellation->cancellation_value;
            }
            
            // Process refunds
            foreach ($refunds as $refund) {
                $customerId = $refund->customer_id;
                if (!isset($customerData[$customerId])) {
                    $customerData[$customerId] = [
                        'customer' => $refund->customer,
                        'invoice_amount' => 0,
                        'payment_amount' => 0,
                        'delivery_amount' => 0,
                        'cancellation_amount' => 0,
                        'refund_amount' => 0,
                        'balance' => 0,
                        'records' => []
                    ];
                }
                
                $customerData[$customerId]['refund_amount'] += $refund->refund_amount;
                $customerData[$customerId]['records'][] = [
                    'type' => 'refund',
                    'date' => $refund->refund_date,
                    'amount' => $refund->refund_amount,
                    'details' => 'Refund from ' . ($refund->bank ? $refund->bank->name : 'Unknown Bank'),
                    'record' => $refund
                ];
                
                $totals['refund'] += $refund->refund_amount;
            }
            
            // Calculate balance for each customer
            foreach ($customerData as $customerId => &$data) {
                // Invoice and delivery are positive (debit)
                // Payment, cancellation, and refund are negative (credit)
                $data['balance'] = 
                    $data['invoice_amount'] - 
                    ($data['payment_amount'] - 
                    ($data['refund_amount'] + $data['delivery_amount']) + 
                    $data['cancellation_amount']);
                
                // Sort records by date
                usort($data['records'], function($a, $b) {
                    return strtotime($a['date']) - strtotime($b['date']);
                });
            }
            
            // Calculate total balance
            $totals['balance'] = 
                $totals['invoice'] - 
                ($totals['payment'] - 
                ($totals['refund'] + $totals['delivery']) + 
                $totals['cancellation']);
            
            $ledgerData = collect($customerData);
        }
        
        return view('admin.reports.ledger', compact(
            'customers', 
            'banks', 
            'ledgerData', 
            'totals',
            'request'
        ));
    }
    
    /**
     * Display the printable ledger report
     */
    public function printView(Request $request)
    {
        // Initialize with empty data
        $ledgerData = collect();
        $totals = [
            'invoice' => 0,
            'payment' => 0,
            'delivery' => 0,
            'cancellation' => 0,
            'refund' => 0,
            'balance' => 0
        ];
        
        // Process filters
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $customerId = $request->input('customer_id');
        $bankId = $request->input('bank_id');
        $recordType = $request->input('record_type', 'all');
        
        // Build query for each record type based on filters
        $invoiceQuery = Invoice::query();
        $paymentQuery = Payment::query();
        $deliveryQuery = Delivery::query();
        $cancellationQuery = Cancellation::query();
        $refundQuery = Refund::query();
        
        // Apply date filters if provided
        if ($startDate && $endDate) {
            $invoiceQuery->whereBetween('created_at', [$startDate, $endDate]);
            $paymentQuery->whereBetween('payment_date', [$startDate, $endDate]);
            $deliveryQuery->whereBetween('delivery_date', [$startDate, $endDate]);
            $cancellationQuery->whereBetween('cancellation_date', [$startDate, $endDate]);
            $refundQuery->whereBetween('refund_date', [$startDate, $endDate]);
        }
        
        // Apply customer filter if provided
        if ($customerId) {
            $invoiceQuery->where('customer_id', $customerId);
            $paymentQuery->where('customer_id', $customerId);
            $deliveryQuery->where('customer_id', $customerId);
            $cancellationQuery->where('customer_id', $customerId);
            $refundQuery->where('customer_id', $customerId);
        }
        
        // Apply bank filter if provided
        if ($bankId) {
            $paymentQuery->where('bank_id', $bankId);
            $refundQuery->where('bank_id', $bankId);
        }
        
        // Get data based on record type filter
        $invoices = ($recordType == 'all' || $recordType == 'invoice') ? $invoiceQuery->with('customer')->get() : collect();
        $payments = ($recordType == 'all' || $recordType == 'payment') ? $paymentQuery->with('customer', 'bank')->get() : collect();
        $deliveries = ($recordType == 'all' || $recordType == 'delivery') ? $deliveryQuery->with('customer')->get() : collect();
        $cancellations = ($recordType == 'all' || $recordType == 'cancellation') ? $cancellationQuery->with('customer')->get() : collect();
        $refunds = ($recordType == 'all' || $recordType == 'refund') ? $refundQuery->with('customer', 'bank')->get() : collect();
        
        // Group data by customer
        $customerData = [];
        
        // Process invoices
        foreach ($invoices as $invoice) {
            $customerId = $invoice->customer_id;
            if (!isset($customerData[$customerId])) {
                $customerData[$customerId] = [
                    'customer' => $invoice->customer,
                    'invoice_amount' => 0,
                    'payment_amount' => 0,
                    'delivery_amount' => 0,
                    'cancellation_amount' => 0,
                    'refund_amount' => 0,
                    'balance' => 0,
                    'records' => []
                ];
            }
            
            $customerData[$customerId]['invoice_amount'] += $invoice->invoice_value;
            $customerData[$customerId]['records'][] = [
                'type' => 'invoice',
                'date' => $invoice->created_at,
                'amount' => $invoice->invoice_value,
                'details' => 'Invoice #' . $invoice->invoice_id,
                'record' => $invoice
            ];
            
            $totals['invoice'] += $invoice->invoice_value;
        }
        
        // Process payments
        foreach ($payments as $payment) {
            $customerId = $payment->customer_id;
            if (!isset($customerData[$customerId])) {
                $customerData[$customerId] = [
                    'customer' => $payment->customer,
                    'invoice_amount' => 0,
                    'payment_amount' => 0,
                    'delivery_amount' => 0,
                    'cancellation_amount' => 0,
                    'refund_amount' => 0,
                    'balance' => 0,
                    'records' => []
                ];
            }
            
            $customerData[$customerId]['payment_amount'] += $payment->amount;
            $customerData[$customerId]['records'][] = [
                'type' => 'payment',
                'date' => $payment->payment_date,
                'amount' => $payment->amount,
                'details' => 'Payment to ' . ($payment->bank ? $payment->bank->name : 'Unknown Bank'),
                'record' => $payment
            ];
            
            $totals['payment'] += $payment->amount;
        }
        
        // Process deliveries
        foreach ($deliveries as $delivery) {
            $customerId = $delivery->customer_id;
            if (!isset($customerData[$customerId])) {
                $customerData[$customerId] = [
                    'customer' => $delivery->customer,
                    'invoice_amount' => 0,
                    'payment_amount' => 0,
                    'delivery_amount' => 0,
                    'cancellation_amount' => 0,
                    'refund_amount' => 0,
                    'balance' => 0,
                    'records' => []
                ];
            }
            
            $customerData[$customerId]['delivery_amount'] += $delivery->delivery_value;
            $customerData[$customerId]['records'][] = [
                'type' => 'delivery',
                'date' => $delivery->delivery_date,
                'amount' => $delivery->delivery_value,
                'details' => 'Delivery #' . $delivery->shipment_no,
                'record' => $delivery
            ];
            
            $totals['delivery'] += $delivery->delivery_value;
        }
        
        // Process cancellations
        foreach ($cancellations as $cancellation) {
            $customerId = $cancellation->customer_id;
            if (!isset($customerData[$customerId])) {
                $customerData[$customerId] = [
                    'customer' => $cancellation->customer,
                    'invoice_amount' => 0,
                    'payment_amount' => 0,
                    'delivery_amount' => 0,
                    'cancellation_amount' => 0,
                    'refund_amount' => 0,
                    'balance' => 0,
                    'records' => []
                ];
            }
            
            $customerData[$customerId]['cancellation_amount'] += $cancellation->cancellation_value;
            $customerData[$customerId]['records'][] = [
                'type' => 'cancellation',
                'date' => $cancellation->cancellation_date,
                'amount' => $cancellation->cancellation_value,
                'details' => 'Cancellation',
                'record' => $cancellation
            ];
            
            $totals['cancellation'] += $cancellation->cancellation_value;
        }
        
        // Process refunds
        foreach ($refunds as $refund) {
            $customerId = $refund->customer_id;
            if (!isset($customerData[$customerId])) {
                $customerData[$customerId] = [
                    'customer' => $refund->customer,
                    'invoice_amount' => 0,
                    'payment_amount' => 0,
                    'delivery_amount' => 0,
                    'cancellation_amount' => 0,
                    'refund_amount' => 0,
                    'balance' => 0,
                    'records' => []
                ];
            }
            
            $customerData[$customerId]['refund_amount'] += $refund->refund_amount;
            $customerData[$customerId]['records'][] = [
                'type' => 'refund',
                'date' => $refund->refund_date,
                'amount' => $refund->refund_amount,
                'details' => 'Refund from ' . ($refund->bank ? $refund->bank->name : 'Unknown Bank'),
                'record' => $refund
            ];
            
            $totals['refund'] += $refund->refund_amount;
        }
        
        // Calculate balance for each customer
        foreach ($customerData as $customerId => &$data) {
            // Invoice and delivery are positive (debit)
            // Payment, cancellation, and refund are negative (credit)
            $data['balance'] = 
                $data['invoice_amount'] - 
                ($data['payment_amount'] - 
                ($data['refund_amount'] + $data['delivery_amount']) + 
                $data['cancellation_amount']);
            
            // Sort records by date
            usort($data['records'], function($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });
        }
        
        // Calculate total balance
        $totals['balance'] = 
            $totals['invoice'] - 
            ($totals['payment'] - 
            ($totals['refund'] + $totals['delivery']) + 
            $totals['cancellation']);
        
        $ledgerData = collect($customerData);
        
        return view('admin.reports.ledger_print', compact(
            'ledgerData', 
            'totals',
            'request'
        ));
    }
}
