<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;

class AddController extends Controller
{
    public function index(Request $request)
    {
        try {
            $genres     = DB::table('genres')->get();
            $categories = DB::table('categories')->get();
            $writers    = DB::table('writers')->get();
            $languages  = DB::table('languages')->where('status', 'active')->get();
        } catch (\Throwable $e) {
            return back()->with('error', 'Error fetching data: '.$e->getMessage());
        }

        return view('admin.add', compact('genres', 'categories', 'writers', 'languages'));
    }

    public function storeBook(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'published'   => ['required', 'date'],
            'price'       => ['nullable', 'numeric', 'min:0'],
            'quantity'    => ['required', 'integer', 'min:1'],
            'rating'      => ['required', 'numeric', 'min:1', 'max:5'],
            'details'     => ['nullable', 'string'],
            'writer_id'   => ['required', 'integer'],
            'genre_id'    => ['required', 'integer'],
            'category_id' => ['required', 'integer'],
            'language_id' => ['required', 'integer'],
            'cover_image' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif', 'max:4096'],
        ]);

        // Handle cover image to public/assets/book_covers
        $cover_image_url = '';
        if ($request->hasFile('cover_image')) {
            $dir = public_path('assets/book_covers');
            if (!File::exists($dir)) File::makeDirectory($dir, 0755, true);
            $ext  = $request->file('cover_image')->getClientOriginalExtension();
            $name = preg_replace('/[^a-zA-Z0-9]/', '_', $validated['title']).'_'.time().'.'.$ext;
            $request->file('cover_image')->move($dir, $name);
            $cover_image_url = 'assets/book_covers/'.$name; // relative path (like legacy)
        }

        try {
            // Insert book
            $book_id = DB::table('books')->insertGetId([
                'title'          => $validated['title'],
                'published'      => $validated['published'],
                'price'          => $validated['price'] ?? 0,
                'quantity'       => $validated['quantity'],
                'details'        => $validated['details'] ?? '',
                'cover_image_url'=> $cover_image_url,
                'rating'         => $validated['rating'],
            ]);

            // Link tables
            DB::table('book_writers')->insert([
                'book_id'   => $book_id,
                'writer_id' => $validated['writer_id'],
            ]);

            DB::table('book_genres')->insert([
                'book_id'  => $book_id,
                'genre_id' => $validated['genre_id'],
            ]);

            DB::table('book_categories')->insert([
                'book_id'     => $book_id,
                'category_id' => $validated['category_id'],
            ]);

            DB::table('book_languages')->insert([
                'book_id'     => $book_id,
                'language_id' => $validated['language_id'],
            ]);

            return redirect()->route('admin.add')->with('success', 'Book added successfully!');
        } catch (\Throwable $e) {
            return redirect()->route('admin.add')->with('error', 'Error adding book: '.$e->getMessage());
        }
    }

    public function storeAudiobook(Request $request)
    {
        $validated = $request->validate([
            'audio_title'        => ['required', 'string', 'max:255'],
            'audio_writer'       => ['required', 'string', 'max:255'],
            'audio_genre'        => ['required', 'string', 'max:255'],
            'audio_category'     => ['required', 'string', 'max:255'],
            'audio_language_id'  => ['required', 'integer'],
            'audio_description'  => ['nullable', 'string'],
            'audio_duration'     => ['required', 'string', 'max:20'],
            'audio_file'         => ['required', 'file', 'mimes:mp3,wav,aac', 'max:51200'], // 50MB
            'audio_poster'       => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif', 'max:4096'],
        ]);

        // Get language name by id
        $language_name = DB::table('languages')
            ->where('language_id', $validated['audio_language_id'])
            ->value('name') ?? '';

        // Upload audio
        $audio_url = '';
        if ($request->hasFile('audio_file')) {
            $dir = public_path('assets/audiobooks');
            if (!File::exists($dir)) File::makeDirectory($dir, 0755, true);
            $ext  = $request->file('audio_file')->getClientOriginalExtension();
            $name = preg_replace('/[^a-zA-Z0-9]/', '_', $validated['audio_title']).'_'.time().'.'.$ext;
            $request->file('audio_file')->move($dir, $name);
            $audio_url = 'assets/audiobooks/'.$name;
        }

        // Upload poster
        $poster_url = '';
        if ($request->hasFile('audio_poster')) {
            $dir = public_path('assets/audiobook_covers');
            if (!File::exists($dir)) File::makeDirectory($dir, 0755, true);
            $ext  = $request->file('audio_poster')->getClientOriginalExtension();
            $name = preg_replace('/[^a-zA-Z0-9]/', '_', $validated['audio_title']).'_'.time().'.'.$ext;
            $request->file('audio_poster')->move($dir, $name);
            $poster_url = 'assets/audiobook_covers/'.$name;
        }

        try {
            DB::table('audiobooks')->insert([
                'title'       => $validated['audio_title'],
                'writer'      => $validated['audio_writer'],
                'genre'       => $validated['audio_genre'],
                'category'    => $validated['audio_category'],
                'language'    => $language_name,
                'audio_url'   => $audio_url,
                'poster_url'  => $poster_url,
                'description' => $validated['audio_description'] ?? '',
                'duration'    => $validated['audio_duration'],
            ]);

            return redirect()->route('admin.add')->with('success', 'Audiobook added successfully!')->withFragment('audiobook-tab');
        } catch (\Throwable $e) {
            return redirect()->route('admin.add')->with('error', 'Error adding audiobook: '.$e->getMessage())->withFragment('audiobook-tab');
        }
    }

    public function storeSubscription(Request $request)
    {
        $validated = $request->validate([
            'plan_name'          => ['required', 'string', 'max:255'],
            'price'              => ['required', 'numeric', 'min:0'],
            'validity_days'      => ['required', 'integer', 'min:1'],
            'book_quantity'      => ['required', 'integer', 'min:0'],
            'audiobook_quantity' => ['required', 'integer', 'min:0'],
            'plan_description'   => ['nullable', 'string'],
            'status'             => ['required', 'in:active,inactive'],
        ]);

        try {
            DB::table('subscription_plans')->insert([
                'plan_name'          => $validated['plan_name'],
                'price'              => $validated['price'],
                'validity_days'      => $validated['validity_days'],
                'book_quantity'      => $validated['book_quantity'],
                'audiobook_quantity' => $validated['audiobook_quantity'],
                'description'        => $validated['plan_description'] ?? '',
                'status'             => $validated['status'],
            ]);

            return redirect()->route('admin.add')->with('success', 'Subscription plan added successfully!')->withFragment('subscription-tab');
        } catch (\Throwable $e) {
            return redirect()->route('admin.add')->with('error', 'Error adding subscription plan: '.$e->getMessage())->withFragment('subscription-tab');
        }
    }

    public function storeEvent(Request $request)
    {
        $validated = $request->validate([
            'event_name'        => ['required', 'string', 'max:255'],
            'event_venue'       => ['required', 'string', 'max:255'],
            'event_date'        => ['required', 'date'],
            'event_description' => ['nullable', 'string'],
            'event_status'      => ['required', 'in:upcoming,ongoing,completed,cancelled'],
            'event_banner'      => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif', 'max:4096'],
        ]);

        // Upload banner
        $banner_url = '';
        if ($request->hasFile('event_banner')) {
            $dir = public_path('assets/event_banners');
            if (!File::exists($dir)) File::makeDirectory($dir, 0755, true);
            $ext  = $request->file('event_banner')->getClientOriginalExtension();
            $name = preg_replace('/[^a-zA-Z0-9]/', '_', $validated['event_name']).'_'.time().'.'.$ext;
            $request->file('event_banner')->move($dir, $name);
            $banner_url = 'assets/event_banners/'.$name;
        }

        try {
            DB::table('events')->insert([
                'name'        => $validated['event_name'],
                'venue'       => $validated['event_venue'],
                'event_date'  => $validated['event_date'],
                'description' => $validated['event_description'] ?? '',
                'banner_url'  => $banner_url,
                'status'      => $validated['event_status'],
            ]);

            return redirect()->route('admin.add')->with('success', 'Event added successfully!')->withFragment('event-tab');
        } catch (\Throwable $e) {
            return redirect()->route('admin.add')->with('error', 'Error adding event: '.$e->getMessage())->withFragment('event-tab');
        }
    }

    // AJAX: add writer/genre/category/language
    public function addItem(Request $request)
    {
        // JSON response expected by the legacy JS
        try {
            $request->validate([
                'type'   => ['required', 'in:writer,genre,category,language'],
                'name'   => ['required', 'string', 'max:255'],
                'status' => ['nullable', 'in:active,inactive'],
            ]);

            $type   = $request->input('type');
            $name   = trim($request->input('name'));
            $status = $request->input('status', 'active');

            $table   = '';
            $idField = '';
            $nameCol = 'name';

            switch ($type) {
                case 'writer':
                    $table = 'writers';     $idField = 'writer_id';   break;
                case 'genre':
                    $table = 'genres';      $idField = 'genre_id';    break;
                case 'category':
                    $table = 'categories';  $idField = 'id';          break;
                case 'language':
                    $table = 'languages';   $idField = 'language_id'; break;
            }

            // Exists?
            $existing = DB::table($table)->where($nameCol, $name)->first();
            if ($existing) {
                return Response::json(['success' => true, 'message' => 'Item already exists', 'id' => $existing->{$idField}]);
            }

            // Insert
            $insertData = [$nameCol => $name];
            if ($type === 'language') $insertData['status'] = $status;

            $id = DB::table($table)->insertGetId($insertData);

            return Response::json(['success' => true, 'message' => 'Item added successfully', 'id' => $id]);
        } catch (\Throwable $e) {
            return Response::json(['success' => false, 'message' => $e->getMessage(), 'id' => null], 200);
        }
    }
}
