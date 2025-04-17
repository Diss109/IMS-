<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EvaluatorPermission;
use App\Models\ServiceProvider;
use App\Models\User;
use Illuminate\Http\Request;

class EvaluatorPermissionController extends Controller
{
    public function index()
    {
        $categories = ServiceProvider::getTypes();
        $roles = User::getRoles();
        $permissions = EvaluatorPermission::all()->groupBy('service_provider_type');
        return view('admin.evaluator_permissions.index', compact('categories', 'roles', 'permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'permissions' => 'required|array',
        ]);

        // Remove all existing permissions
        EvaluatorPermission::truncate();

        // Save new permissions
        foreach ($request->permissions as $category => $roles) {
            foreach ($roles as $role) {
                EvaluatorPermission::create([
                    'service_provider_type' => $category,
                    'role' => $role,
                ]);
            }
        }

        return redirect()->route('admin.evaluator_permissions.index')->with('success', 'Permissions mises à jour avec succès.');
    }
}
