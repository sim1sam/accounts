<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('customer')->latest()->paginate(10);
        return view('admin.payments.index', compact('payments'));
    }

    public function create()
    {
        $customers = Customer::all();
        return view('admin.payments.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'account_no' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Payment::create($request->all());

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment created successfully.');
    }

    public function show(Payment $payment)
    {
        return view('admin.payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $customers = Customer::all();
        return view('admin.payments.edit', compact('payment', 'customers'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'account_no' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $payment->update($request->all());

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()->route('admin.payments.index')
            ->with('success', 'Payment deleted successfully.');
    }
    
    public function findCustomer(Request $request)
    {
        $customer = Customer::where('mobile', $request->mobile)->first();
        
        if ($customer) {
            $kam = null;
            if ($customer->kam_id) {
                $staff = Staff::find($customer->kam_id);
                $kam = $staff ? $staff->name : null;
            }
            
            return response()->json([
                'success' => true,
                'customer' => $customer,
                'kam' => $kam
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Customer not found'
        ]);
    }
}
