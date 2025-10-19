<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    // 📘 Show detailed book page
    public function bookDetails($id)
    {
        // Fetch main book info
        $book = DB::table('books')->where('book_id', $id)->first();
        if (!$book) {
            return view('errors.404');
        }

        // Related entities
        $writers = DB::table('writers')
            ->join('book_writers', 'writers.writer_id', '=', 'book_writers.writer_id')
            ->where('book_writers.book_id', $id)
            ->get();

        $genres = DB::table('genres')
            ->join('book_genres', 'genres.genre_id', '=', 'book_genres.genre_id')
            ->where('book_genres.book_id', $id)
            ->get();

        $categories = DB::table('categories')
            ->join('book_categories', 'categories.id', '=', 'book_categories.category_id')
            ->where('book_categories.book_id', $id)
            ->get();

        $languages = DB::table('languages')
            ->join('book_languages', 'languages.language_id', '=', 'book_languages.language_id')
            ->where('book_languages.book_id', $id)
            ->get();

        // Reviews
        $reviews = DB::table('reviews as r')
            ->join('users as u', 'r.user_id', '=', 'u.user_id')
            ->leftJoin('user_info as ui', 'u.user_id', '=', 'ui.user_id')
            ->select('r.*', 'u.username', 'ui.userimageurl as user_profile')
            ->where('r.book_id', $id)
            ->orderByDesc('r.created_at')
            ->limit(8)
            ->get();

        // Questions + answers
        $questions = DB::table('questions as q')
            ->join('users as u', 'q.user_id', '=', 'u.user_id')
            ->leftJoin('user_info as ui', 'u.user_id', '=', 'ui.user_id')
            ->leftJoin('answers as a', 'q.question_id', '=', 'a.question_id')
            ->leftJoin('admin as au', 'a.admin_id', '=', 'au.admin_id')
            ->leftJoin('user_info as aui', 'au.admin_id', '=', 'aui.user_id')
            ->select(
                'q.*',
                'u.username as questioner_name',
                'ui.userimageurl as questioner_image',
                'a.answer_text',
                'a.created_at as answer_date',
                'au.username as answerer_name',
                'aui.userimageurl as answerer_image'
            )
            ->where('q.book_id', $id)
            ->orderByDesc('q.created_at')
            ->limit(2)
            ->get();

        // Related books (same genre)
        $related_books = [];
        if ($genres->count() > 0) {
            $genre_ids = $genres->pluck('genre_id');
            $related_books = DB::table('books as b')
                ->join('book_genres as bg', 'b.book_id', '=', 'bg.book_id')
                ->whereIn('bg.genre_id', $genre_ids)
                ->where('b.book_id', '!=', $id)
                ->groupBy('b.book_id')
                ->orderByRaw('RAND()')
                ->limit(4)
                ->get();
        }

        return view('book_details.show', compact(
            'book', 'writers', 'genres', 'categories', 'languages',
            'reviews', 'questions', 'related_books'
        ));
    }

    // 🛒 Add to Cart
    public function addToCart(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->back()->with('message', 'Please login to add items to cart.');
        }

        $user_id = Auth::id();
        $book_id = $request->input('book_id');
        $quantity = $request->input('quantity', 1);

        $exists = DB::table('cart')->where('user_id', $user_id)->where('book_id', $book_id)->first();

        if ($exists) {
            return redirect()->back()->with('message', 'This book is already in your cart.');
        }

        DB::table('cart')->insert([
            'user_id' => $user_id,
            'book_id' => $book_id,
            'quantity' => $quantity,
            'created_at' => now(),
        ]);

        return redirect()->back()->with('message', 'Book added to cart successfully!');
    }

    // ❤️ Add to Wishlist
public function addToWishlist(Request $request)
{
    $userId = Auth::id();
    $bookId = $request->book_id;

    if (!$userId) {
        return redirect()->route('login')->with('error', 'Please log in to add items to your wishlist.');
    }

    // ✅ Check if already in wishlist
    $exists = DB::table('wishlist')
        ->where('user_id', $userId)
        ->where('book_id', $bookId)
        ->exists();

    if ($exists) {
        return back()->with('message', 'This book is already in your wishlist.');
    }

    // ✅ Insert new wishlist item
    DB::table('wishlist')->insert([
        'user_id' => $userId,
        'book_id' => $bookId,
        'added_at' => now(),
    ]);

    return back()->with('message', 'Book added to your wishlist!');
}


    // ✍️ Submit Review
    public function submitReview(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->back()->with('message', 'Please login to submit a review.');
        }

        $user_id = Auth::id();
        $book_id = $request->input('book_id');
        $rating = intval($request->input('rating'));
        $review_text = $request->input('review_text');

        $exists = DB::table('reviews')->where('user_id', $user_id)->where('book_id', $book_id)->first();
        if ($exists) {
            return redirect()->back()->with('message', 'You have already reviewed this book.');
        }

        DB::table('reviews')->insert([
            'user_id' => $user_id,
            'book_id' => $book_id,
            'review_text' => $review_text,
            'rating' => $rating,
            'created_at' => now(),
        ]);

        DB::update("
            UPDATE books 
            SET rating = (SELECT AVG(rating) FROM reviews WHERE book_id = ?)
            WHERE book_id = ?
        ", [$book_id, $book_id]);

        return redirect()->back()->with('message', 'Thank you for your review!');
    }

    // ❓ Submit Question
    public function submitQuestion(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->back()->with('message', 'Please login to ask a question.');
        }

        $user_id = Auth::id();
        $book_id = $request->input('book_id');
        $question_text = $request->input('question_text');

        DB::table('questions')->insert([
            'user_id' => $user_id,
            'book_id' => $book_id,
            'question_text' => $question_text,
            'created_at' => now(),
        ]);

        return redirect()->back()->with('message', 'Your question has been submitted!');
    }
}
