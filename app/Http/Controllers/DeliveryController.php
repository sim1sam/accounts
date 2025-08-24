<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\Customer;
use App\Models\Staff;
use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Delivery::query()->with(['customer.keyAccountManager'])->latest();

        // Free text search: shipment_no or customer name/mobile
        $q = trim((string) $request->get('q'));
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('shipment_no', 'like', "%{$q}%")
                  ->orWhereHas('customer', function ($c) use ($q) {
                      $c->where('name', 'like', "%{$q}%")
                        ->orWhere('mobile', 'like', "%{$q}%");
                  });
            });
        }

        // Staff filter via customer's KAM
        if ($request->filled('staff_id')) {
            $query->whereHas('customer', function ($c) use ($request) {
                $c->where('kam', $request->integer('staff_id'));
            });
        }

        // Delivery date range
        if ($request->filled('delivery_date_from')) {
            $query->whereDate('delivery_date', '>=', $request->date('delivery_date_from'));
        }
        if ($request->filled('delivery_date_to')) {
            $query->whereDate('delivery_date', '<=', $request->date('delivery_date_to'));
        }

        // Delivery value range
        if ($request->filled('min_value')) {
            $query->where('delivery_value', '>=', (float) $request->get('min_value'));
        }
        if ($request->filled('max_value')) {
            $query->where('delivery_value', '<=', (float) $request->get('max_value'));
        }

        $deliveries = $query->paginate(10)->withQueryString();

        // Staff list for filter dropdown
        $staff = Staff::orderBy('name')->get();

        return view('admin.deliveries.index', compact('deliveries', 'staff'));
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
