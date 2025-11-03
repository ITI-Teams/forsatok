<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function showResetPassword(Request $request, $token=null){
        return view('auth.reset-password')->with([
            'token'=>$token,
            'email'=>$request->email
        ]);
    }

    public function resetPassword(Request $request){
        $request->validate([
            'token'=>'required',
            'email'=>'required|email',
            'password'=>'required|confirmed|min:6',
        ]);

        $status = Password::reset($request->only('email','password','password_confirmation','token'),
        function($user, $password){
            $user->password = Hash::make($password);
            $user->save();
        });

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('auth.login')->with('status',__($status))
            : back()->withErrors(['email'=>[__($status)]]);
    }
}
