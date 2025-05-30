<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Complaint;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\ComplaintReceived;

class ComplaintController extends Controller
{
    /**
     * Show the assistant complaint form (French).
     */
    public function assistantForm()
    {
        return view('reclamation_assistant');
    }

    /**
     * Handle assistant complaint submission (French).
     */
    public function assistantStore(Request $request)
    {
        try {
            // First, let's log what we're receiving to see if the data is correct
            Log::info('===== ASSISTANT SUBMISSION =====');
            Log::info('All form data: ' . json_encode($request->all()));

            // Validate email format server-side
            $email = $request->input('email');
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return response()->json([
                    'success' => false,
                    'message' => 'L\'adresse email fournie n\'est pas valide. Veuillez vérifier le format de votre email.'
                ], 422);
            }

            // Check urgency format
            Log::info('Urgency value received: "' . $request->input('urgence') . '"');

            // Log subject value specifically
            Log::info('Subject value received: "' . $request->input('sujet') . '"');

            // Normalize complaint type if it's "autre"
            $complaintType = $request->input('sujet', 'autre');
            Log::info('Using complaint_type: ' . $complaintType);

            // Use a guaranteed working direct insert like in our test endpoint
            $companyName = $request->input('company_name');
            if (!$companyName) {
                $companyName = 'Particulier';
            } elseif (strtolower(trim($companyName)) === 'non') {
                $companyName = 'sans entreprise';
            }

            // Make sure urgency level is valid
            $urgencyLevel = $request->input('urgence', 'medium');
            if (!in_array($urgencyLevel, ['low', 'medium', 'high', 'critical'])) {
                $urgencyLevel = 'medium';
            }

            $id = DB::table('complaints')->insertGetId([
                'company_name' => $companyName,
                'first_name' => $request->input('nom', 'Client'),
                'last_name' => 'Assistant Client',
                'email' => $request->input('email', 'assistant@example.com'),
                'complaint_type' => $complaintType,
                'description' => $request->input('description', 'No description provided'),
                'urgency_level' => $urgencyLevel,
                'status' => 'en_attente',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            Log::info('SUCCESS - complaint created with ID: ' . $id);

            // Notify all admins about new complaint
            $admins = \App\Models\User::where('role', \App\Models\User::ROLE_ADMIN)->get();
            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'new_complaint',
                    'message' => 'Nouvelle réclamation #' . $id . ' via assistant',
                    'is_read' => false,
                    'related_id' => $id
                ]);
                \Log::info('New complaint notification sent to admin', ['admin_id' => $admin->id]);
            }

            // Create a notification for ALL admins
            try {
                $admins = \App\Models\User::where('role', \App\Models\User::ROLE_ADMIN)->get();
                foreach ($admins as $admin) {
                    Notification::create([
                        'user_id' => $admin->id,
                        'type' => 'new_complaint',
                        'message' => 'Nouvelle réclamation soumise: #' . $id . ' - ' . $request->input('sujet', 'autre'),
                        'is_read' => false,
                        'related_id' => $id
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Notification creation failed for assistant complaint: ' . $e->getMessage());
            }

            // Handle file separately (after successful complaint creation)
            if ($request->hasFile('piece_jointe')) {
                try {
                    $path = $request->file('piece_jointe')->store('reclamations', 'public');
                    DB::table('complaints')->where('id', $id)->update(['attachment' => $path]);
                    Log::info('File uploaded and attached to complaint: ' . $path);
                } catch (\Exception $fileEx) {
                    Log::warning('File upload failed but complaint was created: ' . $fileEx->getMessage());
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Votre réclamation a été envoyée avec succès.',
                'id' => $id
            ]);
        }
        catch (\Exception $e) {
            Log::error('ASSISTANT ERROR: ' . $e->getMessage());
            Log::error('In file: ' . $e->getFile() . ' line ' . $e->getLine());
            Log::error('Full trace: ' . $e->getTraceAsString());

            // Check if this is a database constraint error (likely enum issue)
            if (strpos($e->getMessage(), 'Data too long') !== false ||
                strpos($e->getMessage(), 'Incorrect string value') !== false ||
                strpos($e->getMessage(), 'Data truncated') !== false ||
                strpos($e->getMessage(), 'Column') !== false) {

                Log::error('Possible database constraint error with complaint_type');

                // Try to resubmit with a definitely valid value
                try {
                    $id = DB::table('complaints')->insertGetId([
                        'company_name' => $request->input('company_name', 'Particulier'),
                        'first_name' => $request->input('nom', 'Client'),
                        'last_name' => 'Assistant Client',
                        'email' => $request->input('email', 'assistant@example.com'),
                        'complaint_type' => 'autre', // Force to 'autre' type
                        'description' => $request->input('description', 'No description provided') . ' (Type original: ' . $request->input('sujet', 'autre') . ')',
                        'urgency_level' => 'medium',
                        'status' => 'en_attente',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    Log::info('RECOVERED - complaint created with ID: ' . $id . ' (forced type: autre)');

                    // Notifications code
                    try {
                        $admins = \App\Models\User::where('role', \App\Models\User::ROLE_ADMIN)->get();
                        foreach ($admins as $admin) {
                            Notification::create([
                                'user_id' => $admin->id,
                                'type' => 'new_complaint',
                                'message' => 'Nouvelle réclamation soumise: #' . $id . ' - autre',
                                'is_read' => false,
                                'related_id' => $id
                            ]);
                        }
                    } catch (\Exception $notifEx) {
                        Log::warning('Notification creation failed for recovery: ' . $notifEx->getMessage());
                    }

                    return response()->json([
                        'success' => true,
                        'message' => 'Votre réclamation a été envoyée avec succès.',
                        'id' => $id
                    ]);

                } catch (\Exception $e2) {
                    Log::error('RECOVERY FAILED: ' . $e2->getMessage());
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors du traitement de votre réclamation. Veuillez réessayer.'
            ], 500);
        }
    }

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

        // Create a notification for ALL admins
        $admins = \App\Models\User::where('role', \App\Models\User::ROLE_ADMIN)->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'new_complaint',
                'message' => 'Nouvelle réclamation soumise: #' . $complaint->id . ' - ' . $complaint->complaint_type,
                'is_read' => false,
                'related_id' => $complaint->id
            ]);
        }

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
