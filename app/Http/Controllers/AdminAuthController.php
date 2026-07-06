<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function index() { return view('login_admin'); }

    public function login(Request $request)
    {
        $request->validate(['email' => 'required|email', 'password' => 'required']);
        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return back()->withErrors(['email' => 'Email atau Password Admin salah'])->withInput();
        }

        session(['admin' => $admin->id, 'nama' => $admin->nama]);
        return redirect()->route('admin.dashboard'); // Arah ke dashboard admin
    }

    public function logout()
    {
        session()->forget(['admin', 'nama']);
        return redirect()->route('login.admin');
    }
}