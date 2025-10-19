<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Process\Process;
use Carbon\Carbon;
use App\Models\User;

class AuthController extends Controller
{
    /** 🔹 Show combined login/register page */
    public function showLoginRegister()
    {
        return view('auth.login-register');
    }

    /** 🔹 Register user */
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        DB::beginTransaction();
        try {
            $userId = DB::table('users')->insertGetId([
                'username' => $request->username,
                'email'    => $request->email,
                'pass'     => Hash::make($request->password),
                'two_step_verification' => 0, // enable by default
            ]);

            DB::commit();
            return back()->with('success', 'Registration successful! Please log in.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Registration failed: ' . $e->getMessage());
        }
    }

    /** 🔹 Login with optional 2FA */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = DB::table('users')->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->pass)) {
            return back()->with('error', 'Invalid email or password.');
        }

        // ✅ If 2FA enabled
        if ($user->two_step_verification == 1) {
            $otp = (string) random_int(100000, 999999);

            DB::table('user_otp')->insert([
                'user_id'      => $user->user_id,
                'otp_code'     => $otp,
                'purpose'      => 'two-factor',
                'otp_attempts' => 0,
                'otp_time'     => now(),
            ]);

            Session::put('2fa_user_id', $user->user_id);
            Session::put('2fa_email', $user->email);

            $python = 'C:\\Users\\USER\\AppData\\Local\\Programs\\Python\\Python313\\python.exe';
            $script = base_path('sendotp.py');

            try {
                $process = new Process([$python, $script, $user->email, $otp]);
                $process->setTimeout(25);
                $process->run();

                if (!$process->isSuccessful()) {
                    \Log::error('2FA sendotp failed: ' . $process->getErrorOutput());
                } else {
                    \Log::info("2FA OTP sent successfully to {$user->email}");
                }
            } catch (\Throwable $e) {
                \Log::error('2FA OTP exception: ' . $e->getMessage());
            }

            return redirect()->route('verify.2fa')
                ->with('info', 'A 6-digit OTP has been sent to your email.');
        }

        // Normal login
        $userModel = User::find($user->user_id);
        Auth::login($userModel);
        return redirect()->intended(route('profile'))->with('success', 'Login successful!');
    }

    /** 🔹 Show OTP verification page */
    public function show2FA()
    {
        return view('auth.verify-2fa');
    }

    /** 🔹 Verify entered OTP */
    public function verify2FA(Request $request)
    {
        $request->validate(['otp_code' => 'required|digits:6']);

        $userId = Session::get('2fa_user_id');
        $row = DB::table('user_otp')
            ->where('user_id', $userId)
            ->where('purpose', 'two-factor')
            ->orderByDesc('otp_time')
            ->first();

        if (!$row) {
            return back()->withErrors(['otp_code' => 'No OTP found.']);
        }

        if ($row->otp_code !== $request->otp_code) {
            DB::table('user_otp')->where('id', $row->id)->increment('otp_attempts');
            return back()->withErrors(['otp_code' => 'Invalid OTP. Try again.']);
        }

        if (Carbon::parse($row->otp_time)->lt(now()->subMinutes(10))) {
            return back()->withErrors(['otp_code' => 'OTP expired.']);
        }

        // ✅ Success: delete old OTPs and log in
        DB::table('user_otp')
            ->where('user_id', $userId)
            ->where('purpose', 'two-factor')
            ->delete();

        $userModel = User::find($userId);
        Auth::login($userModel);
        Session::forget(['2fa_user_id', '2fa_email']);

        return redirect()->intended(route('profile'))->with('success', 'Login successful!');
    }

    /** 🔹 Logout */
    public function logout()
    {
        Auth::logout();
        Session::invalidate();
        Session::regenerateToken();
        return redirect('/login')->with('info', 'Logged out successfully.');
    }
}
