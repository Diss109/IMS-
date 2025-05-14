<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;

class ServiceProviderController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceProvider::query();

        // Handle search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Handle category filter
        if ($request->has('category') && !empty($request->category)) {
            $query->where('service_type', $request->category);
        }

        $serviceProviders = $query->paginate(10)->withQueryString();

        return view('admin.service-providers.index', compact('serviceProviders'));
    }

    public function create()
    {
        return view('admin.service-providers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:service_providers',
            'phone' => 'nullable|string|max:20',
            'address' => 'required|string|max:500',
            'service_type' => 'required|string',
        ]);

        ServiceProvider::create($validated);

        return redirect()->route('admin.service-providers.index')
            ->with('success', 'Prestataire ajouté avec succès.');
    }

    public function show(ServiceProvider $serviceProvider)
    {
        return view('admin.service-providers.show', compact('serviceProvider'));
    }

    public function edit(ServiceProvider $serviceProvider)
    {
        return view('admin.service-providers.edit', compact('serviceProvider'));
    }

    public function update(Request $request, ServiceProvider $serviceProvider)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:service_providers,email,' . $serviceProvider->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'required|string|max:500',
            'service_type' => 'required|string',
        ]);

        $serviceProvider->update($validated);

        return redirect()->route('admin.service-providers.index')
            ->with('success', 'Prestataire mis à jour avec succès.');
    }

    public function destroy(ServiceProvider $serviceProvider)
    {
        $serviceProvider->delete();

        return redirect()->route('admin.service-providers.index')
            ->with('success', 'Prestataire supprimé avec succès.');
    }
}
