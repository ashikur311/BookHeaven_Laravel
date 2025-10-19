<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GenreController extends Controller
{
    public function show($id = null)
    {
        $userId = Auth::id();

        $genres = DB::table('genres')->orderBy('name')->get();

        if (!$id && $genres->count() > 0) {
            return redirect()->route('genre_books.show', ['id' => $genres->first()->genre_id]);
        }

        $genre = DB::table('genres')->where('genre_id', $id)->first();

        if (!$genre && $genres->count() > 0) {
            return redirect()->route('genre_books.show', ['id' => $genres->first()->genre_id]);
        }

        $books = DB::table('books as b')
            ->join('book_genres as bg', 'b.book_id', '=', 'bg.book_id')
            ->leftJoin('book_writers as bw', 'b.book_id', '=', 'bw.book_id')
            ->leftJoin('writers as w', 'bw.writer_id', '=', 'w.writer_id')
            ->leftJoin('reviews as r', 'b.book_id', '=', 'r.book_id')
            ->select(
                'b.*',
                DB::raw('GROUP_CONCAT(DISTINCT w.name SEPARATOR ", ") as writers'),
                DB::raw('AVG(r.rating) as avg_rating')
            )
            ->where('bg.genre_id', $id)
            ->groupBy('b.book_id')
            ->orderBy('b.title')
            ->get();

        $cartBookIds = [];
        if ($userId) {
            $cartBookIds = DB::table('cart')
                ->where('user_id', $userId)
                ->pluck('book_id')
                ->toArray();
        }

        return view('genre_books.show', compact('genre', 'genres', 'books', 'cartBookIds'));
    }

    public function addToCart(Request $request)
    {
        $request->validate(['book_id' => 'required|integer']);

        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'Please login first.']);
        }

        $bookId = $request->book_id;

        $exists = DB::table('cart')->where('user_id', $userId)->where('book_id', $bookId)->exists();
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Book already in cart']);
        }

        DB::table('cart')->insert([
            'user_id' => $userId,
            'book_id' => $bookId,
        ]);

        $cartCount = DB::table('cart')->where('user_id', $userId)->count();

        return response()->json([
            'success' => true,
            'cart_count' => $cartCount,
            'message' => 'Book added to cart successfully',
        ]);
    }
}
