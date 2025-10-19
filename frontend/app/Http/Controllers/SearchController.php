<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = trim($request->input('query', ''));

        if ($query === '') {
            return redirect()->route('home');
        }

        $books = DB::table('books')
            ->where('title', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->limit(20)
            ->get();

        return view('search', [
            'query' => $query,
            'books' => $books
        ]);
    }
}
