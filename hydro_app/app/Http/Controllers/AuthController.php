<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use Hash;
use Session;
use App\Models\User;

class AuthController extends Controller
{
    public function usersignup() {
        return view('usersignup');
    }

    public function register(Request $request) {

        $validator = Validator::make($request->all(), [
            'account_type' => 'required',
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8'
        ]);

        if($validator->fails()) {
            return redirect()->route('usersignup')->withErrors($validator);
        }

        User::create([
            'account_type' => $request->get('account_type'),
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password'))
        ]);

        return redirect()->route('userlogin')->with('success', 'You can now sign in');

    }

    public function userlogin() {
        return view('userlogin');
    }

    public function authenticate(Request $request) {

        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if($validator->fails()) {
            return redirect()->route('userlogin')->withErrors($validator);
        }

        $credentials = $request->only('email', 'password');
        if(Auth::attempt($credentials)) {
            return redirect()->route('dashboard.index')->with('success', 'You are now signed in');
        } else {
            return redirect()->route('userlogin')->withErrors('Invalid email and password');
        }
    }

    public function userlogout() {
        Session::flush();
        Auth::logout();

        return redirect()->route('userlogin');
    }
}
