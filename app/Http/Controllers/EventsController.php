<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class EventsController extends Controller
{
    public function index()
    {
        // Quick stats
        $total_events   = (int) DB::table('events')->count();

        $upcoming_count = (int) DB::table('events')
            ->where('event_date', '>', now())
            ->where('status', '!=', 'cancelled')
            ->count();

        $finished_count = (int) DB::table('events')
            ->where('event_date', '<=', now())
            ->where('status', '!=', 'cancelled')
            ->count();

        $canceled_count = (int) DB::table('events')
            ->where('status', 'cancelled')
            ->count();

        // All events ordered (cancelled last, finished next, upcoming first)
        $all = DB::table('events')
            ->orderByRaw("
                CASE
                  WHEN status = 'cancelled' THEN 3
                  WHEN event_date <= NOW()   THEN 2
                  ELSE 1
                END
            ")
            ->orderByDesc('event_date')
            ->get();

        // Categorize
        $upcoming_events = [];
        $finished_events = [];
        $canceled_events = [];

        foreach ($all as $e) {
            if ($e->status === 'cancelled') {
                $canceled_events[] = $e;
            } elseif (Carbon::parse($e->event_date)->lessThanOrEqualTo(now())) {
                $finished_events[] = $e;
            } else {
                $upcoming_events[] = $e;
            }
        }

        return view('admin.events', compact(
            'total_events', 'upcoming_count', 'finished_count', 'canceled_count',
            'upcoming_events', 'finished_events', 'canceled_events'
        ));
    }

    public function edit($id)
    {
        $event = DB::table('events')->where('event_id', $id)->first();

        if (!$event) {
            return redirect()
                ->route('admin.events')
                ->with('error_message', 'Event not found!');
        }

        return view('admin.events_edit', compact('event'));
    }

    public function update(Request $request, $id)
    {
        $event = DB::table('events')->where('event_id', $id)->first();

        if (!$event) {
            return redirect()
                ->route('admin.events')
                ->with('error_message', 'Event not found!');
        }

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'venue'       => ['required', 'string', 'max:255'],
            'event_date'  => ['required', 'date'],
            'description' => ['required', 'string'],
            'status'      => ['required', 'in:upcoming,ongoing,completed,cancelled'],
            'banner'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        // Keep existing banner path by default
        $bannerPath = $event->banner_url;

        if ($request->hasFile('banner')) {
            $dir = public_path('assets/event_banners');
            if (!is_dir($dir)) { @mkdir($dir, 0755, true); }

            $ext = strtolower($request->file('banner')->getClientOriginalExtension());
            $filename = 'event_' . \Illuminate\Support\Str::random(16) . '.' . $ext;
            $request->file('banner')->move($dir, $filename);

            // Delete old banner if it exists
            if (!empty($bannerPath)) {
                $oldFull = public_path(trim($bannerPath, '/'));
                if (is_file($oldFull)) { @unlink($oldFull); }
            }

            $bannerPath = 'assets/event_banners/' . $filename;
        }

        DB::table('events')
            ->where('event_id', $id)
            ->update([
                'name'        => $validated['name'],
                'venue'       => $validated['venue'],
                'event_date'  => \Illuminate\Support\Carbon::parse($validated['event_date']),
                'description' => $validated['description'],
                'status'      => $validated['status'],
                'banner_url'  => $bannerPath,
                'updated_at'  => now(),
            ]);

        return redirect()
            ->route('admin.events')
            ->with('success_message', 'Event updated successfully!');
    }

    public function destroy($id)
    {
        $event = DB::table('events')->where('event_id', $id)->first();

        if ($event) {
            if (!empty($event->banner_url)) {
                $full = public_path(trim($event->banner_url, '/'));
                if (is_file($full)) { @unlink($full); }
            }
            DB::table('events')->where('event_id', $id)->delete();
        }

        return redirect()
            ->route('admin.events')
            ->with('success_message', 'Event deleted successfully!');
    }
}
