<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class ResetPasswordController extends Controller
{
    public function showResetForm()
    {
        if (!Session::has('otp_verified') || !Session::has('forgot_user_id')) {
            return redirect()->route('password.request')
                ->with('error', 'Session expired. Please restart password reset.');
        }

        return view('auth.reset-password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $userId = Session::get('forgot_user_id');
        if (!$userId) {
            return redirect()->route('password.request')->with('error', 'Session expired.');
        }

        $hashed = Hash::make($request->password);

        DB::table('users')->where('user_id', $userId)->update(['pass' => $hashed]);

        // clear sessions
        Session::forget(['otp_verified', 'forgot_user_id']);

        return redirect()->route('login')->with('success', 'Password reset successfully! Please log in.');
    }
}
