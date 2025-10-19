<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function index()
    {
        // Ensure user is logged in
        if (!Auth::check()) {
            return redirect('/login');
        }

        $userId = Auth::id();

        // Fetch user basic & extended info
        $user = DB::table('users as u')
            ->leftJoin('user_info as ui', 'u.user_id', '=', 'ui.user_id')
            ->where('u.user_id', $userId)
            ->select('u.*', 'ui.*')
            ->first();

        // Total books purchased
        $totalBooks = DB::table('orders as o')
            ->join('order_items as oi', 'o.order_id', '=', 'oi.order_id')
            ->where('o.user_id', $userId)
            ->whereIn('o.status', ['shipped', 'delivered'])
            ->sum('oi.quantity');

        // Active subscriptions
        $activeSubs = DB::table('user_subscriptions')
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->count();

        // Partner status
        $partner = DB::table('partners')->where('user_id', $userId)->value('status');
        $partnerStatus = $partner ? ucfirst($partner) : 'Not a partner';

        // Total spent
        $totalSpent = DB::table('orders')
            ->where('user_id', $userId)
            ->whereIn('status', ['shipped', 'delivered'])
            ->sum('total_amount');

        // Monthly purchase chart data
        $monthlyRaw = DB::table('orders as o')
            ->join('order_items as oi', 'o.order_id', '=', 'oi.order_id')
            ->selectRaw('MONTH(order_date) as month, SUM(oi.quantity) as books_purchased')
            ->where('o.user_id', $userId)
            ->whereIn('o.status', ['shipped', 'delivered'])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Fill all months with default 0
        $monthlyData = array_fill(1, 12, 0);
        foreach ($monthlyRaw as $row) {
            $monthlyData[$row->month] = $row->books_purchased;
        }

        // Pass all data to Blade
        return view('profile.index', [
            'user' => $user,
            'totalBooks' => $totalBooks,
            'activeSubs' => $activeSubs,
            'partnerStatus' => $partnerStatus,
            'totalSpent' => $totalSpent,
            'monthlyData' => $monthlyData
        ]);
    }
}
