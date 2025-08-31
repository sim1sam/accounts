<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    /**
     * Display a listing of the accounts.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accounts = Account::with('currency')->get();
        return view('admin.accounts.index', compact('accounts'));
    }

    /**
     * Show the form for creating a new account.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $currencies = Currency::where('is_active', true)->get();
        return view('admin.accounts.create', compact('currencies'));
    }

    /**
     * Store a newly created account in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'initial_amount' => 'required|numeric|min:0',
            'currency_id' => 'required|exists:currencies,id',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $account = new Account();
        $account->name = $request->name;
        $account->category = $request->category;
        $account->initial_amount = $request->initial_amount;
        $account->current_amount = $request->initial_amount;
        $account->currency_id = $request->currency_id;
        $account->is_active = $request->has('is_active');
        $account->save();

        return redirect()->route('admin.accounts.index')
            ->with('success', 'Account created successfully.');
    }

    /**
     * Display the specified account.
     *
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        return view('admin.accounts.show', compact('account'));
    }

    /**
     * Show the form for editing the specified account.
     *
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function edit(Account $account)
    {
        $currencies = Currency::where('is_active', true)->get();
        return view('admin.accounts.edit', compact('account', 'currencies'));
    }

    /**
     * Update the specified account in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Account $account)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'currency_id' => 'required|exists:currencies,id',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // If currency is changing, adjust the amount based on conversion rates
        if ($account->currency_id != $request->currency_id) {
            $oldCurrency = Currency::find($account->currency_id);
            $newCurrency = Currency::find($request->currency_id);
            
            // Convert to BDT first, then to new currency
            $amountInBDT = $account->current_amount * $oldCurrency->conversion_rate;
            $account->current_amount = $amountInBDT / $newCurrency->conversion_rate;
        }

        $account->name = $request->name;
        $account->category = $request->category;
        $account->currency_id = $request->currency_id;
        $account->is_active = $request->has('is_active');
        $account->save();

        return redirect()->route('admin.accounts.index')
            ->with('success', 'Account updated successfully.');
    }

    /**
     * Remove the specified account from storage.
     *
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $account)
    {
        // Check if account exists and delete it
        if ($account->exists) {
            $account->delete();
            return redirect()->route('admin.accounts.index')
                ->with('success', 'Account deleted successfully.');
        }
        
        return redirect()->route('admin.accounts.index')
            ->with('error', 'Account not found.');
    }

    /**
     * Adjust the account balance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function adjustBalance(Request $request, Account $account)
    {
        $validator = Validator::make($request->all(), [
            'adjustment_type' => 'required|in:increase,decrease',
            'amount' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $amount = $request->amount;
        $success = false;

        if ($request->adjustment_type === 'increase') {
            $success = $account->increaseBalance($amount);
            $message = 'Account balance increased successfully.';
        } else {
            $success = $account->decreaseBalance($amount);
            $message = $success ? 'Account balance decreased successfully.' : 'Insufficient balance.';
        }

        return redirect()->route('admin.accounts.show', $account->id)
            ->with($success ? 'success' : 'error', $message);
    }
}
