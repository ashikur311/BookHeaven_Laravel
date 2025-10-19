@extends('layouts.app')

@section('title', 'Add Audio Books to Subscription')

@section('content')
<main style="padding:1.5rem 5%;max-width:1400px;margin:0 auto;">
    @if(session('message'))
        <div class="alert success">{{ session('message') }}</div>
    @endif
    @if(session('error'))
        <div class="alert error">{{ session('error') }}</div>
    @endif

    @if($subscription)
    <div class="plan-overview">
        <div class="plan-header">
            <h1 class="plan-title">{{ $subscription->plan_name }} Subscription</h1>
            <div class="plan-status">Active</div>
        </div>
        <div class="plan-details-container">
            <div class="plan-features">
                <div class="feature-item"><div class="feature-text">
                    <div class="feature-label">Audio Books per month</div>
                    <div class="feature-value">{{ $subscription->audiobook_quantity }}</div>
                </div></div>
                <div class="feature-item"><div class="feature-text">
                    <div class="feature-label">Audio Books Used</div>
                    <div class="feature-value">{{ $subscription->used_audio }}</div>
                </div></div>
                <div class="feature-item"><div class="feature-text">
                    <div class="feature-label">Remaining</div>
                    <div class="feature-value">{{ $subscription->remaining_audio }}</div>
                </div></div>
                <div class="feature-item"><div class="feature-text">
                    <div class="feature-label">Days Left</div>
                    <div class="feature-value">{{ $subscription->days_left }}</div>
                </div></div>
            </div>
        </div>
    </div>

    <div class="add-books-header">
        <div class="header-container">
            <h2>Add Audio Books</h2>
            <a href="{{ url('/book_add_to_subscription?sub_id='.$subId.'&plan_type='.$planType) }}" class="add-book-btn">
                <i class="fas fa-book"></i> Add Regular Books
            </a>
        </div>
    </div>

    <div class="catalog-container" style="display:flex;gap:1.5rem;">
        <aside class="genre-sidebar">
            <h3 class="genre-title">Browse Genres</h3>
            <div class="genre-list">
                <a href="{{ request()->fullUrlWithQuery(['genre'=>'all']) }}" class="genre-item {{ $selectedGenre=='all'?'active':'' }}">All Genres</a>
                @foreach($genres as $genre)
                    <a href="{{ request()->fullUrlWithQuery(['genre'=>$genre]) }}" class="genre-item {{ $selectedGenre==$genre?'active':'' }}">
                        {{ $genre }}
                    </a>
                @endforeach
            </div>
        </aside>

        <div class="book-grid">
            @forelse($audiobooks as $book)
            <div class="book-card">
                <img src="{{ $book->poster_url ?? 'https://via.placeholder.com/250x200/57abd2/ffffff?text=Audio+Book' }}" 
                     alt="{{ $book->title }}" class="book-cover">
                <div class="book-info">
                    <h3 class="book-title">{{ $book->title }}</h3>
                    <p class="book-author">{{ $book->writer }}</p>
                    <div class="book-rating"><span class="star-icon">★</span>4.5</div>

                    <form method="POST" action="{{ route('audio.add.to.subscription.store') }}">
                        @csrf
                        <input type="hidden" name="sub_id" value="{{ $subId }}">
                        <input type="hidden" name="audiobook_id" value="{{ $book->audiobook_id }}">
                        <button type="submit" class="add-button"
                            @disabled($book->already_added || $subscription->remaining_audio <= 0)>
                            {{ $book->already_added ? 'Already Added' : 'Add to My Subscription' }}
                        </button>
                    </form>
                </div>
            </div>
            @empty
                <div class="no-books">No audiobooks found in this category.</div>
            @endforelse
        </div>
    </div>
    @else
        <div class="no-subscription">
            <h2>Subscription Not Found or Expired</h2>
            <p>The subscription you're trying to access is either expired or doesn't exist.</p>
            <a href="{{ route('subscriptions.index') }}" class="subscribe-btn">View Subscription Plans</a>
        </div>
    @endif
</main>
<link rel="stylesheet" href="{{ asset('css/addbooktosubscription.css') }}">
@endsection
