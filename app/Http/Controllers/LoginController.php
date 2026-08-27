<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'=>'required|email',
            'password'=>'required'
        ]);

        /*
        ==========================
        CEK ADMIN DULU
        ==========================
        */

        $admin = Admin::where('email',$request->email)->first();

        if($admin && Hash::check($request->password,$admin->password))
        {
            session([
                'admin'=>$admin->id,
                'nama'=>$admin->nama
            ]);

            return redirect()->route('admin.dashboard');
        }

        /*
        ==========================
        CEK USER
        ==========================
        */

        if(Auth::attempt([
            'email'=>$request->email,
            'password'=>$request->password
        ]))
        {
            $request->session()->regenerate();

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email'=>'Email atau Password salah.'
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();

        session()->forget(['admin','nama']);

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}