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
use App\Models\Cancellation;
use App\Models\Staff;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function dashboard()
    {
        // Default period: current month
        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        // Resolve date columns if present
        $invoiceDateCol = Schema::hasColumn('invoices', 'invoice_date') ? 'invoice_date' : 'created_at';
        $cancellationDateCol = Schema::hasColumn('cancellations', 'cancellation_date') ? 'cancellation_date' : 'created_at';

        // Aggregates by staff for current month
        $invoiceAgg = Invoice::selectRaw('staff_id, COALESCE(SUM(invoice_value),0) as total_invoice')
            ->whereBetween($invoiceDateCol, [$start, $end])
            ->groupBy('staff_id')
            ->pluck('total_invoice', 'staff_id');

        $cancelAgg = Cancellation::selectRaw('staff_id, COALESCE(SUM(cancellation_value),0) as total_cancel')
            ->whereBetween($cancellationDateCol, [$start, $end])
            ->groupBy('staff_id')
            ->pluck('total_cancel', 'staff_id');

        // Build combined list for top 10 staff
        $staffIds = collect($invoiceAgg->keys())->merge($cancelAgg->keys())->unique()->values();
        $staffMap = Staff::whereIn('id', $staffIds)->get(['id','name'])->keyBy('id');
        $staffSales = $staffIds->map(function($sid) use ($invoiceAgg, $cancelAgg, $staffMap){
            $inv = (float) ($invoiceAgg[$sid] ?? 0);
            $can = (float) ($cancelAgg[$sid] ?? 0);
            return [
                'staff_id' => $sid,
                'staff_name' => optional($staffMap->get($sid))->name ?? 'Unknown',
                'invoice_total' => $inv,
                'cancel_total' => $can,
                'sale_total' => $inv - $can,
            ];
        })->sortByDesc('sale_total')->values()->take(10);

        return view('admin.dashboard', compact('staffSales'));
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

    /**
     * Staff-wise sales report (invoice - cancellation) with optional period filters.
     */
    public function staffSales(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $staffId = $request->query('staff_id');
        try {
            $start = $from ? \Carbon\Carbon::createFromFormat('Y-m-d', $from)->startOfDay() : now()->startOfMonth();
        } catch (\Throwable $e) {
            $start = now()->startOfMonth();
        }
        try {
            $end = $to ? \Carbon\Carbon::createFromFormat('Y-m-d', $to)->endOfDay() : now()->endOfMonth();
        } catch (\Throwable $e) {
            $end = now()->endOfMonth();
        }

        $invoiceDateCol = Schema::hasColumn('invoices', 'invoice_date') ? 'invoice_date' : 'created_at';
        $cancellationDateCol = Schema::hasColumn('cancellations', 'cancellation_date') ? 'cancellation_date' : 'created_at';

        $invoiceQuery = Invoice::selectRaw('staff_id, COALESCE(SUM(invoice_value),0) as total_invoice')
            ->whereBetween($invoiceDateCol, [$start, $end])
            ->groupBy('staff_id');
        if ($staffId !== null && $staffId !== '') {
            $invoiceQuery->where('staff_id', $staffId);
        }
        $invoiceAgg = $invoiceQuery->pluck('total_invoice', 'staff_id');

        $cancelQuery = Cancellation::selectRaw('staff_id, COALESCE(SUM(cancellation_value),0) as total_cancel')
            ->whereBetween($cancellationDateCol, [$start, $end])
            ->groupBy('staff_id');
        if ($staffId !== null && $staffId !== '') {
            $cancelQuery->where('staff_id', $staffId);
        }
        $cancelAgg = $cancelQuery->pluck('total_cancel', 'staff_id');

        $staffIds = collect($invoiceAgg->keys())->merge($cancelAgg->keys());
        if ($staffId !== null && $staffId !== '') {
            $staffIds = $staffIds->merge([(int) $staffId]);
        }
        $staffIds = $staffIds->unique()->values();
        $staffMap = $staffIds->isNotEmpty()
            ? Staff::whereIn('id', $staffIds)->get(['id','name'])->keyBy('id')
            : collect();
        $rows = $staffIds->map(function($sid) use ($invoiceAgg, $cancelAgg, $staffMap){
            $inv = (float) ($invoiceAgg[$sid] ?? 0);
            $can = (float) ($cancelAgg[$sid] ?? 0);
            return (object) [
                'staff_id' => $sid,
                'staff_name' => optional($staffMap->get($sid))->name ?? 'Unknown',
                'invoice_total' => $inv,
                'cancel_total' => $can,
                'sale_total' => $inv - $can,
            ];
        })->sortByDesc('sale_total')->values();

        $staffs = Staff::orderBy('name')->get(['id','name']);

        return view('admin.reports.staff_sales', [
            'rows' => $rows,
            'start' => $start->toDateString(),
            'end' => $end->toDateString(),
            'staffs' => $staffs,
            'selectedStaffId' => $staffId,
        ]);
    }
}
