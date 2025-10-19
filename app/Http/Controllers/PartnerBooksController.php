<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartnerBooksController extends Controller
{
    public function index()
    {
        $error_message = '';
        $success_message = session('success') ?? '';

        try {
            // Stats
            $stats = [
                'total_books'   => (int) DB::table('partner_books')->count(),
                'pending'       => (int) DB::table('partner_books')->where('status', 'pending')->count(),
                'visible'       => (int) DB::table('partner_books')->where('status', 'visible')->count(),
                'on_rent'       => (int) DB::table('partner_books')->where('status', 'on rent')->count(),
                'return_apply'  => (int) DB::table('partner_books')->where('status', 'return apply')->count(),
            ];

            // Pending
            $pendingBooks = DB::table('partner_books as pb')
                ->join('rent_books as rb', 'pb.rent_book_id', '=', 'rb.rent_book_id')
                ->join('partners as p', 'pb.partner_id', '=', 'p.partner_id')
                ->join('users as u', 'p.user_id', '=', 'u.user_id')
                ->select('pb.id','pb.added_at','pb.status','rb.title','rb.writer','rb.genre','rb.poster_url',
                         'p.partner_id','u.username as partner_name')
                ->where('pb.status', 'pending')
                ->orderByDesc('pb.added_at')
                ->get();

            // On rent
            $onRentBooks = DB::table('partner_books as pb')
                ->join('rent_books as rb', 'pb.rent_book_id', '=', 'rb.rent_book_id')
                ->join('partners as p', 'pb.partner_id', '=', 'p.partner_id')
                ->join('users as u', 'p.user_id', '=', 'u.user_id')
                ->join('user_subscription_rent_book_access as usra', 'pb.rent_book_id', '=', 'usra.rent_book_id')
                ->join('users as usr', 'usra.user_id', '=', 'usr.user_id')
                ->select('pb.id','pb.added_at','pb.status','rb.title','rb.writer','rb.genre','rb.poster_url',
                         'p.partner_id','u.username as partner_name','usra.user_id','usr.username as renter_name')
                ->where('pb.status', 'on rent')
                ->orderByDesc('pb.added_at')
                ->get();

            // Return apply
            $returnApplyBooks = DB::table('partner_books as pb')
                ->join('rent_books as rb', 'pb.rent_book_id', '=', 'rb.rent_book_id')
                ->join('partners as p', 'pb.partner_id', '=', 'p.partner_id')
                ->join('users as u', 'p.user_id', '=', 'u.user_id')
                ->join('user_subscription_rent_book_access as usra', 'pb.rent_book_id', '=', 'usra.rent_book_id')
                ->join('users as usr', 'usra.user_id', '=', 'usr.user_id')
                ->select('pb.id','pb.added_at','pb.status','rb.title','rb.writer','rb.genre','rb.poster_url',
                         'p.partner_id','u.username as partner_name','usra.user_id','usr.username as renter_name')
                ->where('pb.status', 'return apply')
                ->orderByDesc('pb.added_at')
                ->get();

        } catch (\Throwable $e) {
            $error_message = 'Error fetching books: ' . $e->getMessage();
            $stats = ['total_books'=>0,'pending'=>0,'visible'=>0,'on_rent'=>0,'return_apply'=>0];
            $pendingBooks = collect(); $onRentBooks = collect(); $returnApplyBooks = collect();
        }

        return view('admin.partnerbooks', compact(
            'stats','pendingBooks','onRentBooks','returnApplyBooks','error_message','success_message'
        ));
    }

    public function approve(Request $request)
    {
        $request->validate([
            'book_id' => ['required','integer']
        ]);

        try {
            DB::table('partner_books')->where('id', (int)$request->book_id)
                ->update(['status' => 'visible']);
            return redirect()->route('admin.partnerbooks')->with('success', 'Book approved and made visible!');
        } catch (\Throwable $e) {
            return redirect()->route('admin.partnerbooks')->with('error', 'Error approving book: '.$e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'book_id' => ['required','integer']
        ]);

        try {
            DB::table('partner_books')->where('id', (int)$request->book_id)->delete();
            return redirect()->route('admin.partnerbooks')->with('success', 'Book deleted successfully!');
        } catch (\Throwable $e) {
            return redirect()->route('admin.partnerbooks')->with('error', 'Error deleting book: '.$e->getMessage());
        }
    }

    public function setReturnDate(Request $request)
    {
        $data = $request->validate([
            'book_id'     => ['required','integer'],
            'return_date' => ['required','date']
        ]);

        try {
            DB::table('partner_books')->where('id', (int)$data['book_id'])
                ->update([
                    'return_date' => $data['return_date'],  // <- fixed column name
                    'status'      => 'return apply'         // <- normalized state
                ]);

            return redirect()->route('admin.partnerbooks')->with('success', 'Return date set successfully!');
        } catch (\Throwable $e) {
            return redirect()->route('admin.partnerbooks')->with('error', 'Error setting return date: '.$e->getMessage());
        }
    }
}
