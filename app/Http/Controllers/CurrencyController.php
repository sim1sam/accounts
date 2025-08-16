<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the currencies.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currencies = Currency::all();
        return view('admin.currencies.index', compact('currencies'));
    }

    /**
     * Show the form for creating a new currency.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.currencies.create');
    }

    /**
     * Store a newly created currency in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:3|unique:currencies',
            'symbol' => 'required|string|max:10',
            'conversion_rate' => 'required|numeric|min:0',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        // If this is set as default, unset all other defaults
        if ($request->has('is_default')) {
            Currency::where('is_default', true)->update(['is_default' => false]);
        }
        
        // Prepare data with proper boolean handling
        $data = $request->all();
        $data['is_default'] = $request->has('is_default');
        $data['is_active'] = $request->has('is_active');
        
        Currency::create($data);

        return redirect()->route('admin.currencies.index')
            ->with('success', 'Currency created successfully.');
    }

    /**
     * Display the specified currency.
     *
     * @param  \App\Models\Currency  $currency
     * @return \Illuminate\Http\Response
     */
    public function show(Currency $currency)
    {
        return view('admin.currencies.show', compact('currency'));
    }

    /**
     * Show the form for editing the specified currency.
     *
     * @param  \App\Models\Currency  $currency
     * @return \Illuminate\Http\Response
     */
    public function edit(Currency $currency)
    {
        return view('admin.currencies.edit', compact('currency'));
    }

    /**
     * Update the specified currency in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Currency  $currency
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Currency $currency)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:3|unique:currencies,code,' . $currency->id,
            'symbol' => 'required|string|max:10',
            'conversion_rate' => 'required|numeric|min:0',
            'is_default' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ]);

        // If this is set as default, unset all other defaults
        if ($request->has('is_default')) {
            Currency::where('is_default', true)->update(['is_default' => false]);
        }
        
        // Prepare data with proper boolean handling
        $data = $request->all();
        $data['is_default'] = $request->has('is_default');
        $data['is_active'] = $request->has('is_active');
        
        $currency->update($data);

        return redirect()->route('admin.currencies.index')
            ->with('success', 'Currency updated successfully.');
    }

    /**
     * Remove the specified currency from storage.
     *
     * @param  \App\Models\Currency  $currency
     * @return \Illuminate\Http\Response
     */
    public function destroy(Currency $currency)
    {
        // Don't allow deleting the default currency
        if ($currency->is_default === true) {
            return redirect()->route('admin.currencies.index')
                ->with('error', 'Default currency cannot be deleted.');
        }

        $currency->delete();

        return redirect()->route('admin.currencies.index')
            ->with('success', 'Currency deleted successfully.');
    }
}
