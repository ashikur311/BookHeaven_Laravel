<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    public function index()
    {
        $error_message = '';
        $success_message = session('success') ?? '';

        // prepare containers
        $stats = [
            'total_orders' => 0,
            'pending'      => 0,
            'confirmed'    => 0,
            'shipped'      => 0,
            'delivered'    => 0,
            'cancelled'    => 0,
        ];

        $statuses = ['pending','confirmed','shipped','delivered','cancelled'];
        $orders_by_status = array_fill_keys($statuses, collect());

        try {
            // stats
            $stats['total_orders'] = (int) DB::table('orders')->count();
            foreach ($statuses as $s) {
                $stats[$s] = (int) DB::table('orders')->where('status', $s)->count();
            }

            // lists
            foreach ($statuses as $s) {
                $orders_by_status[$s] = DB::table('orders as o')
                    ->join('users as u', 'o.user_id', '=', 'u.user_id')
                    ->select('o.order_id','o.user_id','u.username','o.total_amount','o.order_date','o.status',
                             'o.payment_method','o.shipping_address')
                    ->where('o.status', $s)
                    ->orderByDesc('o.order_date')
                    ->get();
            }
        } catch (\Throwable $e) {
            $error_message = 'Error fetching orders: ' . $e->getMessage();
        }

        return view('admin.orders', compact('stats','orders_by_status','error_message','success_message'));
    }

    public function updateStatus(Request $request)
    {
        $data = $request->validate([
            'order_id'   => ['required','integer'],
            'new_status' => ['required','in:pending,confirmed,shipped,delivered,cancelled'],
        ]);

        try {
            DB::table('orders')
                ->where('order_id', (int) $data['order_id'])
                ->update(['status' => $data['new_status']]);

            return redirect()->route('admin.orders')
                ->with('success', "Order #{$data['order_id']} status updated to {$data['new_status']} successfully!");
        } catch (\Throwable $e) {
            return redirect()->route('admin.orders')
                ->with('error', 'Error updating order status: ' . $e->getMessage());
        }
    }
    public function edit(int $id)
{
    try {
        $order = DB::table('orders as o')
            ->join('users as u', 'o.user_id', '=', 'u.user_id')
            ->select('o.*', 'u.username', 'u.email')
            ->where('o.order_id', $id)
            ->first();

        if (!$order) {
            return redirect()->route('admin.orders')->with('error', 'Order not found');
        }

        $items = DB::table('order_items as oi')
            ->join('books as b', 'oi.book_id', '=', 'b.book_id')
            ->select('oi.*', 'b.title')
            ->where('oi.order_id', $id)
            ->orderBy('oi.id')
            ->get();

        return view('admin.order_edit', [
            'order'       => $order,
            'order_items' => $items,
        ]);
    } catch (\Throwable $e) {
        return redirect()->route('admin.orders')->with('error', 'Error fetching order: '.$e->getMessage());
    }
}

      public function update(Request $request, int $id)
            {
            $data = $request->validate([
        'status'           => 'required|in:pending,confirmed,shipped,delivered,cancelled',
        'shipping_address' => 'nullable|string',
        'payment_method'   => 'required|in:cod,online',
        'notes'            => 'nullable|string',
    ]);

    try {
        $affected = DB::table('orders')->where('order_id', $id)->update($data);

        return redirect()
            ->route('admin.orders')
            ->with('success', "Order #{$id} updated successfully!");
    } catch (\Throwable $e) {
        return back()->withInput()->with('error', 'Error updating order: '.$e->getMessage());
    }
}

    public function destroy(Request $request)
    {
        $data = $request->validate([
            'order_id' => ['required','integer'],
        ]);

        try {
            DB::beginTransaction();

            DB::table('order_items')->where('order_id', (int) $data['order_id'])->delete();
            DB::table('orders')->where('order_id', (int) $data['order_id'])->delete();

            DB::commit();
            return redirect()->route('admin.orders')
                ->with('success', "Order #{$data['order_id']} deleted successfully!");
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('admin.orders')
                ->with('error', 'Error deleting order: ' . $e->getMessage());
        }
    }
    public function items(int $id)
{
    try {
        // Order header (user, totals, etc.)
        $order = DB::table('orders as o')
            ->join('users as u', 'o.user_id', '=', 'u.user_id')
            ->select(
                'o.order_id','o.user_id','o.order_date','o.status','o.total_amount',
                'o.payment_method','o.payment_status','o.shipping_address',
                'u.username','u.email'
            )
            ->where('o.order_id', $id)
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Line items
        $items = DB::table('order_items as oi')
            ->join('books as b', 'oi.book_id', '=', 'b.book_id')
            ->select(
                'oi.id','oi.book_id','oi.quantity','oi.price',
                'b.title','b.cover_image_url'
            )
            ->where('oi.order_id', $id)
            ->orderBy('oi.id')
            ->get();

        // computed totals (server trust > client render)
        $subTotal = $items->reduce(function($c,$i){ return $c + ((float)$i->price * (int)$i->quantity); }, 0.0);
        // Add any tax/shipping logic here if you have it; keeping 0 for now:
        $tax = 0.0; $shipping = 0.0;
        $grandTotal = $subTotal + $tax + $shipping;

        return response()->json([
            'order' => [
                'id' => $order->order_id,
                'status' => $order->status,
                'date' => $order->order_date,
                'total' => (float)$order->total_amount,
                'payment_method' => $order->payment_method,
                'payment_status' => $order->payment_status,
                'shipping_address' => $order->shipping_address,
                'user' => [
                    'id' => $order->user_id,
                    'name' => $order->username,
                    'email' => $order->email,
                ],
            ],
            'items' => $items->map(function($i){
                return [
                    'id' => $i->id,
                    'book_id' => $i->book_id,
                    'title' => $i->title,
                    'cover' => $i->cover_image_url, // may be null
                    'price' => (float)$i->price,
                    'quantity' => (int)$i->quantity,
                    'line_total' => (float)$i->price * (int)$i->quantity,
                ];
            }),
            'totals' => [
                'subtotal' => $subTotal,
                'tax' => $tax,
                'shipping' => $shipping,
                'grand_total' => $grandTotal,
            ]
        ]);
    } catch (\Throwable $e) {
        return response()->json(['message' => 'Error fetching items: '.$e->getMessage()], 500);
    }
}
}
