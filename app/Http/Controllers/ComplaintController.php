<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use Illuminate\Support\Facades\Mail;
use App\Mail\ComplaintReceived;

class ComplaintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('complaints.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'complaint_type' => 'required|string|in:retard_livraison,retard_chargement,marchandise_endommagée,mauvais_comportement,autre',
            'urgency_level' => 'required|string|in:low,medium,high,critical',
            'description' => 'required|string',
        ], [
            'company_name.required' => 'Le nom de l\'entreprise est requis.',
            'email.required' => 'L\'adresse email est requise.',
            'email.email' => 'L\'adresse email n\'est pas valide.',
            'complaint_type.required' => 'Le type de réclamation est requis.',
            'complaint_type.in' => 'Le type de réclamation sélectionné n\'est pas valide.',
            'urgency_level.required' => 'Le niveau d\'urgence est requis.',
            'urgency_level.in' => 'Le niveau d\'urgence sélectionné n\'est pas valide.',
            'description.required' => 'La description est requise.',
        ]);

        $complaint = Complaint::create($validated);

        return redirect()
            ->route('complaints.show', $complaint)
            ->with('success', 'Votre réclamation a été soumise avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function createPublic()
    {
        return view('complaints.public-create');
    }

    public function storePublic(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'complaint_type' => 'required|string|in:retard_livraison,retard_chargement,marchandise_endommagée,mauvais_comportement,autre',
            'urgency_level' => 'required|string|in:low,medium,high',
            'description' => 'required|string',
        ]);

        try {
            $complaint = Complaint::create([
                'company_name' => $validated['company_name'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'complaint_type' => $validated['complaint_type'],
                'urgency_level' => $validated['urgency_level'],
                'description' => $validated['description'],
                'status' => 'en_attente'
            ]);

            // Send confirmation email to the client
            Mail::to($validated['email'])->send(new ComplaintReceived($complaint));

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Votre réclamation a été soumise avec succès. Vous recevrez un email de confirmation dans quelques instants.'
                ]);
            }

            return redirect()->back()->with('success', 'Votre réclamation a été soumise avec succès. Vous recevrez un email de confirmation dans quelques instants.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Une erreur s\'est produite lors de la soumission de votre réclamation. Veuillez réessayer.'
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Une erreur s\'est produite lors de la soumission de votre réclamation. Veuillez réessayer.']);
        }
    }
}
