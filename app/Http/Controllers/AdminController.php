<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Expense;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    /**
     * Day-wise totals for invoices, payments, expenses for a given month (YYYY-MM).
     */
    public function dashboardData(Request $request)
    {
        try {
            $month = $request->query('month');
            $date = $request->query('date'); // optional single day in Y-m-d
        if ($date) {
            try {
                $start = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
                $end = (clone $start)->endOfDay();
            } catch (\Throwable $e) {
                $start = now()->startOfDay();
                $end = (clone $start)->endOfDay();
            }
        } else {
            try {
                $start = $month ? Carbon::createFromFormat('Y-m', $month)->startOfMonth() : now()->startOfMonth();
            } catch (\Throwable $e) {
                $start = now()->startOfMonth();
            }
            $end = (clone $start)->endOfMonth();
        }

        // Build labels for each day
        $dates = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $dates[] = $cursor->toDateString();
            $cursor->addDay();
        }

        // Query aggregates using business date columns when present
        $invoiceDateCol = Schema::hasColumn('invoices', 'invoice_date') ? 'invoice_date' : 'created_at';
        $paymentDateCol = Schema::hasColumn('payments', 'payment_date') ? 'payment_date' : 'created_at';
        $expenseDateCol = Schema::hasColumn('expenses', 'expense_date') ? 'expense_date' : 'created_at';

        $invoiceRows = Invoice::selectRaw('DATE(' . $invoiceDateCol . ') as d, SUM(invoice_value) as s')
            ->whereBetween($invoiceDateCol, [$start, $end])
            ->groupBy('d')->pluck('s', 'd');

        $paymentRows = Payment::selectRaw('DATE(' . $paymentDateCol . ') as d, SUM(amount) as s')
            ->whereBetween($paymentDateCol, [$start, $end])
            ->groupBy('d')->pluck('s', 'd');

        // Prefer amount_in_bdt if present, else amount
        $expenseAmountCol = Schema::hasColumn('expenses', 'amount_in_bdt') ? 'amount_in_bdt' : 'amount';
        $expenseRows = Expense::selectRaw('DATE(' . $expenseDateCol . ') as d, SUM(' . $expenseAmountCol . ') as s')
            ->whereBetween($expenseDateCol, [$start, $end])
            ->groupBy('d')->pluck('s', 'd');

        $invoices = [];
        $payments = [];
        $expenses = [];
        foreach ($dates as $d) {
            $invoices[] = (float) ($invoiceRows[$d] ?? 0);
            $payments[] = (float) ($paymentRows[$d] ?? 0);
            $expenses[] = (float) ($expenseRows[$d] ?? 0);
        }

        // Add debugging info
        $response = [
            'labels' => $dates,
            'invoices' => $invoices,
            'payments' => $payments,
            'expenses' => $expenses,
            'month' => $date ? null : $start->format('Y-m'),
            'date' => $date ? $start->format('Y-m-d') : null,
            'debug' => [
                'total_invoices' => array_sum($invoices),
                'total_payments' => array_sum($payments),
                'total_expenses' => array_sum($expenses),
                'period_start' => $start->toDateString(),
                'period_end' => $end->toDateString(),
                'invoice_date_col' => $invoiceDateCol,
                'payment_date_col' => $paymentDateCol,
                'expense_date_col' => $expenseDateCol,
            ]
        ];

        return response()->json($response);
        
        } catch (\Exception $e) {
            Log::error('Dashboard data error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to load dashboard data',
                'message' => $e->getMessage(),
                'labels' => [],
                'invoices' => [],
                'payments' => [],
                'expenses' => []
            ], 500);
        }
    }
}
