<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        // Query aggregates
        $invoiceRows = Invoice::selectRaw('DATE(created_at) as d, SUM(invoice_value) as s')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('d')->pluck('s', 'd');

        $paymentRows = Payment::selectRaw('DATE(created_at) as d, SUM(amount) as s')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('d')->pluck('s', 'd');

        // Prefer amount_in_bdt if present, else amount
        $expenseColumn = \Schema::hasColumn('expenses', 'amount_in_bdt') ? 'amount_in_bdt' : 'amount';
        $expenseRows = Expense::selectRaw('DATE(created_at) as d, SUM(' . $expenseColumn . ') as s')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('d')->pluck('s', 'd');

        $invoices = [];
        $payments = [];
        $expenses = [];
        foreach ($dates as $d) {
            $invoices[] = (float) ($invoiceRows[$d] ?? 0);
            $payments[] = (float) ($paymentRows[$d] ?? 0);
            $expenses[] = (float) ($expenseRows[$d] ?? 0);
        }

        return response()->json([
            'labels' => $dates,
            'invoices' => $invoices,
            'payments' => $payments,
            'expenses' => $expenses,
            'month' => $date ? null : $start->format('Y-m'),
            'date' => $date ? $start->format('Y-m-d') : null,
        ]);
    }
}
