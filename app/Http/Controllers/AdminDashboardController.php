<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Stats
            $stats = [
                'total_users'      => DB::table('users')->count(),
                'total_books'      => DB::table('books')->count(),
                'total_partners'   => DB::table('partners')->count(),
                'total_audiobooks' => DB::table('audiobooks')->count(),
                'total_writers'    => DB::table('writers')->count(),
                'total_sales_month'=> DB::table('orders')
                    ->whereBetween('order_date', [
                        now()->startOfMonth(), now()->endOfMonth()
                    ])->count(),
                'total_orders'     => DB::table('orders')->count(),
            ];

            // Monthly sales (last 6 months)
            $monthly_sales_data = DB::table('orders')
                ->selectRaw("DATE_FORMAT(order_date, '%b') as month, COUNT(*) as count")
                ->where('order_date', '>=', now()->subMonths(6))
                ->groupByRaw("DATE_FORMAT(order_date, '%Y-%m'), DATE_FORMAT(order_date, '%b')")
                ->orderByRaw("DATE_FORMAT(order_date, '%Y-%m')")
                ->get();

            $monthly_sales = [
                'labels' => $monthly_sales_data->pluck('month'),
                'data'   => $monthly_sales_data->pluck('count'),
            ];

            // Subscription sales
            $subscription_sales_data = DB::table('subscription_plans as sp')
                ->leftJoin('subscription_orders as so', 'sp.plan_id', '=', 'so.plan_id')
                ->select('sp.plan_name', DB::raw('COUNT(so.id) as count'))
                ->groupBy('sp.plan_id','sp.plan_name')
                ->get();

            $subscription_sales = [
                'labels' => $subscription_sales_data->pluck('plan_name'),
                'data'   => $subscription_sales_data->pluck('count'),
            ];

            // User growth
            $user_growth_data = DB::table('users')
                ->selectRaw("DATE_FORMAT(create_time, '%b') as month, COUNT(*) as count")
                ->where('create_time', '>=', now()->subMonths(6))
                ->groupByRaw("DATE_FORMAT(create_time, '%Y-%m'), DATE_FORMAT(create_time, '%b')")
                ->orderByRaw("DATE_FORMAT(create_time, '%Y-%m')")
                ->get();

            $user_growth = [
                'labels' => $user_growth_data->pluck('month'),
                'data'   => $user_growth_data->pluck('count'),
            ];

            // Pending orders
            $pending_orders = DB::table('orders as o')
                ->join('users as u', 'o.user_id', '=', 'u.user_id')
                ->select('o.order_id as id', 'u.username as user_name', 
                         DB::raw("DATE_FORMAT(o.order_date, '%Y-%m-%d') as date"), 'o.total_amount as amount')
                ->where('o.status', 'pending')
                ->orderBy('o.order_date', 'desc')
                ->limit(5)
                ->get();

            // Pending partners
            $pending_partners = DB::table('partners as p')
                ->join('users as u', 'p.user_id', '=', 'u.user_id')
                ->select('p.partner_id as id', 'u.username as name',
                         DB::raw("DATE_FORMAT(p.joined_at, '%Y-%m-%d') as joined_date"), 'p.status')
                ->where('p.status', 'pending')
                ->orderBy('p.joined_at', 'desc')
                ->get();

        } catch (\Exception $e) {
            return back()->with('error', 'Error fetching data: '.$e->getMessage());
        }

        return view('admin.dashboard', compact(
            'stats','monthly_sales','subscription_sales','user_growth','pending_orders','pending_partners'
        ));
    }

    public function approvePartner(Request $request)
    {
        DB::table('partners')
            ->where('partner_id', $request->partner_id)
            ->update(['status' => 'approved']);

        return back()->with('success', 'Partner request approved successfully!');
    }

    public function cancelPartner(Request $request)
    {
        DB::table('partners')
            ->where('partner_id', $request->partner_id)
            ->update(['status' => 'suspended']);

        return back()->with('success', 'Partner request cancelled successfully!');
    }
}
