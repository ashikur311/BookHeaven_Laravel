<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Exception;

class SettingsController extends Controller
{
    /** ───────────────────────────────
     *  Show Settings Page
     *  ─────────────────────────────── */
    public function index()
    {
        $userId = Auth::id();

        $user = DB::table('users as u')
            ->leftJoin('user_info as ui', 'u.user_id', '=', 'ui.user_id')
            ->where('u.user_id', $userId)
            ->select(
                'u.*',
                'ui.phone',
                'ui.birthday',
                'ui.address',
                DB::raw('u.create_time as created_at') // ✅ Alias for Blade compatibility
            )
            ->first();

        $billing = DB::table('user_billing_address')->where('user_id', $userId)->first();
        $payments = DB::table('user_payment_methods')->where('user_id', $userId)->get();

        $divisions = [
            'Dhaka', 'Chittagong', 'Rajshahi', 'Khulna',
            'Barishal', 'Sylhet', 'Rangpur', 'Mymensingh'
        ];

        return view('profile.settings', compact('user', 'billing', 'payments', 'divisions'));
    }

    /** ───────────────────────────────
     *  Update Profile Information
     *  ─────────────────────────────── */
    public function updateProfile(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'email' => 'required|email',
            'username' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'birthday' => 'nullable|date',
            'address' => 'nullable|string',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048'
        ]);

        try {
            $user = DB::table('users')->where('user_id', $userId)->first();
            $profilePath = $user->user_profile;

            // ✅ Handle profile image upload
            if ($request->hasFile('profile_image')) {
                // Delete old image if exists
                if (!str_starts_with($profilePath ?? '', 'https://') &&
                    $profilePath &&
                    Storage::exists(str_replace('storage/', 'public/', $profilePath))) {
                    Storage::delete(str_replace('storage/', 'public/', $profilePath));
                }

                $path = $request->file('profile_image')->store('public/user_images');
                $profilePath = str_replace('public/', 'storage/', $path);
            }

            DB::beginTransaction();

            DB::table('users')
                ->where('user_id', $userId)
                ->update([
                    'username' => $request->username,
                    'email' => $request->email,
                    'user_profile' => $profilePath
                ]);

            DB::table('user_info')->updateOrInsert(
                ['user_id' => $userId],
                [
                    'phone' => $request->phone,
                    'birthday' => $request->birthday,
                    'address' => $request->address
                ]
            );

            DB::commit();
            return back()->with('success', 'Profile updated successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating profile: ' . $e->getMessage());
        }
    }

    /** ───────────────────────────────
     *  Remove Profile Image
     *  ─────────────────────────────── */
    public function removeProfileImage()
    {
        $userId = Auth::id();
        $user = DB::table('users')->where('user_id', $userId)->first();

        if (!empty($user->user_profile) && !str_starts_with($user->user_profile, 'https://')) {
            $path = str_replace('storage/', 'public/', $user->user_profile);
            if (Storage::exists($path)) Storage::delete($path);
        }

        DB::table('users')->where('user_id', $userId)->update([
            'user_profile' => 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80'
        ]);

        return back()->with('success', 'Profile photo removed successfully!');
    }

    /** ───────────────────────────────
     *  Change Password
     *  ─────────────────────────────── */
    public function changePassword(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed'
        ]);

        $user = DB::table('users')->where('user_id', $userId)->first();

        if (!Hash::check($request->current_password, $user->pass)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        DB::table('users')->where('user_id', $userId)
            ->update(['pass' => Hash::make($request->new_password)]);

        Auth::logout();
        return redirect()->route('login')->with('success', 'Password changed successfully. Please log in again.');
    }

    /** ───────────────────────────────
     *  Update Billing Address
     *  ─────────────────────────────── */
    public function updateBilling(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'street_address' => 'required|string',
            'city' => 'required|string',
            'division' => 'required|string',
            'zip_code' => 'required|string',
        ]);

        DB::table('user_billing_address')->updateOrInsert(
            ['user_id' => $userId],
            [
                'street_address' => $request->street_address,
                'city' => $request->city,
                'division' => $request->division,
                'zip_code' => $request->zip_code,
                'country' => 'Bangladesh'
            ]
        );

        return back()->with('success', 'Billing address updated successfully!');
    }

    /** ───────────────────────────────
     *  Add Payment Method
     *  ─────────────────────────────── */
    public function addPayment(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'card_type' => 'required|string',
            'card_number' => 'required|digits:16',
            'card_name' => 'required|string',
            'expiry_date' => 'required|string',
            'cvv' => 'required|digits_between:3,4'
        ]);

        DB::table('user_payment_methods')->insert([
            'user_id' => $userId,
            'card_type' => $request->card_type,
            'card_number' => $request->card_number,
            'card_name' => $request->card_name,
            'expiry_date' => $request->expiry_date,
            'cvv' => $request->cvv,
        ]);

        return back()->with('success', 'Payment method added successfully!');
    }

    /** ───────────────────────────────
     *  Delete Payment Method
     *  ─────────────────────────────── */
    public function deletePayment($id)
    {
        $userId = Auth::id();
        DB::table('user_payment_methods')->where('id', $id)->where('user_id', $userId)->delete();
        return back()->with('success', 'Payment method deleted successfully!');
    }

    /** ───────────────────────────────
     *  Update Two-Step Verification
     *  ─────────────────────────────── */
    public function updateVerification(Request $request)
    {
        $userId = Auth::id();
        $enabled = $request->has('two_step_verification') ? 1 : 0;

        DB::table('users')->where('user_id', $userId)->update(['two_step_verification' => $enabled]);

        return back()->with('success', 'Two-step verification settings updated!');
    }
}
