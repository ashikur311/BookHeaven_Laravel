<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class VerifyOtpController extends Controller
{
    public function show()
    {
        if (!Session::has('forgot_user_id')) {
            return redirect()->route('password.request')->with('error', 'No session found. Please start again.');
        }

        return view('auth.verify-otp');
    }

    public function verify(Request $request)
    {
        $request->validate(['otp_code' => 'required|digits:6']);
        $userId = Session::get('forgot_user_id');
        $enteredOtp = $request->otp_code;

        $otpRecord = DB::table('user_otp')
            ->where('user_id', $userId)
            ->where('purpose', 'password_reset')
            ->orderByDesc('id')
            ->first();

        if (!$otpRecord) {
            return back()->with('error', 'No OTP found. Please request a new one.');
        }

        $otpTime = strtotime($otpRecord->otp_time);
        if (time() - $otpTime > 900) { // 15 min expiry
            return back()->with('error', 'OTP expired. Please request a new one.');
        }

        if ($enteredOtp != $otpRecord->otp_code) {
            $attempts = $otpRecord->otp_attempts + 1;
            if ($attempts >= 3) {
                return back()->with('error', 'Maximum attempts reached. Please request a new OTP.');
            }

            DB::table('user_otp')->where('id', $otpRecord->id)->update(['otp_attempts' => $attempts]);
            return back()->with('error', 'Invalid OTP. Please try again.');
        }

        // ✅ Verified
        Session::put('otp_verified', true);
        return redirect()->route('password.reset.form')->with('success', 'OTP verified successfully!');
    }
}
