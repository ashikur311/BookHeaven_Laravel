<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $userId = session('user_id');

        // ---------- Fetch books ----------
        $all_books = DB::table('books')
            ->leftJoin('book_writers', 'books.book_id', '=', 'book_writers.book_id')
            ->leftJoin('writers', 'book_writers.writer_id', '=', 'writers.writer_id')
            ->leftJoin('book_genres', 'books.book_id', '=', 'book_genres.book_id')
            ->leftJoin('genres', 'book_genres.genre_id', '=', 'genres.genre_id')
            ->leftJoin('book_categories', 'books.book_id', '=', 'book_categories.book_id')
            ->leftJoin('categories', 'book_categories.category_id', '=', 'categories.id')
            ->select('books.*',
                DB::raw('GROUP_CONCAT(DISTINCT writers.name) as writers'),
                DB::raw('GROUP_CONCAT(DISTINCT genres.name) as genres'),
                DB::raw('GROUP_CONCAT(DISTINCT categories.name) as categories')
            )
            ->groupBy('books.book_id')
            ->orderByDesc('books.created_at')
            ->limit(20)
            ->get();

        $popular_books = DB::table('books')
            ->leftJoin('order_items', 'books.book_id', '=', 'order_items.book_id')
            ->leftJoin('book_writers', 'books.book_id', '=', 'book_writers.book_id')
            ->leftJoin('writers', 'book_writers.writer_id', '=', 'writers.writer_id')
            ->select('books.*',
                DB::raw('COUNT(order_items.book_id) as order_count'),
                DB::raw('GROUP_CONCAT(DISTINCT writers.name) as writers')
            )
            ->groupBy('books.book_id')
            ->orderByDesc('order_count')
            ->limit(20)
            ->get();

        $top_rated_books = DB::table('books')
            ->leftJoin('book_writers', 'books.book_id', '=', 'book_writers.book_id')
            ->leftJoin('writers', 'book_writers.writer_id', '=', 'writers.writer_id')
            ->whereNotNull('books.rating')
            ->select('books.*', DB::raw('GROUP_CONCAT(DISTINCT writers.name) as writers'))
            ->groupBy('books.book_id')
            ->orderByDesc('books.rating')
            ->limit(20)
            ->get();

        $recent_books = DB::table('books')
            ->leftJoin('book_writers', 'books.book_id', '=', 'book_writers.book_id')
            ->leftJoin('writers', 'book_writers.writer_id', '=', 'writers.writer_id')
            ->select('books.*', DB::raw('GROUP_CONCAT(DISTINCT writers.name) as writers'))
            ->groupBy('books.book_id')
            ->orderByDesc('books.created_at')
            ->limit(20)
            ->get();

        $writers = DB::table('writers')->orderBy('name')->limit(15)->get();
        $genres = DB::table('genres')->orderBy('name')->limit(15)->get();

        // ---------- Pass data to view ----------
        return view('home', compact(
            'userId',
            'all_books',
            'popular_books',
            'top_rated_books',
            'recent_books',
            'writers',
            'genres'
        ));
    }
}
