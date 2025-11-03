<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankDailyBalance;
use App\Models\Currency;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banks = Bank::latest()->paginate(10);
        return view('admin.banks.index', compact('banks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.banks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'required|string|max:255|unique:banks',
            'branch' => 'nullable|string|max:255',
            'currency_id' => 'required|exists:currencies,id',
            'initial_balance' => 'required|numeric',
            'amount_in_bdt' => 'nullable|numeric',
        ]);
        
        $currency = Currency::find($request->currency_id);
        $amountInBDT = $request->amount_in_bdt ?? $request->initial_balance;
        
        // If currency is not BDT, calculate the BDT amount
        if ($currency && $currency->code !== 'BDT' && !$request->amount_in_bdt) {
            $amountInBDT = $request->initial_balance * $currency->conversion_rate;
        }
        
        $bank = Bank::create([
            'name' => $request->name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'branch' => $request->branch,
            'currency_id' => $request->currency_id,
            'initial_balance' => $request->initial_balance,
            'current_balance' => $request->initial_balance, // Set current balance to initial balance
            'amount_in_bdt' => $amountInBDT,
        ]);
        
        return redirect()->route('admin.banks.index')
            ->with('success', 'Bank account created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bank $bank)
    {
        return view('admin.banks.show', compact('bank'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Bank $bank)
    {
        return view('admin.banks.edit', compact('bank'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Bank $bank)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'account_number' => 'required|string|max:255|unique:banks,account_number,' . $bank->id,
            'branch' => 'nullable|string|max:255',
            'currency_id' => 'required|exists:currencies,id',
            'is_active' => 'nullable|boolean',
        ]);
        
        // Check if currency has changed
        $oldCurrencyId = $bank->currency_id;
        $newCurrencyId = $request->currency_id;
        $currentBalance = $bank->current_balance;
        $amountInBDT = $bank->amount_in_bdt;
        
        // If currency changed, recalculate the balances
        if ($oldCurrencyId != $newCurrencyId) {
            $oldCurrency = Currency::find($oldCurrencyId);
            $newCurrency = Currency::find($newCurrencyId);
            
            // Convert current balance to BDT if old currency exists
            if ($oldCurrency) {
                $amountInBDT = $currentBalance * $oldCurrency->conversion_rate;
            }
            
            // Convert BDT to new currency if new currency exists
            if ($newCurrency && $newCurrency->code !== 'BDT') {
                $currentBalance = $amountInBDT / $newCurrency->conversion_rate;
            } else {
                // If new currency is BDT, set current balance to BDT amount
                $currentBalance = $amountInBDT;
            }
        }
        
        $bank->update([
            'name' => $request->name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'branch' => $request->branch,
            'currency_id' => $request->currency_id,
            'current_balance' => $currentBalance,
            'amount_in_bdt' => $amountInBDT,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);
        
        return redirect()->route('admin.banks.index')
            ->with('success', 'Bank account updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bank $bank)
    {
        $bank->delete();
        
        return redirect()->route('admin.banks.index')
            ->with('success', 'Bank account deleted successfully!');
    }
    
    /**
     * Adjust the bank balance (increase or decrease).
     */
    public function adjustBalance(Request $request, Bank $bank)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'amount_in_bdt' => 'nullable|numeric|min:0',
            'type' => 'required|in:increase,decrease',
        ]);
        
        $amount = $request->amount;
        $amountInBDT = $request->amount_in_bdt;
        $type = $request->type;
        $success = false;
        
        // Determine if we're working with BDT or the bank's currency
        $isBDT = !$bank->currency || $bank->currency->code === 'BDT';
        
        if ($type === 'increase') {
            $success = $bank->increaseBalance($amount, $isBDT);
            $message = 'Bank balance increased successfully!';
        } else {
            $success = $bank->decreaseBalance($amount, $isBDT);
            $message = $success 
                ? 'Bank balance decreased successfully!' 
                : 'Insufficient funds in the bank account!';
        }
        
        return redirect()->route('admin.banks.show', $bank->id)
            ->with($success ? 'success' : 'error', $message);
    }

    /**
     * Show compare page with system vs physical balances per bank for a given date.
     */
    public function compareIndex(Request $request)
    {
        $date = $request->query('date') ? Carbon::parse($request->query('date'))->toDateString() : Carbon::today()->toDateString();
        $banks = Bank::with('currency')->orderBy('name')->get();
        $existing = BankDailyBalance::where('date', $date)->get()->keyBy('bank_id');

        return view('admin.banks.compare', compact('banks', 'date', 'existing'));
    }

    /**
     * Store/update physical balances for the compare page.
     */
    public function compareStore(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'banks' => 'required|array',
            'banks.*.physical_amount' => 'nullable|numeric|min:0',
            'banks.*.note' => 'nullable|string|max:1000',
        ]);

        $date = Carbon::parse($validated['date'])->toDateString();
        $banksInput = $validated['banks'];

        // Fetch banks once
        $banks = Bank::with('currency')->whereIn('id', array_keys($banksInput))->get()->keyBy('id');

        foreach ($banksInput as $bankId => $payload) {
            if (!isset($banks[$bankId])) continue;
            $bank = $banks[$bankId];
            $physical = isset($payload['physical_amount']) && $payload['physical_amount'] !== null
                ? (float)$payload['physical_amount'] : null;
            $note = $payload['note'] ?? null;

            if ($physical === null) {
                // skip if not provided
                continue;
            }

            $rate = $bank->currency ? (float)($bank->currency->conversion_rate ?? 1) : 1.0;
            if ($rate <= 0) { $rate = 1.0; }

            // Convert physical to BDT if needed
            $physicalBDT = $bank->currency && ($bank->currency->code !== 'BDT')
                ? $physical * $rate
                : $physical;

            // System BDT snapshot
            $systemBDT = $bank->amount_in_bdt ?? (($bank->currency && $bank->currency->code !== 'BDT')
                ? ((float)($bank->current_balance ?? 0)) * $rate
                : (float)($bank->current_balance ?? 0));

            $differenceBDT = $physicalBDT - (float)$systemBDT;

            BankDailyBalance::updateOrCreate(
                [
                    'bank_id' => $bank->id,
                    'date' => $date,
                ],
                [
                    'physical_amount' => $physical,
                    'physical_amount_bdt' => $physicalBDT,
                    'system_amount_bdt' => $systemBDT,
                    'difference_bdt' => $differenceBDT,
                    'note' => $note,
                    'created_by' => optional(auth()->user())->id,
                ]
            );
        }

        return redirect()->route('admin.banks.compare', ['date' => $date])->with('success', 'Daily bank balances saved.');
    }
}
