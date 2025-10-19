<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function showAuthPage(Request $request)
    {
        if ($request->session()->has('admin_id')) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'username'  => ['required', 'string', 'max:100', 'unique:admin,username'],
            'email'     => ['required', 'email', 'max:190', 'unique:admin,email'],
            'password'  => ['required', 'string', 'min:8', 'confirmed'], // expects password_confirmation
            'full_name' => ['nullable', 'string', 'max:190'],
        ]);

        $admin = Admin::create([
            'username'  => $validated['username'],
            'email'     => $validated['email'],
            'password'  => $validated['password'], // hashed by mutator
            'full_name' => $validated['full_name'] ?? null,
        ]);

        return back()
            ->with('success', 'Admin account created successfully! You can now login.')
            ->with('switch_to_login', true)
            ->withInput(['username' => $admin->username, 'email' => $admin->email]);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $admin = Admin::where('username', $validated['username'])->first();

        if (!$admin || !Hash::check($validated['password'], $admin->password)) {
            return back()->with('error', 'Invalid username or password.')->withInput();
        }

        // same as your PHP: bump updated_at on login
        $admin->touch();

        // same session keys as legacy
        $request->session()->put([
            'admin_id'        => $admin->getKey(),
            'admin_username'  => $admin->username,
            'admin_full_name' => $admin->full_name,
        ]);

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['admin_id','admin_username','admin_full_name']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.auth')->with('success', 'Signed out successfully.');
    }
}
