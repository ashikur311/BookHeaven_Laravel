<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WriterController extends Controller
{
    // 🖥️ Show writer books
    public function index(Request $request, $writer_id = null)
    {
        // Get all writers for sidebar
        $writers = DB::table('writers')->orderBy('name')->get();

        // If no writer selected, redirect to first
        if (!$writer_id && $writers->count() > 0) {
            $firstWriter = $writers->first();
            return redirect()->route('writer.books', ['writer_id' => $firstWriter->writer_id]);
        }

        // Get current writer details
        $writer = DB::table('writers')->where('writer_id', $writer_id)->first();

        // If invalid writer, redirect to first
        if (!$writer && $writers->count() > 0) {
            $firstWriter = $writers->first();
            return redirect()->route('writer.books', ['writer_id' => $firstWriter->writer_id]);
        }

        // Fetch all books by this writer with average ratings
        $books = DB::table('books as b')
            ->join('book_writers as bw', 'b.book_id', '=', 'bw.book_id')
            ->leftJoin('reviews as r', 'b.book_id', '=', 'r.book_id')
            ->where('bw.writer_id', $writer_id)
            ->select('b.*', DB::raw('AVG(r.rating) as avg_rating'))
            ->groupBy('b.book_id')
            ->orderBy('b.title')
            ->get();

        // Detect which books are already in user's cart
        $in_cart = [];
        if (Auth::check()) {
            $user = Auth::user();
            $userId = $user->user_id ?? $user->id;
            $in_cart = DB::table('cart')
                ->where('user_id', $userId)
                ->pluck('book_id')
                ->toArray();
        }

        return view('writer_books.index', compact('writers', 'writer', 'books', 'in_cart'));
    }

    // ➕ Add to Cart (AJAX)
    public function addToCart(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'You must be logged in']);
        }

        $user = Auth::user();
        $userId = $user->user_id ?? $user->id;
        $bookId = $request->book_id;

        $exists = DB::table('cart')
            ->where('user_id', $userId)
            ->where('book_id', $bookId)
            ->exists();

        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Book already in cart']);
        }

        DB::table('cart')->insert([
            'user_id' => $userId,
            'book_id' => $bookId,
            'quantity' => 1,
        ]);

        $cartCount = DB::table('cart')->where('user_id', $userId)->count();

        return response()->json(['success' => true, 'cart_count' => $cartCount]);
    }
}
