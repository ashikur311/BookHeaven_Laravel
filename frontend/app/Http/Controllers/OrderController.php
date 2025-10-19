<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $user = DB::table('users as u')
            ->leftJoin('user_info as ui', 'u.user_id', '=', 'ui.user_id')
            ->where('u.user_id', $userId)
            ->select('u.*', 'ui.address', 'ui.phone')
            ->first();

        $orders = DB::table('orders')
            ->where('user_id', $userId)
            ->orderByDesc('order_date')
            ->get();

        $stats = [
            'total' => $orders->count(),
            'pending' => $orders->where('status', 'pending')->count(),
            'shipped' => $orders->where('status', 'shipped')->count(),
            'delivered' => $orders->where('status', 'delivered')->count(),
        ];

        return view('profile.orders', compact('orders', 'user', 'stats'));
    }

    public function details($id)
    {
        $userId = Auth::id();

        $order = DB::table('orders')
            ->where('user_id', $userId)
            ->where('order_id', $id)
            ->first();

        $items = DB::table('order_items as oi')
            ->join('books as b', 'oi.book_id', '=', 'b.book_id')
            ->where('oi.order_id', $id)
            ->select('oi.*', 'b.title', 'b.cover_image_url')
            ->get();

        return response()->json([
            'success' => true,
            'order' => $order,
            'items' => $items
        ]);
    }
}
