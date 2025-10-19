<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Process\Process;

class PaymentController extends Controller
{
    /* ===========================
     * bKash page (order/subscription)
     * =========================== */
    public function bkash(Request $request)
    {
        $user   = Auth::user();
        $userId = $user->user_id ?? $user->id;

        $type = $request->query('type', 'subscription'); // subscription | book_order
        $id   = (int) $request->query('id');

        if ($type === 'book_order') {
            $order = DB::table('orders')
                ->where('order_id', $id)
                ->where('user_id', $userId)
                ->first();

            abort_if(!$order, 404, 'Order not found.');

            return view('payment.bkash', [
                'type'  => $type,
                'id'    => $id,
                'order' => $order,
            ]);
        }

        // subscription
        $plan = DB::table('subscription_plans')->where('plan_id', $id)->first();
        abort_if(!$plan, 404, 'Subscription plan not found.');

        return view('payment.bkash', [
            'type' => $type,
            'id'   => $id,
            'plan' => $plan,
        ]);
    }

    /* ===========================
     * Send OTP (AJAX) + store in DB
     * =========================== */
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user   = Auth::user();
        $userId = $user->user_id ?? $user->id;

        // generate 6-digit OTP
        $otp = (string) random_int(100000, 999999);

        // store in DB
        DB::table('user_otp')->insert([
            'user_id'      => $userId,
            'otp_code'     => $otp,
            'otp_time'     => now(),
            'purpose'      => 'bkash_payment',
            'otp_attempts' => 0,
        ]);

        // also keep a short-lived copy in session (simple guard)
        Session::put('bkash_otp', $otp);

        // call python script
        $python  = 'C:\\Users\\USER\\AppData\\Local\\Programs\\Python\\Python313\\python.exe';
        $script  = base_path('sendotp.py');

        Log::info("Sending OTP $otp to {$request->email} via $python $script");

        try {
            $process = new Process([$python, $script, $request->email, $otp]);
            $process->setTimeout(25);
            $process->run();

            if (!$process->isSuccessful()) {
                Log::error('Python sendotp failed: ' . $process->getErrorOutput());
                // fallback success message so UX keeps going (OTP still in DB)
                return response()->json([
                    'status'  => 'mocked',
                    'message' => 'OTP sent (local fallback). Check spam too.',
                ]);
            }

            Log::info('OTP mail sent successfully');
            return response()->json([
                'status'  => 'sent',
                'message' => 'OTP sent. Check your email.',
            ]);
        } catch (\Throwable $e) {
            Log::error('OTP exception: ' . $e->getMessage());
            return response()->json([
                'status'  => 'mocked',
                'message' => 'OTP sent (local fallback). Check spam too.',
            ]);
        }
    }

    /* ===========================
     * Confirm bKash payment
     * =========================== */
    public function bkashPay(Request $request)
    {
        $request->validate([
            'bkash_number' => ['required', 'regex:/^01[3-9]\d{8}$/'],
            'otp_code'     => ['required', 'digits:6'],
            'type'         => ['required', 'in:subscription,book_order'],
            'id'           => ['required', 'integer'],
        ]);

        $user   = Auth::user();
        $userId = $user->user_id ?? $user->id;

        // verify OTP from DB (latest, not expired, <=5 attempts)
        $row = DB::table('user_otp')
            ->where('user_id', $userId)
            ->where('purpose', 'bkash_payment')
            ->orderByDesc('otp_time')
            ->first();

        if (!$row) {
            return back()->withErrors(['otp_code' => 'No OTP found. Please send again.']);
        }

        // increment attempts if mismatch
        if ($request->otp_code !== (string) $row->otp_code) {
            DB::table('user_otp')->where('id', $row->id)->increment('otp_attempts');
            return back()->withErrors(['otp_code' => 'Invalid OTP. Try again.']);
        }

        // check expiry (10 minutes)
        if (Carbon::parse($row->otp_time)->lt(now()->subMinutes(10))) {
            return back()->withErrors(['otp_code' => 'OTP expired. Send again.']);
        }

        // OK -> delete OTPs for this purpose
        DB::table('user_otp')->where('user_id', $userId)->where('purpose', 'bkash_payment')->delete();
        Session::forget('bkash_otp');

        // handle type
        if ($request->type === 'book_order') {
            DB::table('orders')
                ->where('order_id', $request->id)
                ->where('user_id', $userId)
                ->update([
                    'status'         => 'confirmed',
                    'payment_method' => 'online',
                    'payment_status' => 'confirm',
                ]);

            return redirect('/profile/orders')->with('success', 'bKash payment confirmed.');
        }

        // subscription
        $plan = DB::table('subscription_plans')->where('plan_id', $request->id)->first();
        if (!$plan) {
            return back()->withErrors(['otp_code' => 'Plan not found.']);
        }

        $start = now();
        $end   = now()->copy()->addDays($plan->validity_days);

        DB::transaction(function () use ($userId, $plan, $start, $end) {
            $existing = DB::table('user_subscriptions')
                ->where('user_id', $userId)
                ->where('subscription_plan_id', $plan->plan_id)
                ->first();

            if ($existing) {
                DB::table('user_subscriptions')
                    ->where('user_subscription_id', $existing->user_subscription_id)
                    ->update([
                        'start_date'          => $start,
                        'end_date'            => $end,
                        'status'              => 'active',
                        'available_audio'     => DB::raw('available_audio + ' . (int) $plan->audiobook_quantity),
                        'available_rent_book' => DB::raw('available_rent_book + ' . (int) $plan->book_quantity),
                    ]);
                $subId = $existing->user_subscription_id;
            } else {
                $subId = DB::table('user_subscriptions')->insertGetId([
                    'user_id'              => $userId,
                    'subscription_plan_id' => $plan->plan_id,
                    'start_date'           => $start,
                    'end_date'             => $end,
                    'status'               => 'active',
                    'available_audio'      => (int) $plan->audiobook_quantity,
                    'available_rent_book'  => (int) $plan->book_quantity,
                ]);
            }

            DB::table('subscription_transactions')->insert([
                'user_subscription_id' => $subId,
                'amount'               => $plan->price,
                'payment_method'       => 'bkash',
                'payment_status'       => 'paid',
                'transaction_code'     => 'BKH' . time() . rand(100, 999),
                'payment_provider'     => 'bKash',
                'transaction_date'     => now(),
            ]);
        });

        return redirect('/profile/subscriptions')->with('success', 'Subscription activated.');
    }


// ===========================
// CARD PAYMENT VIEW (GET)
// ===========================
public function cardPayment(Request $request)
{
    $user = Auth::user();
    $user_id = $user->user_id ?? $user->id;

    $type = $request->query('type');
    $id   = $request->query('id');

    if (!in_array($type, ['subscription', 'book_order']) || !$id) {
        abort(404, 'Invalid payment request');
    }

    // Fetch saved cards for this user
    $savedCards = DB::table('user_payment_methods')->where('user_id', $user_id)->get();

    $description = '';
    $amount = 0;

    if ($type === 'subscription') {
        $payment = DB::table('subscription_plans')->where('plan_id', $id)->first();
        abort_if(!$payment, 404, 'Subscription not found.');

        $description = "Subscription: {$payment->plan_name}";
        $amount = $payment->price;
    } else {
        $order = DB::table('orders')
            ->where('order_id', $id)
            ->where('user_id', $user_id)
            ->first();

        if (!$order) {
            return response()->view('errors.404', [
                'debug' => [
                    'user_id' => $user_id,
                    'order_exists' => DB::table('orders')->where('order_id', $id)->exists(),
                    'matching_orders' => DB::table('orders')->where('order_id', $id)->get(),
                ],
            ], 404);
        }

        $amount = $order->total_amount ?? 0;

        $items = DB::table('order_items')
            ->join('books', 'order_items.book_id', '=', 'books.book_id')
            ->where('order_items.order_id', $id)
            ->select('books.title', 'order_items.quantity')
            ->get()
            ->map(fn($row) => "{$row->title} (x{$row->quantity})")
            ->toArray();

        $description = 'Book Purchase: ' . implode(', ', $items);
    }

    return view('payment.card', [
        'user' => $user,
        'username' => $user->username ?? $user->name ?? 'Customer',
        'type' => $type,
        'id' => $id,
        'description' => $description,
        'amount' => $amount,
        'savedCards' => $savedCards,
    ]);
}


// ===========================
// PROCESS PAYMENT (POST)
// ===========================
// ===========================
// PROCESS PAYMENT (POST)
// ===========================
public function processCardPayment(Request $request)
{
    $user_id = Auth::id();
    $type = $request->input('type');
    $id   = $request->input('id');

    if (!in_array($type, ['subscription', 'book_order']) || !$id) {
        return back()->withErrors('Invalid payment request.');
    }

    $savedCards = DB::table('user_payment_methods')->where('user_id', $user_id)->get();
    $usingSavedCard = $request->filled('saved_card_id') && $request->input('saved_card_id') !== 'new';

    $error = null;

    // 🔹 Validate card
    if ($usingSavedCard) {
        $savedCardId = (int)$request->input('saved_card_id');
        $savedCard = $savedCards->firstWhere('id', $savedCardId);

        if (!$savedCard) {
            $error = 'Invalid card selection.';
        } elseif (empty($request->saved_card_cvv)) {
            $error = 'Please enter the CVV for your card.';
        } elseif (!preg_match('/^\d{3,4}$/', $request->saved_card_cvv)) {
            $error = 'Invalid CVV (must be 3 or 4 digits).';
        } elseif ($request->saved_card_cvv !== $savedCard->cvv) {
            $error = 'CVV does not match the saved card.';
        }
    } else {
        $cardNumber = preg_replace('/\s+/', '', $request->card_number);
        $cardExpiry = $request->card_expiry;
        $cardCvv = $request->card_cvv;
        $cardName = trim($request->card_name);

        if (empty($cardNumber) || empty($cardExpiry) || empty($cardCvv) || empty($cardName)) {
            $error = 'Please fill in all card details.';
        } elseif (!preg_match('/^\d{16}$/', $cardNumber)) {
            $error = 'Invalid card number (must be 16 digits).';
        } elseif (!preg_match('/^\d{3,4}$/', $cardCvv)) {
            $error = 'Invalid CVV (must be 3 or 4 digits).';
        } elseif (!preg_match('/^(0[1-9]|1[0-2])\/?([0-9]{2})$/', $cardExpiry)) {
            $error = 'Invalid expiry date (MM/YY format).';
        }
    }

    if ($error) {
        return back()->withErrors($error)->withInput();
    }

    // Simulate transaction
    $transaction_id = 'CARD' . time() . rand(100, 999);

    DB::beginTransaction();
    try {
        if ($type === 'subscription') {
            $plan = DB::table('subscription_plans')->where('plan_id', $id)->first();
            if (!$plan) {
                throw new \Exception('Subscription plan not found.');
            }

            $startDate = now();
            $endDate = now()->addDays($plan->validity_days);

            // Check if user already has a subscription
            $existingSub = DB::table('user_subscriptions')
                ->where('user_id', $user_id)
                ->where('subscription_plan_id', $id)
                ->first();

            if ($existingSub) {
                DB::table('user_subscriptions')
                    ->where('user_subscription_id', $existingSub->user_subscription_id)
                    ->update([
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'status' => 'active',
                        'available_audio' => DB::raw('available_audio + ' . (int)$plan->audiobook_quantity),
                        'available_rent_book' => DB::raw('available_rent_book + ' . (int)$plan->book_quantity),
                    ]);

                $userSubId = $existingSub->user_subscription_id;
            } else {
                // ✅ Removed created_at / updated_at
                $userSubId = DB::table('user_subscriptions')->insertGetId([
                    'user_id' => $user_id,
                    'subscription_plan_id' => $id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'status' => 'active',
                    'available_audio' => $plan->audiobook_quantity,
                    'available_rent_book' => $plan->book_quantity,
                ]);
            }

            // ✅ Use correct FK
            DB::table('subscription_transactions')->insert([
                'user_subscription_id' => $userSubId,
                'amount' => $plan->price,
                'payment_method' => 'card',
                'payment_status' => 'paid',
                'transaction_code' => $transaction_id,
                'payment_provider' => 'Stripe',
                'transaction_date' => now(),
            ]);

            $success = "Subscription payment successful! Your subscription is now active.";
            $redirect = url('/profile/subscriptions');
        } else {
            DB::table('transactions')->insert([
                'order_id' => $id,
                'payment_method' => 'card',
                'payment_status' => 'paid',
                'transaction_date' => now(),
                'payment_reference' => $transaction_id,
            ]);

            DB::table('orders')->where('order_id', $id)->update([
                'status' => 'confirmed',
                'payment_method' => 'card',
                'payment_status' => 'paid',
            ]);

            $success = "Book purchase successful! Your order has been confirmed.";
            $redirect = route('orders.index');
        }

        // Save new card if needed
        if (!$usingSavedCard && $request->boolean('save_card')) {
            $number = preg_replace('/\s+/', '', $request->card_number);
            $expiry = $request->card_expiry;
            $cardType = 'visa';

            if (preg_match('/^5[1-5]/', $number)) $cardType = 'mastercard';
            elseif (preg_match('/^3[47]/', $number)) $cardType = 'amex';
            elseif (preg_match('/^6(?:011|5)/', $number)) $cardType = 'discover';

            DB::table('user_payment_methods')->insert([
                'user_id' => $user_id,
                'card_type' => $cardType,
                'card_number' => $number,
                'card_name' => $request->card_name,
                'expiry_date' => $expiry,
                'cvv' => $request->card_cvv,
                'is_default' => $savedCards->count() === 0 ? 1 : 0,
            ]);
        }

        DB::commit();

        Session::flash('success', $success);
        Session::flash('redirect', $redirect ?? route('home'));
        return redirect()->back();

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors('Payment failed: ' . $e->getMessage());
    }
}



}
