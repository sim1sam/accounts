<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Models\Customer;
use Illuminate\Http\Request;

class RefundController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $refunds = Refund::with('customer')->latest()->get();
        return view('admin.refunds.index', compact('refunds'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::all();
        return view('admin.refunds.create', compact('customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'refund_amount' => 'required|numeric|min:0',
            'refund_date' => 'required|date',
            'account' => 'nullable|string',
            'remarks' => 'nullable|string'
        ]);

        Refund::create($request->all());

        return redirect()->route('admin.refunds.index')
            ->with('success', 'Refund created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $refund = Refund::with('customer')->findOrFail($id);
        return view('admin.refunds.show', compact('refund'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $refund = Refund::findOrFail($id);
        $customers = Customer::all();
        return view('admin.refunds.edit', compact('refund', 'customers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'refund_amount' => 'required|numeric|min:0',
            'refund_date' => 'required|date',
            'account' => 'nullable|string',
            'remarks' => 'nullable|string'
        ]);

        $refund = Refund::findOrFail($id);
        $refund->update($request->all());

        return redirect()->route('admin.refunds.index')
            ->with('success', 'Refund updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $refund = Refund::findOrFail($id);
        $refund->delete();

        return redirect()->route('admin.refunds.index')
            ->with('success', 'Refund deleted successfully.');
    }
}
