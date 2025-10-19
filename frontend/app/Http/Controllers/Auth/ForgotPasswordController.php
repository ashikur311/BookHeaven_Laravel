<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Process\Process;

class ForgotPasswordController extends Controller
{
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->email;

        // ✅ Check if email exists
        $user = DB::table('users')->where('email', $email)->first();
        if (!$user) {
            return back()->with('error', 'Email not found in our records.');
        }

        $userId = $user->user_id ?? $user->id;

        // ✅ Generate 6-digit OTP
        $otp = random_int(100000, 999999);

        // ✅ Insert OTP record
        DB::table('user_otp')->insert([
            'user_id' => $userId,
            'otp_code' => $otp,
            'otp_time' => now(),
            'purpose' => 'password_reset',
            'otp_attempts' => 0,
        ]);

        // ✅ Store session
        Session::put('forgot_user_id', $userId);

        // ✅ Python OTP sender
        $python = 'C:\\Users\\USER\\AppData\\Local\\Programs\\Python\\Python313\\python.exe';
        $script = base_path('sendotp.py');

        try {
            $process = new Process([$python, $script, $email, $otp]);
            $process->setTimeout(25);
            $process->run();

            if (!$process->isSuccessful()) {
                \Log::error('OTP send failed: ' . $process->getErrorOutput());
                return back()->with('error', 'Failed to send OTP email. Try again.');
            }

            \Log::info("Password reset OTP sent successfully to {$email}");
            return redirect()->route('verify.otp.show')->with('success', 'OTP has been sent to your email.');
        } catch (\Throwable $e) {
            \Log::error('OTP exception: ' . $e->getMessage());
            return back()->with('error', 'Could not send OTP. Please try again.');
        }
    }
}
