<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WritersController extends Controller
{
    public function index(Request $request)
    {
        $error_message = '';
        $success_message = '';
        $edit_mode = false;
        $writer_to_edit = null;

        try {
            // Stats
            $total_writers = (int) DB::table('writers')->count();

            // Writers with book counts
            $writers = DB::select("
                SELECT 
                    w.writer_id,
                    w.name,
                    w.email,
                    w.bio,
                    w.address,
                    w.image_url,
                    COUNT(bw.book_id) AS book_count
                FROM writers w
                LEFT JOIN book_writers bw ON w.writer_id = bw.writer_id
                GROUP BY w.writer_id, w.name, w.email, w.bio, w.address, w.image_url
                ORDER BY w.name
            ");

            // Edit mode (via ?edit=ID)
            if ($request->filled('edit')) {
                $edit_id = (int) $request->query('edit');
                $writer_to_edit = DB::table('writers')->where('writer_id', $edit_id)->first();
                if ($writer_to_edit) {
                    $edit_mode = true;
                }
            }
        } catch (\Throwable $e) {
            $error_message = 'Error fetching writers: ' . $e->getMessage();
        }

        // Prolific writer & totals (same as your PHP page)
        $most_books = 0;
        $prolific_writer = '';
        $total_books = 0;
        foreach ($writers ?? [] as $w) {
            $total_books += (int) $w->book_count;
            if ((int) $w->book_count > $most_books) {
                $most_books = (int) $w->book_count;
                $prolific_writer = $w->name;
            }
        }

        $stats = [
            'total_writers'  => $total_writers,
            'prolific_name'  => $prolific_writer ?: 'None',
            'total_books'    => $total_books,
        ];

        return view('admin.writers', compact(
            'stats', 'writers', 'error_message', 'success_message', 'edit_mode', 'writer_to_edit'
        ));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'writer_id'     => ['required', 'integer'],
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['nullable', 'email', 'max:255'],
            'bio'           => ['nullable', 'string'],
            'address'       => ['nullable', 'string', 'max:255'],
            'current_image' => ['nullable', 'string'], // relative path like assets/writer_images/abc.jpg
            'image'         => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp'],
        ]);

        try {
            $image_url = $data['current_image'] ?? null;

            // If new image provided
            if ($request->hasFile('image')) {
                // Save to public/assets/writer_images
                $dir = public_path('assets/writer_images');
                if (!is_dir($dir)) {
                    @mkdir($dir, 0755, true);
                }

                $ext = $request->file('image')->getClientOriginalExtension();
                $filename = 'writer_'.Str::random(16).'.'.$ext;
                $request->file('image')->move($dir, $filename);

                // Remove old file if it exists and is local
                if ($image_url) {
                    $oldPath = public_path($image_url);
                    if (is_file($oldPath)) {
                        @unlink($oldPath);
                    }
                }

                // Store relative URL just like legacy
                $image_url = 'assets/writer_images/'.$filename;
            }

            DB::table('writers')
                ->where('writer_id', (int) $data['writer_id'])
                ->update([
                    'name'      => $data['name'],
                    'email'     => $data['email'] ?? null,
                    'bio'       => $data['bio'] ?? null,
                    'address'   => $data['address'] ?? null,
                    'image_url' => $image_url,
                ]);

            return redirect()->route('admin.writers')->with('success', 'Writer updated successfully!');
        } catch (\Throwable $e) {
            return redirect()->route('admin.writers', ['edit' => (int) $data['writer_id']])
                ->with('error', 'Error updating writer: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'writer_id' => ['required', 'integer'],
        ]);

        $id = (int) $request->writer_id;

        try {
            // Get image to delete
            $writer = DB::table('writers')->select('image_url')->where('writer_id', $id)->first();

            // Delete from junction table
            DB::table('book_writers')->where('writer_id', $id)->delete();

            // Delete writer
            DB::table('writers')->where('writer_id', $id)->delete();

            // Delete local image if present
            if ($writer && $writer->image_url) {
                $full = public_path($writer->image_url);
                if (is_file($full)) {
                    @unlink($full);
                }
            }

            return redirect()->route('admin.writers')->with('success', 'Writer deleted successfully!');
        } catch (\Throwable $e) {
            return redirect()->route('admin.writers')->with('error', 'Error deleting writer: ' . $e->getMessage());
        }
    }
}
