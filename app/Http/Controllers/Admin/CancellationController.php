<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cancellation;
use App\Models\Customer;
use App\Models\Staff;
use Illuminate\Http\Request;

class CancellationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cancellations = Cancellation::with('customer')->latest()->get();
        return view('admin.cancellations.index', compact('cancellations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::all();
        $staff = Staff::orderBy('name')->get();
        return view('admin.cancellations.create', compact('customers', 'staff'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check if we have multiple cancellations
        if ($request->has('cancellations')) {
            // Validate each cancellation entry
            foreach ($request->cancellations as $index => $cancellationData) {
                $request->validate([
                    "cancellations.{$index}.customer_id" => 'required|exists:customers,id',
                    "cancellations.{$index}.staff_id" => 'required|exists:staff,id',
                    "cancellations.{$index}.cancellation_value" => 'required|numeric|min:0',
                    "cancellations.{$index}.remarks" => 'nullable|string',
                    "cancellations.{$index}.cancellation_date" => 'required|date'
                ]);
            }
            
            // Create each cancellation
            foreach ($request->cancellations as $cancellationData) {
                Cancellation::create($cancellationData);
            }
            
            return redirect()->route('admin.cancellations.index')
                ->with('success', count($request->cancellations) . ' cancellations created successfully.');
        } else {
            // Handle single cancellation (legacy support)
            $request->validate([
                'customer_id' => 'required|exists:customers,id',
                'staff_id' => 'required|exists:staff,id',
                'cancellation_value' => 'required|numeric|min:0',
                'remarks' => 'nullable|string',
                'cancellation_date' => 'required|date'
            ]);

            Cancellation::create($request->all());

            return redirect()->route('admin.cancellations.index')
                ->with('success', 'Cancellation created successfully.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cancellation = Cancellation::with('customer')->findOrFail($id);
        return view('admin.cancellations.show', compact('cancellation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $cancellation = Cancellation::findOrFail($id);
        $customers = Customer::all();
        $staff = Staff::orderBy('name')->get();
        return view('admin.cancellations.edit', compact('cancellation', 'customers', 'staff'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'staff_id' => 'required|exists:staff,id',
            'cancellation_value' => 'required|numeric|min:0',
            'remarks' => 'nullable|string',
            'cancellation_date' => 'required|date'
        ]);

        $cancellation = Cancellation::findOrFail($id);
        $cancellation->update($request->all());

        return redirect()->route('admin.cancellations.index')
            ->with('success', 'Cancellation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cancellation = Cancellation::findOrFail($id);
        $cancellation->delete();

        return redirect()->route('admin.cancellations.index')
            ->with('success', 'Cancellation deleted successfully.');
    }
}
