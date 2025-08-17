<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\Account;
use App\Models\Expense;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BudgetController extends Controller
{
    public function index()
    {
        $budgets = Budget::withCount('items')->latest('month')->paginate(10);
        return view('admin.budgets.index', compact('budgets'));
    }

    public function create()
    {
        $accounts = Account::with('currency')->where('is_active', 1)->get();
        $currencies = Currency::query()->get(['id','code','conversion_rate','name']);
        return view('admin.budgets.create', compact('accounts','currencies'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'month' => 'required|date_format:Y-m',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.account_id' => 'required|exists:accounts,id',
            'items.*.currency_id' => 'required|exists:currencies,id',
            'items.*.amount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $budget = Budget::create([
                'month' => $data['month'] . '-01', // store as date
                'remarks' => $data['remarks'] ?? null,
                'status' => 'planned',
            ]);

            foreach ($data['items'] as $item) {
                $account = Account::findOrFail($item['account_id']);
                $currency = Currency::findOrFail($item['currency_id']);
                $code = strtoupper($currency->code ?? 'BDT');
                $rate = (float) ($currency->conversion_rate ?? 1);
                if ($rate <= 0) { $rate = 1; }
                $native = (float) $item['amount'];
                $bdt = $code === 'BDT' ? $native : $native * $rate;

                BudgetItem::create([
                    'budget_id' => $budget->id,
                    'account_id' => $account->id,
                    'currency_id' => $currency->id,
                    'amount' => $native,
                    'amount_in_bdt' => $bdt,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.budgets.show', $budget->id)->with('success', 'Budget created');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(Budget $budget)
    {
        $budget->load(['items.account','items.currency']);
        return view('admin.budgets.show', compact('budget'));
    }

    // Convert budget items to expenses (one per item)
    public function convert(Budget $budget)
    {
        if ($budget->status === 'converted') {
            return back()->with('success', 'Already converted');
        }

        DB::beginTransaction();
        try {
            $budget->load(['items.account.currency']);
            foreach ($budget->items as $item) {
                // Create expense from budget item
                Expense::create([
                    'account_id' => $item->account_id,
                    'amount' => $item->amount, // native
                    'amount_in_bdt' => $item->amount_in_bdt, // bdt
                    'remarks' => 'Budget ' . $budget->month->format('Y-m') . ' - ' . $item->account->name,
                    'status' => 'pending',
                ]);
            }

            $budget->update(['status' => 'converted']);
            DB::commit();
            return back()->with('success', 'Budget converted to expenses');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
