@extends('layouts.app')

@section('title', ($book->title ?? 'Book Details') . ' | Book Heaven')

@section('content')

{{-- ✅ Link to your existing CSS --}}
<link rel="stylesheet" href="{{ asset('css/book_details.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

@if(session('message'))
<script>alert("{{ session('message') }}");</script>
@endif

<main>
@if(!empty($book))
  <div class="book-container">

    {{-- ✅ LEFT SECTION --}}
    <div class="left-section">
      <div class="book-header">
        <img src="{{ asset($book->cover_image_url ?? 'https://images.unsplash.com/photo-1544947950-fa07a98d237f') }}" 
             alt="{{ $book->title }}" class="book-image">

        <div class="book-info">
          <h1 class="book-title">{{ $book->title }}</h1>
          <p class="book-author">By {{ collect($writers)->pluck('name')->join(', ') }}</p>

          {{-- ⭐ Ratings --}}
          @php
            $rating = $book->rating ?? 0;
            $full = floor($rating);
            $half = ($rating - $full) >= 0.5;
          @endphp
          <div class="book-rating">
            @for($i=1;$i<=5;$i++)
              @if($i <= $full)
                <i class="fas fa-star"></i>
              @elseif($i == $full + 1 && $half)
                <i class="fas fa-star-half-alt"></i>
              @else
                <i class="far fa-star"></i>
              @endif
            @endfor
            <span>{{ number_format($rating,1) }} ({{ count($reviews) }} reviews)</span>
          </div>

          <p class="book-price">৳{{ number_format($book->price,2) }}</p>
          <p class="book-description">{{ $book->details ?? 'No description available.' }}</p>

          <div class="book-meta">
            <p><strong>Published:</strong> {{ \Carbon\Carbon::parse($book->published)->format('F j, Y') }}</p>
            <p><strong>Genre:</strong> {{ collect($genres)->pluck('name')->join(', ') }}</p>
            <p><strong>Category:</strong> {{ collect($categories)->pluck('name')->join(', ') }}</p>
            <p><strong>Language:</strong> {{ collect($languages)->pluck('name')->join(', ') }}</p>
          </div>

          {{-- 🛒 Buttons --}}
          <div class="button-group">
            <form method="POST" action="{{ route('book.addToCart') }}">
              @csrf
              <input type="hidden" name="book_id" value="{{ $book->book_id }}">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-cart-plus"></i> Add to Cart
              </button>
            </form>
            <form method="POST" action="{{ route('book.addToWishlist') }}">
              @csrf
              <input type="hidden" name="book_id" value="{{ $book->book_id }}">
              <button type="submit" class="btn btn-secondary">
                <i class="fas fa-heart"></i> Add to Wishlist
              </button>
            </form>
          </div>
        </div>
      </div>

      {{-- 👤 Author Section --}}
      @if(!empty($writers))
      <div class="author-section">
        @php $w = $writers[0]; @endphp
        <img src="{{ asset($w->image_url ?? 'https://images.unsplash.com/photo-1580489944761-15a19d654956') }}" class="author-image">
        <div class="author-info">
          <h2 class="author-name">{{ $w->name }}</h2>
          <p class="author-bio">{{ $w->bio ?? 'No biography available.' }}</p>
        </div>
      </div>
      @endif

      {{-- 📚 Related Books --}}
      @if(!empty($related_books))
      <div class="related-books">
        <h2 class="section-title">You May Also Like</h2>
        <div class="books-grid">
          @foreach($related_books as $r)
          <div class="book-card">
            <a href="{{ url('/book_details/'.$r->book_id) }}">
              <img src="{{ asset($r->cover_image_url ?? 'https://images.unsplash.com/photo-1589998059171-988d887df646') }}" class="book-cover">
            </a>
            <div class="book-info-small">
              <h3 class="book-title-small">
                <a href="{{ url('/book_details/'.$r->book_id) }}">{{ $r->title }}</a>
              </h3>
              <p class="book-author-small">
                {{ DB::table('writers')
                    ->join('book_writers','writers.writer_id','=','book_writers.writer_id')
                    ->where('book_writers.book_id',$r->book_id)
                    ->value('writers.name') ?? 'Unknown' }}
              </p>
              <p class="book-price-small">৳{{ number_format($r->price,2) }}</p>
            </div>
          </div>
          @endforeach
        </div>
      </div>
      @endif
    </div>

    {{-- ✅ RIGHT SECTION --}}
    <div class="right-section">
      {{-- REVIEWS --}}
      <div class="review-section">
        <h2 class="section-title">Customer Reviews</h2>
        @forelse($reviews as $review)
        <div class="review-item">
          <div class="review-header">
            <img src="{{ asset($review->user_profile ?? 'https://randomuser.me/api/portraits/women/43.jpg') }}" class="reviewer-image">
            <span class="reviewer-name">{{ $review->username }}</span>
            <span class="review-date">{{ \Carbon\Carbon::parse($review->created_at)->format('F j, Y') }}</span>
          </div>
          <div class="review-rating">
            @for($i=1;$i<=5;$i++)
              @if($i <= $review->rating)<i class="fas fa-star"></i>@else<i class="far fa-star"></i>@endif
            @endfor
          </div>
          <p class="review-text">{{ $review->review_text }}</p>
        </div>
        @empty
        <p>No reviews yet. Be the first to review!</p>
        @endforelse

        {{-- Review Form --}}
        <button id="reviewToggle" class="toggle-button"><i class="fas fa-pen"></i> Write a Review</button>
        <div id="reviewForm" class="toggle-form">
          <form method="POST" action="{{ route('book.submitReview') }}">
            @csrf
            <input type="hidden" name="book_id" value="{{ $book->book_id }}">
            <div class="rating-input">
              <label>Rating:</label>
              <div class="rating-stars">
                @for($i=5;$i>=1;$i--)
                <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}">
                <label for="star{{ $i }}">★</label>
                @endfor
              </div>
            </div>
            <textarea name="review_text" placeholder="Share your thoughts..." required></textarea>
            <button type="submit" class="submit-btn">Submit Review</button>
          </form>
        </div>
      </div>

      {{-- QUESTIONS --}}
      <div class="qa-section">
        <h2 class="section-title">Questions & Answers</h2>
        @forelse($questions as $q)
        <div class="qa-item">
          <div class="qa-header">
            <img src="{{ asset($q->questioner_image ?? 'https://randomuser.me/api/portraits/women/68.jpg') }}" class="questioner-image">
            <span class="questioner-name">{{ $q->questioner_name }}</span>
          </div>
          <p class="question-text">{{ $q->question_text }}</p>
          @if(!empty($q->answer_text))
          <div class="answer-container">
            <span class="answer-label">ANSWER</span>
            <div class="qa-header">
              <img src="{{ asset($q->answerer_image ?? 'https://randomuser.me/api/portraits/men/75.jpg') }}" class="questioner-image">
              <span class="questioner-name">{{ $q->answerer_name }}</span>
            </div>
            <p class="answer-text">{{ $q->answer_text }}</p>
          </div>
          @endif
        </div>
        @empty
        <p>No questions yet. Ask the first question!</p>
        @endforelse

        {{-- Q&A Form --}}
        <button id="qaToggle" class="toggle-button"><i class="fas fa-question"></i> Ask a Question</button>
        <div id="qaForm" class="toggle-form">
          <form method="POST" action="{{ route('book.submitQuestion') }}">
            @csrf
            <input type="hidden" name="book_id" value="{{ $book->book_id }}">
            <textarea name="question_text" placeholder="What would you like to know?" required></textarea>
            <button type="submit" class="submit-btn">Submit Question</button>
          </form>
        </div>
      </div>
    </div>
  </div>
@else
  <div class="no-book-found">
    <h2>Book not found</h2>
    <p>The book you're looking for doesn't exist or has been removed.</p>
    <a href="{{ url('/') }}" class="btn btn-primary">Browse Books</a>
  </div>
@endif
</main>

<script>
  document.getElementById('reviewToggle').onclick = () => {
    const f = document.getElementById('reviewForm');
    f.style.display = f.style.display === 'none' ? 'block' : 'none';
  };
  document.getElementById('qaToggle').onclick = () => {
    const f = document.getElementById('qaForm');
    f.style.display = f.style.display === 'none' ? 'block' : 'none';
  };
</script>
@endsection
