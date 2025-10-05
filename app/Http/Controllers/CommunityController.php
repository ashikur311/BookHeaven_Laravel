<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommunityController extends Controller
{
    public function index()
    {
        // Stats
        $stats = [
            'total_communities'  => 0,
            'active_communities' => 0,
            'banned_communities' => 0,
            'total_members'      => 0,
        ];

        try {
            $stats['total_communities']  = (int) DB::table('communities')->count();
            $stats['active_communities'] = (int) DB::table('communities')->where('status', 'active')->count();
            $stats['banned_communities'] = (int) DB::table('communities')->where('status', 'banned')->count();
            $stats['total_members']      = (int) DB::table('community_members')->count();

            // Communities + creator + member count
            $communities = DB::table('communities as c')
                ->join('users as u', 'c.created_by', '=', 'u.user_id')
                ->leftJoin('community_members as cm', 'c.community_id', '=', 'cm.community_id')
                ->select([
                    'c.community_id',
                    'c.name',
                    'c.description',
                    'c.created_at',
                    'c.cover_image_url',
                    'c.privacy',
                    'c.status',
                    'u.user_id as creator_id',
                    'u.username as creator_name',
                    'u.user_profile as creator_image',
                    DB::raw('COUNT(cm.user_id) as member_count'),
                ])
                ->groupBy([
                    'c.community_id', 'c.name', 'c.description', 'c.created_at', 'c.cover_image_url', 'c.privacy', 'c.status',
                    'u.user_id', 'u.username', 'u.user_profile'
                ])
                ->orderBy('c.community_id', 'asc')
                ->get();

        } catch (\Throwable $e) {
            return view('admin.community', [
                'stats'        => $stats,
                'communities'  => collect(),
                'error_message'=> 'Error fetching communities: '.$e->getMessage(),
                'success_message' => session('success_message')
            ]);
        }

        return view('admin.community', [
            'stats'           => $stats,
            'communities'     => $communities,
            'error_message'   => session('error_message'),
            'success_message' => session('success_message'),
        ]);
    }

    public function destroy(Request $request)
    {
        $data = $request->validate([
            'community_id' => ['required','integer'],
        ]);
        $communityId = (int) $data['community_id'];

        try {
            DB::beginTransaction();

            // Delete messages
            DB::table('community_messages')->where('community_id', $communityId)->delete();

            // Posts in this community
            $postIds = DB::table('community_posts')
                ->where('community_id', $communityId)
                ->pluck('post_id');

            if ($postIds->isNotEmpty()) {
                // Likes
                DB::table('post_likes')->whereIn('post_id', $postIds)->delete();
                // Comments
                DB::table('post_comments')->whereIn('post_id', $postIds)->delete();
            }

            // Posts
            DB::table('community_posts')->where('community_id', $communityId)->delete();
            // Members
            DB::table('community_members')->where('community_id', $communityId)->delete();
            // Finally community
            DB::table('communities')->where('community_id', $communityId)->delete();

            DB::commit();

            return redirect()
                ->route('admin.community')
                ->with('success_message', 'Community deleted successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()
                ->route('admin.community')
                ->with('error_message', 'Error deleting community: '.$e->getMessage());
        }
    }
}
