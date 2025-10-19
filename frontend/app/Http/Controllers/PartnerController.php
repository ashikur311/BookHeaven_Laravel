<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class PartnerController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Fetch partner data with username
        $partner = DB::table('partners as p')
            ->join('users as u', 'p.user_id', '=', 'u.user_id')
            ->where('p.user_id', $userId)
            ->select('p.*', 'u.username')
            ->first();

        if (!$partner) {
            // not a partner yet
            return view('partner.become');
        }

        if ($partner->status === 'pending') {
            // waiting for approval
            return view('partner.pending', compact('partner'));
        }

        // Active partner dashboard
        $partnerId = $partner->partner_id;

        // Active books
        $activeBooks = DB::table('partner_books as pb')
            ->join('rent_books as rb', 'pb.rent_book_id', '=', 'rb.rent_book_id')
            ->where('pb.partner_id', $partnerId)
            ->whereIn('pb.status', ['visible', 'on rent', 'pending'])
            ->select('pb.*', 'rb.title', 'rb.writer', 'rb.genre')
            ->get();

        // Return books
        $returnBooks = DB::table('partner_books as pb')
            ->join('rent_books as rb', 'pb.rent_book_id', '=', 'rb.rent_book_id')
            ->where('pb.partner_id', $partnerId)
            ->whereIn('pb.status', ['return apply', 'return'])
            ->select('pb.*', 'rb.title', 'rb.writer', 'rb.genre')
            ->get();

        // Stats
        $totalBooks = $activeBooks->count() + $returnBooks->count();
        $inRent = $activeBooks->where('status', 'on rent')->count();
        $returnRequests = $returnBooks->where('status', 'return apply')->count();
        $totalIncome = $activeBooks->sum('revenue') + $returnBooks->sum('revenue');

        return view('partner.dashboard', compact(
            'partner',
            'activeBooks',
            'returnBooks',
            'totalBooks',
            'inRent',
            'returnRequests',
            'totalIncome'
        ));
    }

    public function becomePartner(Request $request)
    {
        $userId = Auth::id();

        $exists = DB::table('partners')->where('user_id', $userId)->exists();
        if ($exists) return back()->with('error', 'You have already applied or are a partner.');

        DB::table('partners')->insert([
            'user_id' => $userId,
            'status' => 'pending',
            'joined_at' => now()
        ]);

        return back()->with('success', 'Your partner application has been submitted for admin approval.');
    }

    public function applyReturn(Request $request)
    {
        $bookId = $request->input('book_id');
        $partnerId = DB::table('partners')->where('user_id', Auth::id())->value('partner_id');

        $updated = DB::table('partner_books')
            ->where('id', $bookId)
            ->where('partner_id', $partnerId)
            ->update(['status' => 'return apply']);

        return back()->with($updated ? 'success' : 'error', $updated
            ? 'Return request submitted successfully!'
            : 'Failed to submit return request.');
    }

    public function deleteReturn(Request $request)
    {
        $bookId = $request->input('book_id');
        $partnerId = DB::table('partners')->where('user_id', Auth::id())->value('partner_id');

        $rentBookId = DB::table('partner_books')
            ->where('id', $bookId)
            ->where('partner_id', $partnerId)
            ->value('rent_book_id');

        if (!$rentBookId)
            return back()->with('error', 'Book not found or permission denied.');

        DB::beginTransaction();
        try {
            DB::table('partner_books')->where('id', $bookId)->delete();
            DB::table('rent_books')->where('rent_book_id', $rentBookId)->delete();
            DB::commit();
            return back()->with('success', 'Book removed successfully from both lists!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to remove book: ' . $e->getMessage());
        }
    }


    public function addBook()
{
    $userId = Auth::id();
    $partner = DB::table('partners')->where('user_id', $userId)->first();
    if (!$partner) {
        return redirect()->route('partner.dashboard')->with('error', 'You must be a partner to add books.');
    }

    $username = DB::table('users')->where('user_id', $userId)->value('username');
    return view('partner.add_book', compact('partner', 'username'));
}

public function storeBook(Request $request)
{
    $userId = Auth::id();
    $partner = DB::table('partners')->where('user_id', $userId)->first();
    if (!$partner) {
        return redirect()->route('partner.dashboard')->with('error', 'You are not a registered partner.');
    }

    $request->validate([
        'bookTitle' => 'required|string|max:255',
        'bookWriter' => 'required|string|max:255',
        'bookGenre' => 'required|string|max:255',
        'bookLanguage' => 'nullable|string|max:100',
        'bookDescription' => 'required|string',
        'bookCover' => 'required|image|mimes:jpeg,png,gif|max:5120'
    ]);

    DB::beginTransaction();
    try {
        // Upload cover
        $coverPath = $request->file('bookCover')->store('rent_book_covers', 'public');

        // Insert into rent_books
        $rentBookId = DB::table('rent_books')->insertGetId([
            'title' => $request->bookTitle,
            'writer' => $request->bookWriter,
            'genre' => $request->bookGenre,
            'language' => $request->bookLanguage ?? 'English',
            'poster_url' => 'storage/' . $coverPath,
            'description' => $request->bookDescription,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert into partner_books
        DB::table('partner_books')->insert([
            'partner_id' => $partner->partner_id,
            'rent_book_id' => $rentBookId,
            'added_at' => now(),
            'status' => 'pending',
            'revenue' => 0,
        ]);

        DB::commit();
        return back()->with('success', 'Book submitted successfully! It will be reviewed by our team.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Error submitting book: ' . $e->getMessage());
    }
}

}
