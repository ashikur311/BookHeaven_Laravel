<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\Process\Process;

class EventController extends Controller
{
    /** ----------------------------------------------------------------
     *  Show all events (joined + upcoming)
     *  ----------------------------------------------------------------
     */
    public function index()
    {
        $userId = Auth::id();

        // Joined Events
        $joinedEvents = DB::table('events')
            ->join('event_participants', 'events.event_id', '=', 'event_participants.event_id')
            ->where('event_participants.user_id', $userId)
            ->where('event_participants.status', 'registered')
            ->select('events.*')
            ->get();

        // Upcoming Events
        $upcomingEvents = DB::table('events')
            ->leftJoin('event_participants', function ($join) use ($userId) {
                $join->on('events.event_id', '=', 'event_participants.event_id')
                     ->where('event_participants.user_id', '=', $userId);
            })
            ->where('events.event_date', '>', now())
            ->where('events.status', 'upcoming')
            ->select('events.*', DB::raw('event_participants.event_id AS is_joined'))
            ->orderBy('events.event_date')
            ->get();

        return view('events.index', compact('joinedEvents', 'upcomingEvents'));
    }

    /** ----------------------------------------------------------------
     *  Join Event
     *  ----------------------------------------------------------------
     */
    public function join(Request $request)
    {
        $request->validate(['event_id' => 'required|integer']);
        $userId = Auth::id();

        $exists = DB::table('event_participants')
            ->where('user_id', $userId)
            ->where('event_id', $request->event_id)
            ->exists();

        if (!$exists) {
            DB::table('event_participants')->insert([
                'user_id' => $userId,
                'event_id' => $request->event_id,
                'status' => 'registered',
                'created_at' => now(),
            ]);

            return redirect()->route('events.index')->with('success', 'Successfully joined the event!');
        }

        return redirect()->route('events.index')->with('error', 'You are already registered for this event.');
    }

    /** ----------------------------------------------------------------
     *  Leave Event
     *  ----------------------------------------------------------------
     */
    public function leave(Request $request)
    {
        $request->validate(['event_id' => 'required|integer']);
        $userId = Auth::id();

        DB::table('event_participants')
            ->where('user_id', $userId)
            ->where('event_id', $request->event_id)
            ->delete();

        return redirect()->route('events.index')->with('success', 'You have left the event.');
    }

    /** ----------------------------------------------------------------
     *  Generate & Send Event Ticket (with QR code)
     *  ----------------------------------------------------------------
     */
    public function ticket(Request $request)
    {
        $request->validate(['event_id' => 'required|integer']);
        $user = Auth::user();
        $event = DB::table('events')->where('event_id', $request->event_id)->first();

        if (!$event) {
            return back()->with('error', 'Event not found.');
        }

        // Make sure storage/tickets directory exists
        $pdfPath = storage_path('app/public/tickets/');
        if (!file_exists($pdfPath)) {
            mkdir($pdfPath, 0777, true);
        }

        // Unique filename
        $fileName = 'ticket_' . $user->id . '_' . $event->event_id . '_' . time() . '.pdf';
        $fullPath = $pdfPath . $fileName;

        // Generate the PDF
        $pdf = Pdf::loadView('pdf.ticket', compact('user', 'event'));
        $pdf->save($fullPath);

        // OPTIONAL: Run Python script to email ticket
        $pythonScript = base_path('sendticket.py');
        if (file_exists($pythonScript)) {
            $process = new Process([
                'python',
                $pythonScript,
                $user->email,
                $fullPath
            ]);
            $process->run();

            if (!$process->isSuccessful()) {
                \Log::error("Ticket sending failed: " . $process->getErrorOutput());
            }
        }

        return response()->download($fullPath, 'Event_Ticket_' . $event->name . '.pdf');
    }
}
