<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WishlistController extends Controller
{
    // 🖥️ View wishlist
    public function index()
    {
        $user = Auth::user();
        $userId = $user->user_id ?? $user->id; // ✅ Works with both schemas

        // Fetch wishlist items
        $wishlist = DB::table('wishlist as w')
            ->join('books as b', 'w.book_id', '=', 'b.book_id')
            ->where('w.user_id', $userId)
            ->select('b.*', 'w.added_at')
            ->orderByDesc('w.added_at')
            ->get();

        // Count
        $wishlist_count = $wishlist->count();

        // Add writers for each book
        foreach ($wishlist as $book) {
            $book->writers = DB::table('book_writers as bw')
                ->join('writers as w', 'bw.writer_id', '=', 'w.writer_id')
                ->where('bw.book_id', $book->book_id)
                ->pluck('w.name')
                ->implode(', ');
        }

        return view('profile.wishlist', compact('user', 'wishlist', 'wishlist_count'));
    }

    // 🗑️ Remove from wishlist (AJAX)
    public function remove(Request $request)
    {
        $user = Auth::user();
        $userId = $user->user_id ?? $user->id;

        DB::table('wishlist')
            ->where('user_id', $userId)
            ->where('book_id', $request->book_id)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Book removed from wishlist']);
    }

    // ➕ Add to cart (AJAX)
    public function addToCart(Request $request)
    {
        $user = Auth::user();
        $userId = $user->user_id ?? $user->id;
        $bookId = $request->book_id;

        $exists = DB::table('cart')->where('user_id', $userId)->where('book_id', $bookId)->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This book is already in your cart',
                'already_in_cart' => true,
            ]);
        }

        DB::transaction(function () use ($userId, $bookId) {
            DB::table('cart')->insert([
                'user_id' => $userId,
                'book_id' => $bookId,
                'quantity' => 1,
            ]);

            DB::table('wishlist')
                ->where('user_id', $userId)
                ->where('book_id', $bookId)
                ->delete();
        });

        return response()->json(['success' => true, 'message' => 'Book added to cart and removed from wishlist']);
    }

    // 🧾 Check if book already in cart
    public function checkCart(Request $request)
    {
        $user = Auth::user();
        $userId = $user->user_id ?? $user->id;

        $exists = DB::table('cart')
            ->where('user_id', $userId)
            ->where('book_id', $request->book_id)
            ->exists();

        return response()->json([
            'success' => true,
            'already_in_cart' => $exists,
        ]);
    }
}
