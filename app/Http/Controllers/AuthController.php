<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('home');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function register(Request $request)
    {
        Log::info('Registration attempt started', [
            'request_data' => $request->all(),
            'headers' => $request->headers->all(),
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type')
        ]);

        try {
            // Log the raw request data
            Log::info('Raw request data:', [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => $request->has('password') ? 'present' : 'missing',
                'password_confirmation' => $request->has('password_confirmation') ? 'present' : 'missing',
                'all_input' => $request->all(),
                'all_files' => $request->allFiles()
            ]);

            // Basic validation
            if (empty($request->input('name')) || empty($request->input('email')) || empty($request->input('password'))) {
                throw new \Exception('All fields are required');
            }

            // Check if email already exists
            if (User::where('email', $request->input('email'))->exists()) {
                throw new \Exception('Email already exists');
            }

            // Log before user creation
            Log::info('Attempting to create user with data:', [
                'name' => $request->input('name'),
                'email' => $request->input('email')
            ]);

            // Create user
            $user = User::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'password' => Hash::make($request->input('password'))
            ]);

            Log::info('User created successfully', [
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'user' => $user
            ]);

        } catch (\Exception $e) {
            Log::error('Registration error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            Log::error('Request data: ' . json_encode($request->all()));
            Log::error('Request headers: ' . json_encode($request->headers->all()));
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('welcome');
    }
}
