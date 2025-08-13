<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;

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
            'initial_balance' => 'required|numeric|min:0',
        ]);
        
        $bank = Bank::create([
            'name' => $request->name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'branch' => $request->branch,
            'initial_balance' => $request->initial_balance,
            'current_balance' => $request->initial_balance, // Set current balance to initial balance
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
            'is_active' => 'sometimes|boolean',
        ]);
        
        $bank->update([
            'name' => $request->name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'branch' => $request->branch,
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
            'type' => 'required|in:increase,decrease',
        ]);
        
        $amount = $request->amount;
        $type = $request->type;
        $success = false;
        
        if ($type === 'increase') {
            $success = $bank->increaseBalance($amount);
            $message = 'Bank balance increased successfully!';
        } else {
            $success = $bank->decreaseBalance($amount);
            $message = $success 
                ? 'Bank balance decreased successfully!' 
                : 'Insufficient funds in the bank account!';
        }
        
        return redirect()->route('admin.banks.show', $bank->id)
            ->with($success ? 'success' : 'error', $message);
    }
}
