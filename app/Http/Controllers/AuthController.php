<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller {
    public function showLogin() {
        return view('auth.login');
    }
    public function login(Request $r) {
        $creds = $r->validate([
            'username' => ['required','string'],
            'password' => ['required','string'],
            'remember' => ['sometimes','boolean'],
        ]);
        if (Auth::attempt(['username'=>$creds['username'], 'password'=>$creds['password']], $r->boolean('remember'))) {
            $r->session()->regenerate();
            return redirect()->intended(route('home'));
        }
        return back()->withErrors(['username' => 'Falscher Benutzername oder Passwort.'])->onlyInput('username');
    }
    public function logout(Request $r) {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect()->route('login');
    }
}
