<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class BookSubscriptionController extends Controller
{
    public function show(Request $request)
    {
        $userId = Auth::id();
        $subId = $request->query('sub_id');
        $planType = $request->query('plan_type');
        $selectedGenre = $request->query('genre', 'all');

        $subscription = DB::table('user_subscriptions as us')
            ->join('subscription_plans as sp', 'us.subscription_plan_id', '=', 'sp.plan_id')
            ->where('us.user_subscription_id', $subId)
            ->where('us.user_id', $userId)
            ->where('us.status', 'active')
            ->where('us.end_date', '>', now())
            ->select('us.*', 'sp.*')
            ->first();

        if (!$subscription) {
            return view('subscriptions.book_add', [
                'subscription' => null,
                'genres' => [],
                'rentBooks' => [],
                'selectedGenre' => $selectedGenre,
                'planType' => $planType,
            ]);
        }

        // days left
        $daysLeft = now()->diffInDays($subscription->end_date, false);

        // count books already added
        $booksUsed = DB::table('user_subscription_rent_book_access')
            ->where('user_subscription_id', $subId)
            ->count();

        $booksRemaining = $subscription->book_quantity - $booksUsed;
        $booksProgress = ($subscription->book_quantity > 0)
            ? ($booksUsed / $subscription->book_quantity) * 100
            : 0;

        // genres
        $genres = DB::table('rent_books')
            ->whereNotNull('genre')
            ->where('genre', '!=', '')
            ->distinct()
            ->orderBy('genre')
            ->pluck('genre')
            ->toArray();

        // books list
        $rentBooksQuery = DB::table('rent_books as rb')
            ->leftJoin('partner_books as pb', 'rb.rent_book_id', '=', 'pb.rent_book_id')
            ->select('rb.*')
            ->where(function ($q) {
                $q->whereNull('pb.status')->orWhere('pb.status', 'visible');
            });
        if ($selectedGenre !== 'all') {
            $rentBooksQuery->where('rb.genre', $selectedGenre);
        }
        $rentBooks = $rentBooksQuery->orderBy('rb.title')->get();

        return view('subscriptions.book_add', compact(
            'subscription',
            'genres',
            'rentBooks',
            'selectedGenre',
            'planType',
            'booksUsed',
            'booksRemaining',
            'booksProgress',
            'daysLeft'
        ));
    }

    public function addBook(Request $request)
    {
        $userId = Auth::id();
        $subId = $request->query('sub_id');
        $planType = $request->query('plan_type');
        $rentBookId = (int)$request->input('rent_book_id');

        $subscription = DB::table('user_subscriptions')->where('user_subscription_id', $subId)->first();
        if (!$subscription) return back()->with('error', 'Subscription not found.');

        $plan = DB::table('subscription_plans')->where('plan_id', $subscription->subscription_plan_id)->first();
        $booksUsed = DB::table('user_subscription_rent_book_access')->where('user_subscription_id', $subId)->count();

        if ($booksUsed >= $plan->book_quantity)
            return back()->with('error', 'You have reached your book limit for this period.');

        $exists = DB::table('user_subscription_rent_book_access')
            ->where('user_subscription_id', $subId)
            ->where('rent_book_id', $rentBookId)
            ->exists();

        if ($exists) return back()->with('error', 'This book is already in your subscription.');

        DB::beginTransaction();
        try {
            DB::table('user_subscription_rent_book_access')->insert([
                'user_subscription_id' => $subId,
                'rent_book_id' => $rentBookId,
                'access_date' => now(),
                'status' => 'borrowed',
                'user_id' => $userId,
            ]);

            DB::table('user_subscriptions')
                ->where('user_subscription_id', $subId)
                ->decrement('available_rent_book');

            DB::table('partner_books')
                ->where('rent_book_id', $rentBookId)
                ->update([
                    'status' => 'on rent',
                    'revenue' => DB::raw('COALESCE(revenue,0)+40'),
                ]);

            DB::commit();
            return redirect()->back()->with('message', 'Book added successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error adding book: ' . $e->getMessage());
        }
    }
}
