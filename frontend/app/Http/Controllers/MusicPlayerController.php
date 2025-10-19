<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MusicPlayerController extends Controller
{
    /**
     * Require login and show the player
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please log in to access your audiobooks.');
        }

        return view('music.player');
    }

    /**
     * Return user's audiobooks as JSON
     */
    public function getUserAudiobooks()
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Not logged in'], 401);
        }

        $userId = Auth::id();

        try {
            // Fetch user subscriptions
            $subscriptions = DB::table('user_subscriptions as us')
                ->join('subscription_plans as sp', 'us.subscription_plan_id', '=', 'sp.plan_id')
                ->where('us.user_id', $userId)
                ->where('us.status', 'active')
                ->where('us.end_date', '>', now())
                ->select('us.user_subscription_id', 'sp.plan_name')
                ->get();

            if ($subscriptions->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active subscriptions found.',
                    'audiobooks' => [],
                ]);
            }

            $subscriptionIds = $subscriptions->pluck('user_subscription_id');

            $audiobooks = DB::table('audiobooks as ab')
                ->join('user_subscription_audiobook_access as access', 'ab.audiobook_id', '=', 'access.audiobook_id')
                ->whereIn('access.user_subscription_id', $subscriptionIds)
                ->where('access.status', 'borrowed')
                ->select('ab.audiobook_id', 'ab.title', 'ab.writer', 'ab.poster_url', 'ab.audio_url')
                ->orderBy('ab.title', 'asc')
                ->get()
                ->map(function ($book) {
                    $book->poster_url = $this->publicUrl($book->poster_url, 'assets/audiobook_covers', url('assets/default-audiobook-cover.png'));
                    $book->audio_url  = $this->publicUrl($book->audio_url, 'assets/audiobooks', null);
                    return $book;
                });

            return response()->json(['success' => true, 'audiobooks' => $audiobooks]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage(),
            ], 500);
        }
    }

// app/Http/Controllers/MusicPlayerController.php
private function publicUrl(?string $path, string $folder, ?string $fallback = null): ?string
{
    if (!$path) return $fallback;

    // Normalize slashes
    $p = str_replace('\\', '/', trim($path));

    // Already full URL (correct)
    if (preg_match('#^https?://#i', $p)) {
        return $p;
    }

    // 🚫 Remove any nested BookHeaven2.0 or old folder prefixes
    $p = preg_replace('#.*?/assets/#', '', $p); // keep only path after /assets/

    // ✅ Final URL always points to public/assets
    return url('assets/' . ltrim($p, '/'));
}

}
