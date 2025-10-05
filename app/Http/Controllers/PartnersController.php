<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartnersController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total_partners'    => 0,
            'new_this_month'    => 0,
            'pending_partners'  => 0,
            'approved_partners' => 0,
        ];

        try {
            // Totals
            $stats['total_partners'] = (int) DB::table('partners')->count();

            $stats['new_this_month'] = (int) DB::selectOne("
                SELECT COUNT(*) AS c
                FROM partners
                WHERE joined_at >= DATE_FORMAT(CURRENT_DATE(), '%Y-%m-01')
                  AND joined_at <  DATE_FORMAT(CURRENT_DATE() + INTERVAL 1 MONTH, '%Y-%m-01')
            ")->c ?? 0;

            $stats['pending_partners']  = (int) DB::table('partners')->where('status', 'pending')->count();
            $stats['approved_partners'] = (int) DB::table('partners')->where('status', 'approved')->count();

            // Pending
            $pendingPartners = DB::select("
                SELECT p.partner_id, p.joined_at,
                       u.username, u.email, u.user_profile,
                       ui.phone, ui.address,
                       (SELECT COUNT(*) FROM partner_books pb WHERE pb.partner_id = p.partner_id) AS book_count
                FROM partners p
                JOIN users u ON p.user_id = u.user_id
                LEFT JOIN user_info ui ON p.user_id = ui.user_id
                WHERE p.status = 'pending'
                ORDER BY p.joined_at DESC
            ");

            // Approved
            $approvedPartners = DB::select("
                SELECT p.partner_id, p.joined_at,
                       u.username, u.email, u.user_profile,
                       ui.phone, ui.address,
                       (SELECT COUNT(*) FROM partner_books pb WHERE pb.partner_id = p.partner_id) AS book_count
                FROM partners p
                JOIN users u ON p.user_id = u.user_id
                LEFT JOIN user_info ui ON p.user_id = ui.user_id
                WHERE p.status = 'approved'
                ORDER BY p.joined_at DESC
            ");
        } catch (\Throwable $e) {
            return back()->with('error', 'Error fetching partners: ' . $e->getMessage());
        }

        return view('admin.partners', compact('stats', 'pendingPartners', 'approvedPartners'));
    }

    public function approve(Request $request)
    {
        $request->validate([
            'partner_id' => ['required', 'integer'],
        ]);

        try {
            DB::table('partners')
                ->where('partner_id', (int) $request->partner_id)
                ->update(['status' => 'approved']);

            return redirect()->route('admin.partners')
                ->with('success', "Partner #{$request->partner_id} approved!");
        } catch (\Throwable $e) {
            return redirect()->route('admin.partners')
                ->with('error', 'Error approving partner: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'partner_id' => ['required', 'integer'],
        ]);

        try {
            $id = (int) $request->partner_id;

            // Remove related books
            DB::table('partner_books')->where('partner_id', $id)->delete();
            // Delete partner
            DB::table('partners')->where('partner_id', $id)->delete();

            return redirect()->route('admin.partners')->with('success', "Partner #{$id} deleted.");
        } catch (\Throwable $e) {
            return redirect()->route('admin.partners')->with('error', 'Error deleting partner: ' . $e->getMessage());
        }
    }
}
