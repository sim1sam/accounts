<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Invoice::query()->with(['customer', 'staff'])->latest();

        // Free text search: invoice_id or customer name/mobile
        $q = trim((string) $request->get('q'));
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('invoice_id', 'like', "%{$q}%")
                  ->orWhereHas('customer', function ($c) use ($q) {
                      $c->where('name', 'like', "%{$q}%")
                        ->orWhere('mobile', 'like', "%{$q}%");
                  });
            });
        }

        // Staff filter
        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->integer('staff_id'));
        }

        // Invoice date range filters
        if ($request->filled('invoice_date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date('invoice_date_from'));
        }
        if ($request->filled('invoice_date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date('invoice_date_to'));
        }

        // Invoice value range
        if ($request->filled('min_value')) {
            $query->where('invoice_value', '>=', (float) $request->get('min_value'));
        }
        if ($request->filled('max_value')) {
            $query->where('invoice_value', '<=', (float) $request->get('max_value'));
        }

        $invoices = $query->paginate(10)->withQueryString();

        // Staff list for filter dropdown
        $staff = Staff::orderBy('name')->get();

        return view('admin.invoices.index', compact('invoices', 'staff'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get all customers for the dropdown
        $customers = Customer::orderBy('name')->get();
        // Get all staff for the dropdown
        $staff = Staff::orderBy('name')->get();
        
        return view('admin.invoices.create', compact('customers', 'staff'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check if we're handling multiple invoices or a single invoice
        if ($request->has('invoices')) {
            // Multiple invoices
            $successCount = 0;
            
            foreach ($request->invoices as $key => $invoiceData) {
                // Validate each invoice
                $validator = validator($invoiceData, [
                    'invoice_id' => 'required|string|unique:invoices,invoice_id',
                    'customer_id' => 'required|exists:customers,id',
                    'staff_id' => 'required|exists:staff,id',
                    'invoice_value' => 'required|numeric|min:0',
                    'invoice_date' => 'nullable|date',
                ]);
                
                if ($validator->fails()) {
                    continue; // Skip this invoice if validation fails
                }
                
                // Create the invoice
                $invoice = Invoice::create([
                    'invoice_id' => $invoiceData['invoice_id'],
                    'customer_id' => $invoiceData['customer_id'],
                    'staff_id' => $invoiceData['staff_id'],
                    'invoice_value' => $invoiceData['invoice_value'],
                    'invoice_date' => $invoiceData['invoice_date'] ?? null,
                ]);
                // timestamps are left as current; invoice_date stores the business date
                
                $successCount++;
            }
            
            $message = $successCount > 0 
                ? ($successCount . ' ' . ($successCount == 1 ? 'invoice' : 'invoices') . ' created successfully!') 
                : 'No invoices were created. Please check your input.';
                
            return redirect()->route('admin.invoices.index')
                ->with('success', $message);
        } else {
            // Single invoice (legacy support)
            $request->validate([
                'invoice_id' => 'required|string|unique:invoices,invoice_id',
                'customer_id' => 'required|exists:customers,id',
                'staff_id' => 'required|exists:staff,id',
                'invoice_value' => 'required|numeric|min:0',
                'invoice_date' => 'nullable|date',
            ]);
            
            $invoice = Invoice::create([
                'invoice_id' => $request->invoice_id,
                'customer_id' => $request->customer_id,
                'staff_id' => $request->staff_id,
                'invoice_value' => $request->invoice_value,
                'invoice_date' => $request->invoice_date,
            ]);
            // timestamps are left as current; invoice_date stores the business date
            
            return redirect()->route('admin.invoices.index')
                ->with('success', 'Invoice created successfully!');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load('customer', 'staff');
        return view('admin.invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice)
    {
        $invoice->load('customer', 'staff');
        $customers = Customer::orderBy('name')->get();
        $staff = Staff::orderBy('name')->get();
        return view('admin.invoices.edit', compact('invoice', 'customers', 'staff'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'invoice_id' => 'required|string|unique:invoices,invoice_id,' . $invoice->id,
            'invoice_value' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id',
            'staff_id' => 'nullable|exists:staff,id',
            'invoice_date' => 'nullable|date',
        ]);
        
        $invoice->update([
            'invoice_id' => $request->invoice_id,
            'invoice_value' => $request->invoice_value,
            'customer_id' => $request->customer_id ?? $invoice->customer_id,
            'staff_id' => $request->staff_id ?? $invoice->staff_id,
            'invoice_date' => $request->invoice_date,
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
