<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BooksController extends Controller
{
    public function index(Request $request)
    {
        $error_message = '';
        $success_message = '';

        try {
            $stats = [
                'total_books'    => (int) DB::table('books')->count(),
                'stock_out'      => (int) DB::table('books')->where('quantity', 0)->count(),
                'new_this_month' => (int) DB::selectOne("
                    SELECT COUNT(*) AS c
                    FROM books
                    WHERE created_at >= DATE_FORMAT(CURRENT_DATE(), '%Y-%m-01')
                      AND created_at <  DATE_FORMAT(CURRENT_DATE() + INTERVAL 1 MONTH, '%Y-%m-01')
                ")->c,
            ];

            $books = DB::select("
                SELECT 
                    b.book_id,
                    b.title,
                    b.quantity,
                    b.price,
                    b.created_at,
                    b.cover_image_url,
                    w.name AS author
                FROM books b
                LEFT JOIN book_writers bw ON bw.book_id = b.book_id
                LEFT JOIN writers w      ON w.writer_id = bw.writer_id
                ORDER BY b.created_at DESC
            ");
        } catch (\Throwable $e) {
            $stats = ['total_books' => 0, 'stock_out' => 0, 'new_this_month' => 0];
            $books = [];
            $error_message = 'Error fetching books: ' . $e->getMessage();
        }

        return view('admin.books', compact('stats', 'books', 'error_message', 'success_message'));
    }

    // Optional: you can wire your edit page later
    public function edit($id)
    {
        // Basic book row + the linked writer (if any)
        $book = DB::selectOne("
            SELECT b.*, w.writer_id, w.name AS writer_name
            FROM books b
            LEFT JOIN book_writers bw ON bw.book_id = b.book_id
            LEFT JOIN writers w      ON w.writer_id = bw.writer_id
            WHERE b.book_id = ?
        ", [(int)$id]);

        if (!$book) {
            return redirect()->route('admin.books')->with('error', 'Book not found!');
        }

        // Dropdown data
        $categories = DB::table('categories')->orderBy('name')->get();
        $genres     = DB::table('genres')->orderBy('name')->get();
        $languages  = DB::table('languages')->orderBy('name')->get();
        $writers    = DB::table('writers')->orderBy('name')->get();

        // Pre-selected mappings
        $bookCategoryIds = DB::table('book_categories')
            ->where('book_id', $id)->pluck('category_id')->toArray();

        $bookGenreIds = DB::table('book_genres')
            ->where('book_id', $id)->pluck('genre_id')->toArray();

        $bookLanguageIds = DB::table('book_languages')
            ->where('book_id', $id)->pluck('language_id')->toArray();

        return view('admin.book_edit', [
            'book'            => $book,
            'categories'      => $categories,
            'genres'          => $genres,
            'languages'       => $languages,
            'writers'         => $writers,
            'bookCategoryIds' => $bookCategoryIds,
            'bookGenreIds'    => $bookGenreIds,
            'bookLanguageIds' => $bookLanguageIds,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'book_id'   => ['required','integer'],
            'title'     => ['required','string','max:255'],
            'published' => ['nullable','date'],
            'price'     => ['required','numeric','min:0'],
            'quantity'  => ['required','integer','min:0'],
            'details'   => ['nullable','string'],
            'writer_id' => ['required','integer'],
            'categories'=> ['array'],
            'categories.*' => ['integer'],
            'genres'    => ['array'],
            'genres.*'  => ['integer'],
            'languages' => ['array'],
            'languages.*' => ['integer'],
            'cover_image' => ['nullable','file','mimes:jpg,jpeg,png,webp'],
            'current_cover' => ['nullable','string'], // if you want to pass it from the form (optional)
        ]);

        $bookId = (int) $data['book_id'];

        DB::beginTransaction();
        try {
            // Handle cover upload (optional)
            $coverPath = $data['current_cover'] ?? null;
            if ($request->hasFile('cover_image')) {
                $dir = public_path('assets/book_covers');
                if (!is_dir($dir)) {
                    @mkdir($dir, 0755, true);
                }
                $ext = $request->file('cover_image')->getClientOriginalExtension();
                $filename = preg_replace('/[^a-zA-Z0-9]/', '_', Str::limit($data['title'], 60, '')) . '_' . time() . '.' . $ext;
                $request->file('cover_image')->move($dir, $filename);

                // delete old file if present and local
                if (!empty($coverPath)) {
                    $old = public_path($coverPath);
                    if (is_file($old)) {
                        @unlink($old);
                    }
                }
                $coverPath = 'assets/book_covers/' . $filename;
            }

            // Update books table
            DB::table('books')->where('book_id', $bookId)->update([
                'title'        => $data['title'],
                'published'    => $data['published'] ?? null,
                'price'        => $data['price'],
                'quantity'     => $data['quantity'],
                'details'      => $data['details'] ?? null,
                'cover_image_url' => $coverPath,
            ]);

            // Writer mapping (one-to-one per your legacy)
            DB::table('book_writers')->where('book_id', $bookId)->delete();
            DB::table('book_writers')->insert([
                'book_id' => $bookId,
                'writer_id' => (int)$data['writer_id'],
            ]);

            // Categories
            DB::table('book_categories')->where('book_id', $bookId)->delete();
            if (!empty($data['categories'])) {
                $rows = array_map(fn($cid) => ['book_id' => $bookId, 'category_id' => (int)$cid], $data['categories']);
                DB::table('book_categories')->insert($rows);
            }

            // Genres
            DB::table('book_genres')->where('book_id', $bookId)->delete();
            if (!empty($data['genres'])) {
                $rows = array_map(fn($gid) => ['book_id' => $bookId, 'genre_id' => (int)$gid], $data['genres']);
                DB::table('book_genres')->insert($rows);
            }

            // Languages
            DB::table('book_languages')->where('book_id', $bookId)->delete();
            if (!empty($data['languages'])) {
                $rows = array_map(fn($lid) => ['book_id' => $bookId, 'language_id' => (int)$lid], $data['languages']);
                DB::table('book_languages')->insert($rows);
            }

            DB::commit();
            return redirect()->route('admin.books')->with('success', 'Book updated successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating book: '.$e->getMessage())->withInput();
        }
    }

    public function destroy(Request $request)
    {
        $data = $request->validate([
            'book_id' => ['required', 'integer'],
        ]);

        $bookId = (int) $data['book_id'];

        try {
            DB::beginTransaction();

            DB::table('book_categories')->where('book_id', $bookId)->delete();
            DB::table('book_genres')->where('book_id', $bookId)->delete();
            DB::table('book_languages')->where('book_id', $bookId)->delete();
            DB::table('book_writers')->where('book_id', $bookId)->delete();
            DB::table('cart')->where('book_id', $bookId)->delete();
            DB::table('order_items')->where('book_id', $bookId)->delete();
            DB::table('questions')->where('book_id', $bookId)->delete();
            DB::table('reviews')->where('book_id', $bookId)->delete();
            DB::table('wishlist')->where('book_id', $bookId)->delete();

            DB::table('books')->where('book_id', $bookId)->delete();

            DB::commit();
            return redirect()->route('admin.books')->with('success', "Book #{$bookId} deleted successfully!");
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->route('admin.books')->with('error', 'Error deleting book: ' . $e->getMessage());
        }
    }
}
