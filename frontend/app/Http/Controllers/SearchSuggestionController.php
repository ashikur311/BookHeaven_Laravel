<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchSuggestionController extends Controller
{
    public function index(Request $request)
    {
        $query = trim($request->input('query', ''));

        if ($query === '') {
            return response()->json([]);
        }

        $like = '%' . $query . '%';
        $results = [];

        // 🔹 Search Books
        $books = DB::table('books')
            ->select('book_id as id', 'title as name', DB::raw("'book' as type"))
            ->where('title', 'LIKE', $like)
            ->limit(5)
            ->get()
            ->toArray();
        $results = array_merge($results, $books);

        // 🔹 Search Authors
        $authors = DB::table('writers')
            ->select('writer_id as id', 'name', DB::raw("'author' as type"))
            ->where('name', 'LIKE', $like)
            ->limit(5)
            ->get()
            ->toArray();
        $results = array_merge($results, $authors);

        // 🔹 Search Genres
        $genres = DB::table('genres')
            ->select('genre_id as id', 'name', DB::raw("'genre' as type"))
            ->where('name', 'LIKE', $like)
            ->limit(5)
            ->get()
            ->toArray();
        $results = array_merge($results, $genres);

        return response()->json($results);
    }
}
