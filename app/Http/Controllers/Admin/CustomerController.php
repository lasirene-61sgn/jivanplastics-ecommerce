<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $customers = Customer::latest()->paginate(10);
        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Display a listing of dealer customers.
     *
     * @return \Illuminate\View\View
     */
    public function dealers()
    {
        $customers = Customer::dealers()->latest()->paginate(10);
        return view('admin.customers.dealers', compact('customers'));
    }

    /**
     * Display a listing of individual customers.
     *
     * @return \Illuminate\View\View
     */
    public function individuals()
    {
        $customers = Customer::individuals()->latest()->paginate(10);
        return view('admin.customers.individuals', compact('customers'));
    }

    /**
     * Show the form for creating a new customer.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * Store a newly created customer in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email|unique:users,email',
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'gst_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'customer_type' => 'required|in:individual,dealer',
            'is_active' => 'boolean',
            'is_cod_allowed' => 'boolean',
            'bank_transfer_discount' => 'nullable|numeric|min:0|max:100',
        ]);

        // Prepare data for creation
        $data = $request->except('password');
        
        // Hash password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Create customer record
        $customer = Customer::create($data);

        // Create corresponding user record for authentication
        if ($request->filled('password')) {
            User::updateOrCreate(
                ['email' => $request->email],
                [
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password)
                ]
            );
        }

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified customer.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\View\View
     */
    public function show(Customer $customer)
    {
        return view('admin.customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified customer.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\View\View
     */
    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Update the specified customer in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Customer $customer)
    {
        // Get the user ID for validation
        $userId = User::where('email', $customer->email)->value('id') ?? 0;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $customer->id . '|unique:users,email,' . $userId,
            'password' => 'nullable|string|min:6',
            'phone' => 'nullable|string|max:20',
            'company_name' => 'nullable|string|max:255',
            'gst_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'customer_type' => 'required|in:individual,dealer',
            'is_active' => 'boolean',
            'is_cod_allowed' => 'boolean',
            'bank_transfer_discount' => 'nullable|numeric|min:0|max:100',
        ]);

        // Prepare data for update
        $data = $request->except('password');
        
        // Hash password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $customer->update($data);

        // Update corresponding user record for authentication
        if ($request->filled('password')) {
            User::updateOrCreate(
                ['email' => $request->email],
                [
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password)
                ]
            );
        } elseif ($request->email != $customer->email) {
            // If email is changed, update the user record
            $user = User::where('email', $customer->email)->first();
            if ($user) {
                $user->update([
                    'name' => $request->name,
                    'email' => $request->email
                ]);
            }
        }

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified customer from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Customer $customer)
    {
        // Also delete the corresponding user record
        $user = User::where('email', $customer->email)->first();
        if ($user) {
            $user->delete();
        }
        
        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}