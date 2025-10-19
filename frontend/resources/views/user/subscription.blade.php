@extends('layouts.app')
@section('title', 'My Subscriptions | Book Heaven')

@section('content')

{{-- ✅ Include identical CSS --}}
<link rel="stylesheet" href="{{ asset('css/subscriptions.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<main>
    {{-- Sidebar --}}
    @include('profile.sidebar', ['user' => $user, 'activeTab' => 'subscription'])

    <div class="subscription_content">
        {{-- Stats Grid --}}
        <div class="stats-grid">
            <div class="stat-card"><h3>Total Subscriptions</h3><p>{{ $stats['total'] }}</p></div>
            <div class="stat-card"><h3>Active</h3><p>{{ $stats['active'] }}</p></div>
            <div class="stat-card"><h3>Expired</h3><p>{{ $stats['expired'] }}</p></div>
            <div class="stat-card"><h3>Renew Needed</h3><p>{{ $stats['renew_needed'] }}</p></div>
        </div>

        {{-- Tabs --}}
        <div class="tabs">
            <a href="#books" class="tab-btn active">Books</a>
            <a href="#audiobooks" class="tab-btn">Audiobooks</a>
        </div>

        {{-- BOOKS TAB --}}
        <div id="books" class="tab-content active">
            @if($activeSubs->isEmpty())
                <div class="subscription-plan-card"><p>You don't have any active subscriptions with books.</p></div>
            @else
                @foreach($activeSubs as $subscription)
                    <div class="subscription-plan-card">
                        <div class="plan-header">
                            <div class="plan-title">{{ $subscription->plan_name }} Plan</div>
                            <div class="plan-dates">
                                {{ date('M d, Y', strtotime($subscription->start_date)) }} -
                                {{ date('M d, Y', strtotime($subscription->end_date)) }}
                            </div>
                        </div>

                        <div class="plan-stats">
                            <div class="plan-stat"><strong>Books Allowed:</strong> {{ $subscription->available_rent_book }}</div>
                            <div class="plan-stat"><strong>Books Used:</strong> {{ $subscription->used_rent_book ?? 0 }}</div>
                        </div>

                        @php $books = $booksBySub[$subscription->user_subscription_id] ?? collect(); @endphp
                        @if($books->isNotEmpty())
                            <div class="subscription-table">
                                <table>
                                    <thead>
                                        <tr><th>Title</th><th>Author</th><th>Genre</th><th>Language</th></tr>
                                    </thead>
                                    <tbody>
                                        @foreach($books as $book)
                                            <tr>
                                                <td>{{ $book->title }}</td>
                                                <td>{{ $book->writer }}</td>
                                                <td>{{ $book->genre }}</td>
                                                <td>{{ $book->language }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p>No books added to this subscription yet.</p>
                        @endif

                        <div class="action-buttons">
                            <a href="{{ url('book_add_to_subscription?plan_type=' . strtolower($subscription->plan_name) . '&sub_id=' . $subscription->user_subscription_id) }}" class="btn-add">
                                <i class="fas fa-plus"></i> Add Book
                            </a>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- AUDIOBOOKS TAB --}}
        <div id="audiobooks" class="tab-content">
            @if($activeSubs->isEmpty())
                <div class="subscription-plan-card"><p>You don't have any active subscriptions with audiobooks.</p></div>
            @else
                @foreach($activeSubs as $subscription)
                    <div class="subscription-plan-card">
                        <div class="plan-header">
                            <div class="plan-title">{{ $subscription->plan_name }} Plan</div>
                            <div class="plan-dates">
                                {{ date('M d, Y', strtotime($subscription->start_date)) }} -
                                {{ date('M d, Y', strtotime($subscription->end_date)) }}
                            </div>
                        </div>

                        <div class="plan-stats">
                            <div class="plan-stat"><strong>Audiobooks Allowed:</strong> {{ $subscription->available_audio }}</div>
                            <div class="plan-stat"><strong>Audiobooks Used:</strong> {{ $subscription->used_audio_book ?? 0 }}</div>
                        </div>

                        @php $audios = $audiobooksBySub[$subscription->user_subscription_id] ?? collect(); @endphp
                        @if($audios->isNotEmpty())
                            <div class="subscription-table">
                                <table>
                                    <thead>
                                        <tr><th>Title</th><th>Author</th><th>Genre</th><th>Language</th></tr>
                                    </thead>
                                    <tbody>
                                        @foreach($audios as $audio)
                                            <tr>
                                                <td>{{ $audio->title }}</td>
                                                <td>{{ $audio->writer }}</td>
                                                <td>{{ $audio->genre }}</td>
                                                <td>{{ $audio->language }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p>No audiobooks added to this subscription yet.</p>
                        @endif

                        <div class="action-buttons">
                            <a href="{{ url('audio_book_add_to_subscription?plan_type=' . strtolower($subscription->plan_name) . '&sub_id=' . $subscription->user_subscription_id) }}" class="btn-add">
                                <i class="fas fa-headphones"></i> Add Audiobook
                            </a>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</main>

{{-- Tab switching JS --}}
<script>
document.querySelectorAll(".tab-btn").forEach(tab => {
  tab.addEventListener("click", function(e){
    e.preventDefault();
    document.querySelectorAll(".tab-btn").forEach(t=>t.classList.remove("active"));
    document.querySelectorAll(".tab-content").forEach(c=>c.classList.remove("active"));
    this.classList.add("active");
    document.querySelector(this.getAttribute("href")).classList.add("active");
  });
});
</script>

{{-- Include your CSS --}}
<link rel="stylesheet" href="{{ asset('css/user_subscription.css') }}">
@endsection
