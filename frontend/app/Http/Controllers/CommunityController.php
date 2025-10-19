<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CommunityController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $discoverCommunities = DB::table('communities as c')
            ->leftJoin('community_members as cm', 'c.community_id', '=', 'cm.community_id')
            ->whereNotIn('c.community_id', function ($q) use ($userId) {
                $q->select('community_id')
                  ->from('community_members')
                  ->where('user_id', $userId);
            })
            ->where('c.status', 'active')
            ->select('c.*', DB::raw('COUNT(cm.user_id) as member_count'))
            ->groupBy('c.community_id')
            ->orderByDesc('member_count')
            ->limit(10)
            ->get();

        $userCommunities = DB::table('communities as c')
            ->join('community_members as cm', 'c.community_id', '=', 'cm.community_id')
            ->leftJoin('community_members as cm2', 'c.community_id', '=', 'cm2.community_id')
            ->where('cm.user_id', $userId)
            ->where('cm.status', 'active')
            ->where('c.status', 'active')
            ->select('c.*', 'cm.role', DB::raw('COUNT(cm2.user_id) as member_count'))
            ->groupBy('c.community_id')
            ->orderByDesc('c.created_at')
            ->get();

        return view('community.dashboard', compact('discoverCommunities', 'userCommunities'));
    }

    public function create(Request $request)
    {
        $userId = Auth::id();

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('community_images', 'public');
        }

        $communityId = DB::table('communities')->insertGetId([
            'name' => $request->community_name,
            'description' => $request->community_description,
            'created_by' => $userId,
            'cover_image_url' => $coverPath,
            'privacy' => $request->privacy,
            'status' => 'active',
            'created_at' => now(),
        ]);

        DB::table('community_members')->insert([
            'community_id' => $communityId,
            'user_id' => $userId,
            'role' => 'admin',
            'status' => 'active',
            'joined_at' => now(),
        ]);

        return redirect()->route('community.dashboard')->with('success', 'Community created successfully!');
    }

    public function join(Request $request)
    {
        $userId = Auth::id();
        $communityId = $request->community_id;

        $already = DB::table('community_members')
            ->where('community_id', $communityId)
            ->where('user_id', $userId)
            ->exists();

        if (!$already) {
            DB::table('community_members')->insert([
                'community_id' => $communityId,
                'user_id' => $userId,
                'role' => 'member',
                'status' => 'active',
                'joined_at' => now(),
            ]);
        }

        return redirect()->route('community.dashboard');
    }

    public function show($id)
    {
        $userId = Auth::id();

        $community = DB::table('communities as c')
            ->join('users as u', 'c.created_by', '=', 'u.user_id')
            ->select('c.*', 'u.username as creator_name', 'u.user_profile as creator_avatar')
            ->where('c.community_id', $id)
            ->where('c.status', 'active')
            ->first();

        if (!$community) {
            return redirect()->route('community.dashboard')->withErrors('Community not found.');
        }

        $posts = DB::table('community_posts as p')
            ->join('users as u', 'p.user_id', '=', 'u.user_id')
            ->select(
                'p.*',
                'u.username',
                'u.user_profile',
                DB::raw('(SELECT COUNT(*) FROM post_likes WHERE post_id = p.post_id) as like_count'),
                DB::raw('(SELECT COUNT(*) FROM post_comments WHERE post_id = p.post_id AND status = "active") as comment_count')
            )
            ->where('p.community_id', $id)
            ->where('p.status', 'active')
            ->orderByDesc('p.created_at')
            ->get();

        foreach ($posts as $post) {
            $post->comments = DB::table('post_comments as c')
                ->join('users as u', 'c.user_id', '=', 'u.user_id')
                ->where('c.post_id', $post->post_id)
                ->where('c.status', 'active')
                ->select('c.*', 'u.username', 'u.user_profile')
                ->get();

            $post->is_liked = DB::table('post_likes')
                ->where('post_id', $post->post_id)
                ->where('user_id', $userId)
                ->exists();

            $post->can_edit = ($post->user_id == $userId);
        }

        return view('community.feed', compact('community', 'posts'));
    }

    public function createPost(Request $request, $id)
    {
        $userId = Auth::id();
        $imagePath = null;
        if ($request->hasFile('post_image')) {
            $imagePath = $request->file('post_image')->store('post_images', 'public');
        }

        DB::table('community_posts')->insert([
            'community_id' => $id,
            'user_id' => $userId,
            'content' => $request->content,
            'image_url' => $imagePath,
            'status' => 'active',
            'created_at' => now(),
        ]);

        return redirect()->back();
    }

    public function toggleLike(Request $request, $id)
    {
        $userId = Auth::id();
        $postId = $request->post_id;

        $liked = DB::table('post_likes')
            ->where('post_id', $postId)
            ->where('user_id', $userId)
            ->exists();

        if ($liked) {
            DB::table('post_likes')->where('post_id', $postId)->where('user_id', $userId)->delete();
        } else {
            DB::table('post_likes')->insert(['post_id' => $postId, 'user_id' => $userId]);
        }

        return response()->json(['success' => true]);
    }

    public function addComment(Request $request, $id)
    {
        $userId = Auth::id();

        $commentId = DB::table('post_comments')->insertGetId([
            'post_id' => $request->post_id,
            'user_id' => $userId,
            'content' => $request->content,
            'status' => 'active',
            'created_at' => now(),
        ]);

        $comment = DB::table('post_comments as c')
            ->join('users as u', 'c.user_id', '=', 'u.user_id')
            ->where('c.comment_id', $commentId)
            ->select('u.username', 'u.user_profile', 'c.content', 'c.created_at')
            ->first();

        return response()->json(['success' => true, 'comment' => $comment]);
    }

    public function deletePost($id, $postId)
    {
        $userId = Auth::id();
        $owner = DB::table('community_posts')->where('post_id', $postId)->value('user_id');

        if ($owner == $userId) {
            DB::table('community_posts')->where('post_id', $postId)->update(['status' => 'deleted']);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'error' => 'Unauthorized']);
    }

    public function updatePost(Request $request, $id, $postId)
    {
        $userId = Auth::id();
        $owner = DB::table('community_posts')->where('post_id', $postId)->value('user_id');

        if ($owner == $userId) {
            DB::table('community_posts')->where('post_id', $postId)->update(['content' => $request->content]);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'error' => 'Unauthorized']);
    }

    public function members($id)
    {
        $members = DB::table('community_members as cm')
            ->join('users as u', 'cm.user_id', '=', 'u.user_id')
            ->select('u.username', 'u.user_profile', 'cm.role')
            ->where('cm.community_id', $id)
            ->where('cm.status', 'active')
            ->get();

        return view('community.members', compact('members'));
    }
}
