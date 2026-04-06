<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function index(Request $request): Response
    {
        $customers = Customer::filter($request->only(['search', 'is_active']))
            ->withCount(['villas', 'towerUnits'])
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return Inertia::render('dashboard/customers/Index', [
            'customers' => $customers,
            'filters' => $request->only(['search', 'is_active']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('dashboard/customers/Create');
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $customer = Customer::create($request->validated());

        return redirect()->route('dashboard.customers.show', $customer)
            ->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer): Response
    {
        $customer->load([
            'villas.villaType',
            'villas.status',
            'towerUnits.towerDefinition',
            'towerUnits.status',
        ]);

        return Inertia::render('dashboard/customers/Show', [
            'customer' => $customer,
        ]);
    }

    public function edit(Customer $customer): Response
    {
        return Inertia::render('dashboard/customers/Edit', [
            'customer' => $customer,
        ]);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->validated());

        return redirect()->route('dashboard.customers.show', $customer)
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('dashboard.customers.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
