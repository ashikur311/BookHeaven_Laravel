<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class CartController extends Controller
{
    /** Show Cart */
    public function index()
    {
        $userId = Auth::id();

        $cartItems = DB::table('cart as c')
            ->join('books as b', 'c.book_id', '=', 'b.book_id')
            ->leftJoin('book_writers as bw', 'b.book_id', '=', 'bw.book_id')
            ->leftJoin('writers as w', 'bw.writer_id', '=', 'w.writer_id')
            ->where('c.user_id', $userId)
            ->select(
                'c.id as id',
                'c.quantity',
                'b.book_id',
                'b.title',
                'b.price',
                'b.cover_image_url',
                DB::raw('GROUP_CONCAT(DISTINCT w.name SEPARATOR ", ") as writers')
            )
            ->groupBy('c.id','c.quantity','b.book_id','b.title','b.price','b.cover_image_url')
            ->get();

        $totals = $this->totals($cartItems);
        $userAddress = DB::table('user_info')->where('user_id', $userId)->value('address') ?? '';

        return view('cart.index', compact('cartItems', 'totals', 'userAddress'));
    }

    /** Add Book to Cart (no duplicates) */
    public function add(Request $request)
    {
        $userId = Auth::id();
        $bookId = $request->input('book_id');

        $exists = DB::table('cart')->where('user_id', $userId)->where('book_id', $bookId)->exists();
        if ($exists) {
            return redirect()->route('cart')->with('info', 'Book already in your cart.');
        }

        DB::table('cart')->insert([
            'user_id' => $userId,
            'book_id' => $bookId,
            'quantity' => 1,
        ]);

        return redirect()->route('cart')->with('success', 'Book added to your cart!');
    }

    /** One endpoint for all AJAX cart actions */
    public function handleAction(Request $request)
    {
        $userId = Auth::id();
        $action = $request->input('action');
        $resp = ['success' => false];

        try {
            switch ($action) {
                case 'update': // quantity
                    DB::table('cart')
                        ->where('id', $request->cart_id)
                        ->where('user_id', $userId)
                        ->update(['quantity' => max(1, (int)$request->quantity)]);
                    break;

                case 'remove':
                    DB::table('cart')
                        ->where('id', $request->cart_id)
                        ->where('user_id', $userId)
                        ->delete();
                    break;

                case 'clear':
                    DB::table('cart')->where('user_id', $userId)->delete();
                    break;

                case 'move_to_wishlist':
                    DB::transaction(function () use ($request, $userId) {
                        $bookId = (int)$request->book_id;

                        $exists = DB::table('wishlist')
                            ->where('user_id', $userId)
                            ->where('book_id', $bookId)
                            ->exists();

                        if (!$exists) {
                            DB::table('wishlist')->insert([
                                'user_id' => $userId,
                                'book_id' => $bookId,
                                'added_at' => now(),
                            ]);
                        }

                        DB::table('cart')
                            ->where('id', $request->cart_id)
                            ->where('user_id', $userId)
                            ->delete();
                    });
                    $resp['message'] = 'Book moved to wishlist successfully';
                    break;

                case 'place_order':
                    return $this->placeOrder($request, $userId);

                default:
                    $resp['message'] = 'Invalid action';
            }

            // return fresh totals for dynamic summary updates
            $mini = DB::table('cart as c')
                ->join('books as b', 'c.book_id', '=', 'b.book_id')
                ->where('c.user_id', $userId)
                ->select('c.quantity','b.price')
                ->get();

            $resp['success'] = true;
            $resp['totals']  = $this->totals($mini);

        } catch (Exception $e) {
            $resp['message'] = $e->getMessage();
        }

        return response()->json($resp);
    }

    /** Calculate totals */
    private function totals($items)
    {
        $subtotal = 0; $count = 0;
        foreach ($items as $i) {
            $subtotal += (float)$i->price * (int)$i->quantity;
            $count += (int)$i->quantity;
        }
        $delivery = $count > 0 ? 60 : 0;

        return [
            'subtotal'   => $subtotal,
            'item_count' => $count,
            'delivery'   => $delivery,
            'total'      => $subtotal + $delivery,
        ];
    }

    /** Place order and return order_id for redirect */
    private function placeOrder(Request $request, int $userId)
    {
        $payment = $request->input('payment_method');
        $address = $request->input('shipping_address');

        $cart = DB::table('cart as c')
            ->join('books as b', 'c.book_id', '=', 'b.book_id')
            ->where('c.user_id', $userId)
            ->select('c.book_id','c.quantity','b.price')
            ->get();

        if ($cart->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Your cart is empty.']);
        }

        $totals = $this->totals($cart);

        try {
            DB::beginTransaction();

            $orderId = DB::table('orders')->insertGetId([
                'user_id'          => $userId,
                'status'           => 'pending',
                'total_amount'     => $totals['total'],
                'payment_method'   => $payment,
                'shipping_address' => $address,
                'payment_status' => 'pending',
            ]);

            foreach ($cart as $i) {
                DB::table('order_items')->insert([
                    'order_id' => $orderId,
                    'book_id'  => $i->book_id,
                    'quantity' => $i->quantity,
                    'price'    => $i->price,
                ]);
            }

            DB::table('cart')->where('user_id', $userId)->delete();
            DB::commit();

            return response()->json([
                'success'  => true,
                'order_id' => $orderId,
                'message'  => 'Order placed successfully!',
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error: '.$e->getMessage()]);
        }
    }
}
