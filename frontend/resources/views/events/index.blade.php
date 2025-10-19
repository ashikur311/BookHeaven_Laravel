@extends('layouts.app')
@section('title', 'Events')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

@if (session('success'))
    <div class="alert success">{{ session('success') }}</div>
@elseif (session('error'))
    <div class="alert error">{{ session('error') }}</div>
@endif

<main>
    <h1 class="section-title">My Events</h1>
    <div class="events-container">
        @forelse ($joinedEvents as $event)
            <div class="event-card">
                <div class="countdown">Joined</div>
                <img src="{{ asset($event->banner_url ?? 'assets/default_event.jpg') }}" class="event-poster">
                <div class="event-content">
                    <div class="event-info-card">
                        <p><strong>Event:</strong> {{ $event->name }}</p>
                        <p><strong>Venue:</strong> {{ $event->venue }}</p>
                        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y h:i A') }}</p>
                        <p><strong>Description:</strong> {{ $event->description ?? 'No description available' }}</p>
                    </div>
                    <div class="event-actions">
                        <form method="POST" action="{{ route('events.leave') }}">
                            @csrf
                            <input type="hidden" name="event_id" value="{{ $event->event_id }}">
                            <button class="join-btn leave-btn" type="submit">Leave Event</button>
                        </form>
                        <form method="POST" action="{{ route('events.ticket') }}">
                            @csrf
                            <input type="hidden" name="event_id" value="{{ $event->event_id }}">
                            <button class="download-btn" type="submit"><i class="fas fa-download"></i> Get Ticket</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="no-events">You haven't joined any events yet</div>
        @endforelse
    </div>

    <h1 class="section-title">Upcoming Events</h1>
    <div class="events-container">
        @forelse ($upcomingEvents as $event)
            @if (!$event->is_joined)
                <div class="event-card">
                    <div class="countdown">
                        {{ now()->diffInDays($event->event_date) }} days left
                    </div>
                    <img src="{{ asset($event->banner_url ?? 'assets/default_event.jpg') }}" class="event-poster">
                    <div class="event-content">
                        <div class="event-info-card">
                            <p><strong>Event:</strong> {{ $event->name }}</p>
                            <p><strong>Venue:</strong> {{ $event->venue }}</p>
                            <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y h:i A') }}</p>
                            <p><strong>Description:</strong> {{ $event->description ?? 'No description available' }}</p>
                        </div>
                        <form method="POST" action="{{ route('events.join') }}">
                            @csrf
                            <input type="hidden" name="event_id" value="{{ $event->event_id }}">
                            <button class="join-btn" type="submit">Join Event</button>
                        </form>
                    </div>
                </div>
            @endif
        @empty
            <div class="no-events">No upcoming events at the moment</div>
        @endforelse
    </div>
</main>

<link rel="stylesheet" href="{{ asset('css/events.css') }}">
@endsection
