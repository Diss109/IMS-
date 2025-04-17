<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transporter;
use Illuminate\Http\Request;

class TransporterController extends Controller
{
    public function index()
    {
        $transporters = Transporter::all();
        return view('admin.transporters.index', compact('transporters'));
    }

    public function create()
    {
        return view('admin.transporters.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_info' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        Transporter::create($validated);

        return redirect()->route('admin.transporters.index')
            ->with('success', 'Transporteur créé avec succès.');
    }

    public function edit(Transporter $transporter)
    {
        return view('admin.transporters.edit', compact('transporter'));
    }

    public function update(Request $request, Transporter $transporter)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_info' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $transporter->update($validated);

        return redirect()->route('admin.transporters.index')
            ->with('success', 'Transporteur mis à jour avec succès.');
    }

    public function destroy(Transporter $transporter)
    {
        $transporter->delete();

        return redirect()->route('admin.transporters.index')
            ->with('success', 'Transporteur supprimé avec succès.');
    }
}
