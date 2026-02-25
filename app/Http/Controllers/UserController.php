<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['zone', 'roles'])->latest()->paginate(20);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $zones = Zone::orderBy('nom')->get();
        $roles = Role::all();
        return view('users.create', compact('zones', 'roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'zone_id'  => 'nullable|exists:zones,id',
            'role'     => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'zone_id'  => $data['zone_id'] ?? null,
        ]);

        $user->assignRole($data['role']);

        return redirect()->route('users.index')->with('success', 'Utilisateur créé.');
    }

    public function edit(User $user)
    {
        $zones = Zone::orderBy('nom')->get();
        $roles = Role::all();
        return view('users.edit', compact('user', 'zones', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'zone_id'  => 'nullable|exists:zones,id',
            'role'     => 'required|exists:roles,name',
        ]);

        $updateData = [
            'name'    => $data['name'],
            'email'   => $data['email'],
            'zone_id' => $data['zone_id'] ?? null,
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);
        $user->syncRoles([$data['role']]);

        return redirect()->route('users.index')->with('success', 'Utilisateur modifié.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé.');
    }
}
