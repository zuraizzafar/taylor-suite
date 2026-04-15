<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('branch')
            ->whereIn('role', ['branch_manager', 'worker'])
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        return view('users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'unique:users,email'],
            'role'                  => ['required', 'in:branch_manager,worker'],
            'password'              => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'role'     => $data['role'],
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function editPassword(User $user): View
    {
        abort_unless(in_array($user->role, ['branch_manager', 'worker']), 403);
        return view('users.password', compact('user'));
    }

    public function updatePassword(Request $request, User $user): RedirectResponse
    {
        abort_unless(in_array($user->role, ['branch_manager', 'worker']), 403);

        $request->validate([
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('users.index')->with('success', "Password updated for {$user->name}.");
    }
}
