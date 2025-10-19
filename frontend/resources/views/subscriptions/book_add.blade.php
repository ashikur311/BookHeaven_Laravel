@extends('layouts.app')
@section('title', 'Add Books to Subscription | Book Heaven')

@section('content')
<link rel="stylesheet" href="{{ asset('css/addbooktosubscription.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<main>
    @if(session('message'))
        <div class="alert success">{{ session('message') }}</div>
    @endif
    @if(session('error'))
        <div class="alert error">{{ session('error') }}</div>
    @endif

    @if($subscription)
    <section>
        {{-- ✅ Plan Overview --}}
        <div class="plan-overview">
            <div class="plan-header">
                <h1 class="plan-title">{{ $subscription->plan_name }} Subscription</h1>
                <div class="plan-status">Active</div>
            </div>

            <div class="plan-details-container">
                <div class="plan-features">
                    <div class="feature-item"><div class="feature-text">
                        <div class="feature-label">Books per month</div>
                        <div class="feature-value">{{ $subscription->book_quantity }}</div>
                    </div></div>

                    <div class="feature-item"><div class="feature-text">
                        <div class="feature-label">Books Used</div>
                        <div class="feature-value">{{ $booksUsed }}</div>
                    </div></div>

                    <div class="feature-item"><div class="feature-text">
                        <div class="feature-label">Access to</div>
                        <div class="feature-value">All Genres</div>
                    </div></div>

                    <div class="feature-item"><div class="feature-text">
                        <div class="feature-label">Premium Support</div>
                        <div class="feature-value">24/7</div>
                    </div></div>
                </div>

                <div class="plan-progress">
                    <div class="progress-item">
                        <div class="progress-header">
                            <div class="progress-title">Books Remaining</div>
                            <div class="progress-value">{{ $booksRemaining }}/{{ $subscription->book_quantity }}</div>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $booksProgress }}%"></div>
                        </div>
                        <div class="progress-text">
                            <span>Used: {{ $booksUsed }}</span>
                            <span>Reset: {{ \Carbon\Carbon::parse($subscription->end_date)->format('F j') }}</span>
                        </div>
                    </div>
                    <div class="days-left">
                        <div class="days-left-value">{{ $daysLeft }}</div>
                        <div class="days-left-label">days left</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ✅ Header --}}
        <section class="add-books-header">
            <div class="header-container">
                <h2>Add Rent Books</h2>
                <a href="{{ url('audio_book_add_to_subscription?sub_id=' . $subscription->user_subscription_id . '&plan_type=' . strtolower($planType)) }}" class="add-audiobook-btn">
                    <i class="fas fa-headphones"></i> Add Audio Books
                </a>
            </div>
        </section>

        {{-- ✅ Genres + Book Grid --}}
        <div class="catalog-container">
            <aside class="genre-sidebar">
                <h3 class="genre-title">Browse Genres</h3>
                <div class="genre-list">
                    <a href="{{ request()->fullUrlWithQuery(['genre'=>'all']) }}" class="genre-item {{ $selectedGenre==='all'?'active':'' }}">All Genres</a>
                    @foreach($genres as $g)
                        <a href="{{ request()->fullUrlWithQuery(['genre'=>$g]) }}" class="genre-item {{ $selectedGenre===$g?'active':'' }}">{{ $g }}</a>
                    @endforeach
                </div>
            </aside>

            <div class="book-grid">
                @forelse($rentBooks as $book)
                    <div class="book-card">
                        <img src="{{ asset($book->poster_url) }}" alt="{{ $book->title }}" class="book-cover">
                        <div class="book-info">
                            <h3 class="book-title">{{ $book->title }}</h3>
                            <p class="book-author">{{ $book->writer }}</p>
                            <p class="book-genre">{{ $book->genre }}</p>
                            <form method="POST" action="{{ url('/book_add_to_subscription?sub_id=' . $subscription->user_subscription_id . '&plan_type=' . strtolower($planType)) }}">
                                @csrf
                                <input type="hidden" name="rent_book_id" value="{{ $book->rent_book_id }}">
                                <button type="submit" name="add_book" class="add-button"
                                    {{ $booksRemaining <= 0 ? 'disabled title=You_have_reached_your_limit' : '' }}>
                                    Add to My Subscription
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="no-books">No rent books found in this category.</div>
                @endforelse
            </div>
        </div>
    </section>
    @else
    <div class="no-subscription">
        <h2>Subscription Not Found or Expired</h2>
        <p>The subscription you're trying to access is either expired or doesn't exist.</p>
        <a href="{{ url('/subscriptions') }}" class="subscribe-btn">View Subscription Plans</a>
    </div>
    @endif
</main>
@endsection
