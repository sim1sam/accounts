<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invoices = Invoice::with('customer')->latest()->paginate(10);
        return view('admin.invoices.index', compact('invoices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get all customers for the dropdown
        $customers = Customer::orderBy('name')->get();
        
        return view('admin.invoices.create', compact('customers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|string|unique:invoices,invoice_id',
            'customer_id' => 'required|exists:customers,id',
            'invoice_value' => 'required|numeric|min:0',
        ]);
        
        $invoice = Invoice::create([
            'invoice_id' => $request->invoice_id,
            'customer_id' => $request->customer_id,
            'invoice_value' => $request->invoice_value,
        ]);
        
        return redirect()->route('admin.invoices.index')
            ->with('success', 'Invoice created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load('customer');
        return view('admin.invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        $invoice->load('customer');
        return view('admin.invoices.edit', compact('invoice'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'invoice_id' => 'required|string|unique:invoices,invoice_id,' . $invoice->id,
            'invoice_value' => 'required|numeric|min:0',
        ]);
        
        $invoice->update([
            'invoice_id' => $request->invoice_id,
            'invoice_value' => $request->invoice_value,
        ]);
        
        return redirect()->route('admin.invoices.index')
            ->with('success', 'Invoice updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        
        return redirect()->route('admin.invoices.index')
            ->with('success', 'Invoice deleted successfully!');
    }
    
    /**
     * Find customer by mobile number.
     */
    public function findCustomerByMobile(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string',
        ]);
        
        $customer = Customer::where('mobile', $request->mobile)->first();
        
        if ($customer) {
            return response()->json([
                'success' => true,
                'customer' => $customer,
                'kam' => $customer->keyAccountManager ? $customer->keyAccountManager->name : null,
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Customer not found',
        ]);
    }
}
