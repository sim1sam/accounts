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
        // Check if we have multiple deliveries
        if ($request->has('deliveries')) {
            // Validate each delivery entry
            foreach ($request->deliveries as $index => $deliveryData) {
                $request->validate([
                    "deliveries.{$index}.customer_id" => 'required|exists:customers,id',
                    "deliveries.{$index}.delivery_value" => 'required|numeric|min:0',
                    "deliveries.{$index}.delivery_date" => 'required|date',
                    "deliveries.{$index}.shipment_no" => 'required|string|max:255',
                ]);
            }
            
            // Create each delivery
            foreach ($request->deliveries as $deliveryData) {
                Delivery::create($deliveryData);
            }
            
            return redirect()->route('admin.deliveries.index')
                ->with('success', count($request->deliveries) . ' deliveries created successfully.');
        } else {
            // Handle single delivery (legacy support)
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
