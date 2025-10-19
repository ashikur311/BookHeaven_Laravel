<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        if (!$userId) {
            return redirect()->route('login');
        }

        // 🔹 Fetch user's current subscriptions (both active and expired)
        $currentPlans = DB::table('user_subscriptions as us')
            ->join('subscription_plans as sp', 'us.subscription_plan_id', '=', 'sp.plan_id')
            ->select(
                'sp.*',
                'us.user_subscription_id',
                'us.start_date',
                'us.end_date',
                'us.available_audio',
                'us.available_rent_book',
                DB::raw('DATEDIFF(us.end_date, NOW()) AS days_left'),
                DB::raw('CASE WHEN us.end_date > NOW() THEN "active" ELSE "expired" END AS subscription_status')
            )
            ->where('us.user_id', $userId)
            ->whereIn('us.status', ['active', 'expired'])
            ->orderByDesc('us.end_date')
            ->get();

        // 🔹 Fetch all active subscription plans
        $plans = DB::table('subscription_plans')
            ->where('status', 'active')
            ->orderBy('price')
            ->get();

        return view('subscriptions.plan', compact('currentPlans', 'plans'));
    }

    public function showSubscriptions()
{
    $user = Auth::user();

    $subs = DB::table('user_subscriptions as us')
        ->join('subscription_plans as sp', 'us.subscription_plan_id', '=', 'sp.plan_id')
        ->select('us.*', 'sp.plan_name', 'sp.book_quantity', 'sp.audiobook_quantity')
        ->where('us.user_id', $user->user_id)
        ->get();

    // Attach related books & audiobooks
    foreach ($subs as $sub) {
        $sub->books = DB::table('user_subscription_rent_book_access as s')
            ->join('rent_books as rb', 'rb.rent_book_id', '=', 's.rent_book_id')
            ->where('s.user_subscription_id', $sub->user_subscription_id)
            ->select('rb.title', 'rb.writer', 'rb.genre', 'rb.language')
            ->get();

        $sub->audiobooks = DB::table('user_subscription_audiobook_access as sa')
            ->join('audiobooks as ab', 'ab.audiobook_id', '=', 'sa.audiobook_id')
            ->where('sa.user_subscription_id', $sub->user_subscription_id)
            ->select('ab.title', 'ab.writer', 'ab.genre', 'ab.language')
            ->get();
    }

    $stats = [
        'total' => count($subs),
        'active' => $subs->where('status', 'active')->count(),
        'expired' => $subs->where('status', 'expired')->count(),
        'renew_needed' => 0,
    ];

    return view('profile.subscriptions', compact('user', 'stats', 'subs'))
        ->with('activeSubs', $subs->where('status', 'active'));
}




    public function audioAddToSubscription(Request $request)
{
    $userId = Auth::id();
    $subId = $request->query('sub_id');
    $planType = $request->query('plan_type');
    $selectedGenre = $request->query('genre', 'all');

    // Fetch user's active subscription
    $subscription = DB::table('user_subscriptions as us')
        ->join('subscription_plans as sp', 'us.subscription_plan_id', '=', 'sp.plan_id')
        ->where('us.user_subscription_id', $subId)
        ->where('us.user_id', $userId)
        ->where('us.status', 'active')
        ->where('us.end_date', '>', now())
        ->select('us.*', 'sp.plan_name', 'sp.audiobook_quantity')
        ->first();

    if (!$subscription) {
        return redirect()->route('subscriptions.index')
            ->with('error', 'Invalid or expired subscription.');
    }

    // Used audiobooks
    $usedAudio = DB::table('user_subscription_audiobook_access')
        ->where('user_subscription_id', $subscription->user_subscription_id)
        ->where('status', 'borrowed')
        ->count();

    $subscription->used_audio = $usedAudio;
    $subscription->remaining_audio = max(0, $subscription->audiobook_quantity - $usedAudio);
    $subscription->days_left = now()->diffInDays($subscription->end_date);

    // Genres
    $genres = DB::table('audiobooks')
        ->where('status', 'visible')
        ->distinct()
        ->pluck('genre');

    // Audiobooks by genre
    $audiobooks = DB::table('audiobooks')
        ->when($selectedGenre !== 'all', fn($q) => $q->where('genre', $selectedGenre))
        ->where('status', 'visible')
        ->orderBy('title', 'asc')
        ->get()
        ->map(function ($book) use ($subscription) {
            $book->already_added = DB::table('user_subscription_audiobook_access')
                ->where('user_subscription_id', $subscription->user_subscription_id)
                ->where('audiobook_id', $book->audiobook_id)
                ->where('status', 'borrowed')
                ->exists();
            return $book;
        });

    return view('subscriptions.audio_add', compact(
        'subscription', 'subId', 'planType', 'genres', 'selectedGenre', 'audiobooks'
    ));
}

public function storeAudioToSubscription(Request $request)
{
    $request->validate([
        'sub_id' => 'required|integer',
        'audiobook_id' => 'required|integer'
    ]);

    $userId = Auth::id();
    $subId = $request->input('sub_id');
    $audiobookId = $request->input('audiobook_id');

    $subscription = DB::table('user_subscriptions as us')
        ->join('subscription_plans as sp', 'us.subscription_plan_id', '=', 'sp.plan_id')
        ->where('us.user_subscription_id', $subId)
        ->where('us.user_id', $userId)
        ->where('us.status', 'active')
        ->where('us.end_date', '>', now())
        ->select('us.*', 'sp.audiobook_quantity')
        ->first();

    if (!$subscription) {
        return back()->with('error', 'Subscription not found or expired.');
    }

    $usedCount = DB::table('user_subscription_audiobook_access')
        ->where('user_subscription_id', $subId)
        ->where('status', 'borrowed')
        ->count();

    if ($usedCount >= $subscription->audiobook_quantity) {
        return back()->with('error', 'You have reached your monthly audiobook limit.');
    }

    $already = DB::table('user_subscription_audiobook_access')
        ->where('user_subscription_id', $subId)
        ->where('audiobook_id', $audiobookId)
        ->where('status', 'borrowed')
        ->exists();

    if ($already) {
        return back()->with('error', 'This audiobook is already added.');
    }

    DB::table('user_subscription_audiobook_access')->insert([
        'user_subscription_id' => $subId,
        'audiobook_id' => $audiobookId,
        'access_date' => now(),
        'status' => 'borrowed',
        'user_id' => $userId,
    ]);

    DB::table('user_subscriptions')
        ->where('user_subscription_id', $subId)
        ->decrement('available_audio');

    return back()->with('message', 'Audiobook added to your subscription successfully!');
}

}
