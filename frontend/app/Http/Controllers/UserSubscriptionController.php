<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserSubscriptionController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userId = Auth::id();

        // Fetch user info for sidebar
        $user = DB::table('users as u')
            ->leftJoin('user_info as ui', 'u.user_id', '=', 'ui.user_id')
            ->where('u.user_id', $userId)
            ->select('u.username', 'u.user_profile', 'u.create_time', 'ui.*')
            ->first();

        // Fetch all subscriptions for this user
        $subscriptions = DB::table('user_subscriptions as us')
            ->join('subscription_plans as sp', 'us.subscription_plan_id', '=', 'sp.plan_id')
            ->where('us.user_id', $userId)
            ->select(
                'us.user_subscription_id',
                'us.subscription_plan_id',
                'us.start_date',
                'us.end_date',
                'us.status',
                'us.available_audio',
                'us.available_rent_book',
                'us.used_audio_book',
                'us.used_rent_book',
                'sp.plan_name',
                'sp.price',
                'sp.validity_days',
                'sp.book_quantity',
                'sp.audiobook_quantity'
            )
            ->get();

        // Separate active & expired
        $activeSubs = collect();
        $expiredSubs = collect();

        foreach ($subscriptions as $sub) {
            $status = Carbon::parse($sub->end_date)->isFuture() ? 'active' : 'expired';
            if ($status === 'active') {
                $activeSubs->push($sub);
            } else {
                $expiredSubs->push($sub);
            }
        }

        // Stats
        $stats = [
            'total' => $subscriptions->count(),
            'active' => $activeSubs->count(),
            'expired' => $expiredSubs->count(),
            'renew_needed' => 0
        ];

        // Fetch related Books & Audiobooks
        $booksBySub = [];
        $audiobooksBySub = [];

        foreach ($activeSubs as $sub) {
            $booksBySub[$sub->user_subscription_id] = DB::table('rent_books as rb')
                ->join('user_subscription_rent_book_access as usrba', 'rb.rent_book_id', '=', 'usrba.rent_book_id')
                ->where('usrba.user_subscription_id', $sub->user_subscription_id)
                ->select('rb.rent_book_id', 'rb.title', 'rb.writer', 'rb.genre', 'rb.language', 'rb.poster_url')
                ->get();

            $audiobooksBySub[$sub->user_subscription_id] = DB::table('audiobooks as ab')
                ->join('user_subscription_audiobook_access as usaaa', 'ab.audiobook_id', '=', 'usaaa.audiobook_id')
                ->where('usaaa.user_subscription_id', $sub->user_subscription_id)
                ->select('ab.audiobook_id', 'ab.title', 'ab.writer', 'ab.genre', 'ab.language', 'ab.audio_url')
                ->get();
        }

        return view('user.subscription', compact(
            'user',
            'activeSubs',
            'expiredSubs',
            'stats',
            'booksBySub',
            'audiobooksBySub'
        ));
    }
}
