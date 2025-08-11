<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Customer;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $deliveries = Delivery::with('customer')->latest()->paginate(10);
        return view('admin.deliveries.index', compact('deliveries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::all();
        return view('admin.deliveries.create', compact('customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'delivery_value' => 'required|numeric|min:0',
            'delivery_date' => 'required|date',
            'shipment_no' => 'required|string|max:255',
        ]);
        
        Delivery::create($validated);
        
        return redirect()->route('admin.deliveries.index')
            ->with('success', 'Delivery created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $delivery = Delivery::with('customer.staff')->findOrFail($id);
        return view('admin.deliveries.show', compact('delivery'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $delivery = Delivery::findOrFail($id);
        $customers = Customer::all();
        return view('admin.deliveries.edit', compact('delivery', 'customers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $delivery = Delivery::findOrFail($id);
        
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'delivery_value' => 'required|numeric|min:0',
            'delivery_date' => 'required|date',
            'shipment_no' => 'required|string|max:255',
        ]);
        
        $delivery->update($validated);
        
        return redirect()->route('admin.deliveries.index')
            ->with('success', 'Delivery updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $delivery = Delivery::findOrFail($id);
        $delivery->delete();
        
        return redirect()->route('admin.deliveries.index')
            ->with('success', 'Delivery deleted successfully.');
    }
}
