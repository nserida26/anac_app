<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    // List all users
    public function index()
    {
        $users = User::with('roles')->get();
        return view('admin.users.index', compact('users'));
    }
 
    // Show create form
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    // Store a new user
    public function store(Request $request)
    {
        $request->validate([
            
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'roles' => 'array',
        ]);

        $user = User::create([
            
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        if ($request->roles) {
            $user->assignRole($request->roles);
        }
        $user->markEmailAsVerified();

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    // Show edit form
    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('name')->toArray();
        return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
    }

    // Update a user
    public function update(Request $request, User $user)
    {
        $request->validate([
            
            'email' => "required|email|unique:users,email,{$user->id}",
            'roles' => 'array',
        ]);

        $user->update([
            
            'email' => $request->email,
        ]);

        $user->syncRoles($request->roles);
        $user->markEmailAsVerified();

        return redirect()->route('users.index')->with('success', 'User updated successfully');
    }

    // Delete a user
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }

    // Assign roles to a user
    public function assignRoles(Request $request, User $user)
    {
        $request->validate(['roles' => 'array']);
        $user->syncRoles($request->roles);

        return redirect()->back()->with('success', 'Roles assigned successfully');
    }
}
