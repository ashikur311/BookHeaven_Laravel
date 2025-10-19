<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportsController extends Controller
{
    public function index()
    {
        try {
            // ---------- KEY STATS ----------
            $stats = [
                'total_users'         => (int) DB::table('users')->count(),
                'total_books'         => (int) DB::table('books')->count(),
                'total_audiobooks'    => (int) DB::table('audiobooks')->count(),
                'total_orders'        => (int) DB::table('orders')->count(),
                'total_revenue'       => (float) (DB::table('orders')->where('status', 'delivered')->sum('total_amount') ?? 0),
                'active_subscriptions'=> (int) DB::table('user_subscriptions')->where('status', 'active')->count(),
                'total_writers'       => (int) DB::table('writers')->count(),
                'total_events'        => (int) DB::table('events')->count(),
            ];

            // ---------- SALES (last 6 months) ----------
            $salesRows = DB::table('orders')
                ->selectRaw("DATE_FORMAT(order_date, '%Y-%m') AS month")
                ->selectRaw('COUNT(*) AS order_count')
                ->selectRaw('SUM(total_amount) AS revenue')
                ->where('order_date', '>=', DB::raw("DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)"))
                ->groupBy(DB::raw("DATE_FORMAT(order_date, '%Y-%m')"))
                ->orderBy('month')
                ->get();

            $sales_chart = ['labels' => [], 'orders' => [], 'revenue' => []];
            foreach ($salesRows as $r) {
                $sales_chart['labels'][] = Carbon::parse($r->month . '-01')->format('M Y');
                $sales_chart['orders'][] = (int) $r->order_count;
                $sales_chart['revenue'][] = (float) ($r->revenue ?? 0);
            }

            // ---------- SUBSCRIPTION PERFORMANCE (last 6 months) ----------
            $subscription_monthly_data = DB::table('subscription_orders as so')
                ->selectRaw("DATE_FORMAT(so.issue_date, '%Y-%m') AS month")
                ->selectRaw("COUNT(CASE WHEN so.status = 'active' THEN 1 END) AS new_subs")
                ->selectRaw("COUNT(CASE WHEN so.status = 'renewed' THEN 1 END) AS renewals")
                ->selectRaw("SUM(so.amount) AS revenue")
                ->where('so.issue_date', '>=', DB::raw("DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)"))
                ->groupBy(DB::raw("DATE_FORMAT(so.issue_date, '%Y-%m')"))
                ->orderBy('month')
                ->get()
                ->map(function ($r) {
                    return [
                        'month'     => $r->month,
                        'new_subs'  => (int) $r->new_subs,
                        'renewals'  => (int) $r->renewals,
                        'revenue'   => (float) ($r->revenue ?? 0),
                    ];
                })->toArray();

            // ---------- BOOK CATEGORIES (Top 10) ----------
            $categories_data = DB::table('book_categories as bc')
                ->join('categories as c', 'bc.category_id', '=', 'c.id')
                ->select('c.name as category', DB::raw('COUNT(bc.book_id) as book_count'))
                ->groupBy('bc.category_id', 'c.name')
                ->orderByDesc('book_count')
                ->limit(10)
                ->get()
                ->map(fn($r) => ['category' => $r->category, 'book_count' => (int) $r->book_count])
                ->toArray();

            // ---------- BOOK GENRES (Top 10) ----------
            $genres_data = DB::table('book_genres as bg')
                ->join('genres as g', 'bg.genre_id', '=', 'g.genre_id')
                ->select('g.name as genre', DB::raw('COUNT(bg.book_id) as book_count'))
                ->groupBy('bg.genre_id', 'g.name')
                ->orderByDesc('book_count')
                ->limit(10)
                ->get()
                ->map(fn($r) => ['genre' => $r->genre, 'book_count' => (int) $r->book_count])
                ->toArray();

            // ---------- TOP SELLING BOOKS ----------
            $top_books = DB::table('books as b')
                ->leftJoin('order_items as oi', 'b.book_id', '=', 'oi.book_id')
                ->leftJoin('reviews as r', 'b.book_id', '=', 'r.book_id')
                ->select(
                    'b.title',
                    'b.price',
                    DB::raw('COUNT(oi.id) as sales_count'),
                    DB::raw('SUM(oi.quantity) as total_quantity'),
                    DB::raw('AVG(r.rating) as avg_rating')
                )
                ->groupBy('b.book_id', 'b.title', 'b.price')
                ->orderByDesc('sales_count')
                ->limit(5)
                ->get()
                ->map(function ($r) {
                    return [
                        'title'          => $r->title,
                        'price'          => (float) ($r->price ?? 0),
                        'sales_count'    => (int) ($r->sales_count ?? 0),
                        'total_quantity' => (int) ($r->total_quantity ?? 0),
                        'avg_rating'     => $r->avg_rating ? round((float) $r->avg_rating, 2) : null,
                    ];
                })->toArray();

            // ---------- USER ACTIVITY (Top 5 by orders) ----------
            $user_activity = DB::table('users as u')
                ->leftJoin('orders as o', 'u.user_id', '=', 'o.user_id')
                ->leftJoin('reviews as r', 'u.user_id', '=', 'r.user_id')
                ->leftJoin('wishlist as w', 'u.user_id', '=', 'w.user_id')
                ->leftJoin('user_activities as ua', 'u.user_id', '=', 'ua.user_id')
                ->select(
                    'u.username',
                    DB::raw('COUNT(DISTINCT o.order_id) AS order_count'),
                    DB::raw('COUNT(DISTINCT r.review_id) AS review_count'),
                    DB::raw('COUNT(DISTINCT w.id) AS wishlist_count'),
                    DB::raw('MAX(ua.login_timestamp) AS last_login')
                )
                ->groupBy('u.user_id', 'u.username')
                ->orderByDesc('order_count')
                ->limit(5)
                ->get()
                ->map(function ($r) {
                    return [
                        'username'       => $r->username,
                        'order_count'    => (int) $r->order_count,
                        'review_count'   => (int) $r->review_count,
                        'wishlist_count' => (int) $r->wishlist_count,
                        'last_login'     => $r->last_login, // format in view
                    ];
                })->toArray();

            // ---------- EVENT PARTICIPATION (Recent 5) ----------
            $event_participation = DB::table('events as e')
                ->leftJoin('event_participants as ep', 'e.event_id', '=', 'ep.event_id')
                ->select('e.name as event_name', 'e.event_date', DB::raw('COUNT(ep.id) as participant_count'))
                ->groupBy('e.event_id', 'e.name', 'e.event_date')
                ->orderByDesc('e.event_date')
                ->limit(5)
                ->get()
                ->map(fn($r) => [
                    'event_name'        => $r->event_name,
                    'event_date'        => $r->event_date,
                    'participant_count' => (int) $r->participant_count,
                ])
                ->toArray();

            return view('admin.reports', compact(
                'stats',
                'sales_chart',
                'subscription_monthly_data',
                'categories_data',
                'genres_data',
                'top_books',
                'user_activity',
                'event_participation'
            ));
        } catch (\Throwable $e) {
            // Surface the error to the UI and keep going to a minimal view if needed
            return back()->with('error_message', 'Error generating reports: ' . $e->getMessage());
        }
    }
}
