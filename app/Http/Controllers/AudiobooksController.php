<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AudiobooksController extends Controller
{
    public function index(Request $request)
    {
        $error_message = '';
        $success_message = session('success') ?? '';
        $edit_mode = false;
        $audiobook_to_edit = null;

        try {
            // Fetch all audiobooks
            $audiobooks = DB::table('audiobooks')
                ->select('audiobook_id','title','writer','genre','category','language',
                         'audio_url','poster_url','description','duration','status','created_at')
                ->orderByDesc('created_at')
                ->get();

            // If edit mode requested via GET ?edit=ID
            if ($request->filled('edit')) {
                $id = (int) $request->query('edit');
                $audiobook_to_edit = DB::table('audiobooks')->where('audiobook_id', $id)->first();
                if ($audiobook_to_edit) {
                    $edit_mode = true;
                }
            }

        } catch (\Throwable $e) {
            $error_message = 'Error fetching audiobooks: ' . $e->getMessage();
            $audiobooks = collect();
        }

        return view('admin.audiobooks', compact(
            'audiobooks', 'error_message', 'success_message', 'edit_mode', 'audiobook_to_edit'
        ));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'audiobook_id'  => ['required','integer'],
            'title'         => ['required','string','max:255'],
            'writer'        => ['required','string','max:255'],
            'genre'         => ['required','string','max:255'],
            'category'      => ['required','string','max:255'],
            'language'      => ['nullable','string','max:255'],
            'description'   => ['nullable','string'],
            'duration'      => ['nullable','string'], // HH:MM:SS
            'status'        => ['required','in:visible,hidden,pending'],
            'current_poster'=> ['nullable','string'],
            'current_audio' => ['nullable','string'],
            'poster'        => ['nullable','file','mimes:jpg,jpeg,png,webp'],
            'audio'         => ['nullable','file','mimes:mp3,m4a,wav,ogg'],
        ]);

        $id = (int) $data['audiobook_id'];
        $poster = $data['current_poster'] ?? null;
        $audio  = $data['current_audio'] ?? null;

        try {
            // Poster upload
            if ($request->hasFile('poster')) {
                $dir = public_path('assets/audiobook_covers');
                if (!is_dir($dir)) @mkdir($dir, 0755, true);

                $ext = $request->file('poster')->getClientOriginalExtension();
                $filename = 'audiobook_'.Str::random(16).'.'.$ext;
                $request->file('poster')->move($dir, $filename);

                // remove old
                if ($poster) {
                    $old = public_path($poster);
                    if (is_file($old)) @unlink($old);
                }
                $poster = 'assets/audiobook_covers/'.$filename;
            }

            // Audio upload
            if ($request->hasFile('audio')) {
                $dir = public_path('assets/audiobooks');
                if (!is_dir($dir)) @mkdir($dir, 0755, true);

                $ext = $request->file('audio')->getClientOriginalExtension();
                $filename = 'audio_'.Str::random(16).'.'.$ext;
                $request->file('audio')->move($dir, $filename);

                // remove old
                if ($audio) {
                    $old = public_path($audio);
                    if (is_file($old)) @unlink($old);
                }
                $audio = 'assets/audiobooks/'.$filename;
            }

            DB::table('audiobooks')->where('audiobook_id', $id)->update([
                'title'       => $data['title'],
                'writer'      => $data['writer'],
                'genre'       => $data['genre'],
                'category'    => $data['category'],
                'language'    => $data['language'] ?? null,
                'audio_url'   => $audio,
                'poster_url'  => $poster,
                'description' => $data['description'] ?? null,
                'duration'    => $data['duration'] ?? null,
                'status'      => $data['status'],
                'updated_at'  => now(),
            ]);

            return redirect()->route('admin.audiobooks')->with('success', 'Audiobook updated successfully!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Error updating audiobook: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'audiobook_id' => ['required','integer'],
        ]);
        $id = (int) $request->audiobook_id;

        try {
            $row = DB::table('audiobooks')->select('poster_url','audio_url')->where('audiobook_id', $id)->first();

            DB::table('audiobooks')->where('audiobook_id', $id)->delete();

            if ($row) {
                if ($row->poster_url) {
                    $p = public_path($row->poster_url);
                    if (is_file($p)) @unlink($p);
                }
                if ($row->audio_url) {
                    $a = public_path($row->audio_url);
                    if (is_file($a)) @unlink($a);
                }
            }

            return redirect()->route('admin.audiobooks')->with('success', 'Audiobook deleted successfully!');
        } catch (\Throwable $e) {
            return redirect()->route('admin.audiobooks')->with('error', 'Error deleting audiobook: ' . $e->getMessage());
        }
    }
}
