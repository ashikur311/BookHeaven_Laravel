<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        // Stats (kept same logic as your PHP)
        $stats = [
            'total_users'    => 0,
            'new_this_month' => 0,
            'active_users'   => 0,
            'inactive_users' => 0,
        ];

        try {
            // 1) Total users
            $stats['total_users'] = (int) DB::table('users')->count();

            // 2) New this month
            $stats['new_this_month'] = (int) DB::selectOne("
                SELECT COUNT(*) AS c
                FROM users
                WHERE create_time >= DATE_FORMAT(CURRENT_DATE(), '%Y-%m-01')
                  AND create_time <  DATE_FORMAT(CURRENT_DATE() + INTERVAL 1 MONTH, '%Y-%m-01')
            ")->c ?? 0;

            // 3) Active users (distinct user_ids in user_activities with status='active')
            $stats['active_users'] = (int) DB::table('user_activities')
                ->where('status', 'active')
                ->distinct('user_id')
                ->count('user_id');

            // 4) Inactive = total - active
            $stats['inactive_users'] = $stats['total_users'] - $stats['active_users'];

            // User list (with profile, phone, address)
            $users = DB::select("
                SELECT u.user_id, u.username, u.email, u.create_time,
                       u.user_profile, ui.phone, ui.address
                FROM users u
                LEFT JOIN user_info ui ON u.user_id = ui.user_id
                ORDER BY u.user_id ASC
            ");
        } catch (\Throwable $e) {
            return back()->with('error', 'Error fetching users: ' . $e->getMessage());
        }

        return view('admin.users', [
            'stats' => $stats,
            'users' => $users,
        ]);
    }

    public function destroy(Request $request)
    {
        // Simple deletion handler (adjust to your business rules if needed)
        $request->validate([
            'user_id' => ['required', 'integer'],
        ]);

        try {
            $userId = (int) $request->input('user_id');

            // If you have FKs/cascades, you may only need to delete users row.
            // Otherwise, clean related tables first as you did in legacy code.

            // Example: remove dependent rows in user_info / user_activities first (optional)
            DB::table('user_info')->where('user_id', $userId)->delete();
            DB::table('user_activities')->where('user_id', $userId)->delete();

            // Finally delete user
            DB::table('users')->where('user_id', $userId)->delete();

            return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.users')->with('error', 'Error deleting user: ' . $e->getMessage());
        }
    }
}
