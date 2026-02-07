<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Active filter
        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        return $query->orderBy('name')->paginate($request->per_page ?? 15);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:customers,code',
            'name' => 'required|max:255',
            'address' => 'nullable',
            'phone' => 'nullable|max:20',
            'email' => 'nullable|email|max:255',
            'contact_person' => 'nullable|max:255',
            'contact_phone' => 'nullable|max:20',
            'tax_id' => 'nullable|max:50',
            'payment_terms' => 'required|in:cash,net_7,net_14,net_30,net_45,net_60',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable',
            'is_active' => 'boolean'
        ]);

        $customer = Customer::create($validated);

        return response()->json([
            'message' => '✅ Customer created successfully',
            'data' => $customer
        ], 201);
    }

    public function show(Customer $customer)
    {
        return $customer->load(['invoices' => function ($query) {
            $query->orderBy('invoice_date', 'desc')->limit(10);
        }]);
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'code' => ['required', Rule::unique('customers')->ignore($customer->id)],
            'name' => 'required|max:255',
            'address' => 'nullable',
            'phone' => 'nullable|max:20',
            'email' => 'nullable|email|max:255',
            'contact_person' => 'nullable|max:255',
            'contact_phone' => 'nullable|max:20',
            'tax_id' => 'nullable|max:50',
            'payment_terms' => 'required|in:cash,net_7,net_14,net_30,net_45,net_60',
            'credit_limit' => 'nullable|numeric|min:0',
            'notes' => 'nullable',
            'is_active' => 'boolean'
        ]);

        $customer->update($validated);

        return response()->json([
            'message' => '✅ Customer updated successfully',
            'data' => $customer
        ]);
    }

    public function destroy(Customer $customer)
    {
        // Check if customer has invoices
        if ($customer->invoices()->count() > 0) {
            return response()->json([
                'message' => '❌ Cannot delete customer with existing invoices. Please deactivate instead.'
            ], 422);
        }

        $customer->delete();

        return response()->json([
            'message' => '✅ Customer deleted successfully'
        ]);
    }
}
