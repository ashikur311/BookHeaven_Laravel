@extends('layouts.app')

@section('title', 'Books by Genre - BookHeaven')

@section('content')
<section class="genre-header py-5 text-center text-white" style="background: linear-gradient(135deg, #57abd2, #9ccde5);">
  <div class="container">
    <h1><i class="fas fa-book-open me-2"></i>Books by Genre</h1>
  </div>
</section>

<div class="container my-5">
  <div class="row">
    {{-- Sidebar --}}
    <div class="col-lg-3 mb-4">
      <div class="p-3 rounded shadow-sm" style="background:#f8f9fa;">
        <h4 class="mb-3"><i class="fas fa-list me-2"></i>Genres</h4>
        <ul class="list-unstyled genre-list">
          @foreach ($genres as $g)
            @php
              $count = DB::table('book_genres')->where('genre_id', $g->genre_id)->count();
            @endphp
            <li class="mb-2">
              <a href="{{ route('genre_books.show', $g->genre_id) }}"
                 class="d-flex justify-content-between align-items-center text-decoration-none 
                        px-3 py-2 rounded {{ isset($genre) && $genre->genre_id == $g->genre_id ? 'bg-primary text-white' : 'bg-light text-dark' }}">
                <span>{{ $g->name }}</span>
                <span class="badge {{ isset($genre) && $genre->genre_id == $g->genre_id ? 'bg-light text-primary' : 'bg-secondary' }}">
                  {{ $count }}
                </span>
              </a>
            </li>
          @endforeach
        </ul>
      </div>
    </div>

    {{-- Content --}}
    <div class="col-lg-9">
      <div class="p-4 rounded shadow-sm bg-white">
        <h2 class="mb-4 text-primary">
          <i class="fas fa-tag me-2"></i>{{ $genre->name ?? 'Select a Genre' }}
        </h2>

        @if ($books->count() > 0)
          <div class="row row-cols-1 row-cols-md-3 g-4">
            @foreach ($books as $book)
              @php
                $rating = round($book->avg_rating ?? 0, 1);
                $inCart = in_array($book->book_id, $cartBookIds);
              @endphp
              <div class="col">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="book-card-img-container">
                    <a href="{{ url('/book/'.$book->book_id) }}">
                      <img src="{{ $book->cover_image_url ? asset($book->cover_image_url) : asset('assets/images/default-book.jpg') }}" 
                           class="card-img-top" alt="{{ $book->title }}">
                    </a>
                  </div>
                  <div class="card-body d-flex flex-column">
                    <h5 class="book-title">
                      <a href="{{ url('/book/'.$book->book_id) }}" class="text-decoration-none text-dark">
                        {{ $book->title }}
                      </a>
                    </h5>
                    <p class="book-writer mb-1">
                      <i class="fas fa-user-edit me-1"></i>{{ $book->writers ?? 'Unknown Writer' }}
                    </p>

                    <div class="book-rating mb-2">
                      <span class="text-warning">
                        @for ($i = 1; $i <= 5; $i++)
                          @if ($i <= floor($rating))
                            <i class="fas fa-star"></i>
                          @elseif ($i - $rating < 1)
                            <i class="fas fa-star-half-alt"></i>
                          @else
                            <i class="far fa-star"></i>
                          @endif
                        @endfor
                      </span>
                      <span class="ms-2 fw-semibold">{{ $rating > 0 ? $rating : 'No ratings' }}</span>
                    </div>

                    <p class="book-price fw-bold text-primary mb-3">৳{{ number_format($book->price, 2) }}</p>

                    <button class="btn btn-sm btn-primary mt-auto btn-add-to-cart {{ $inCart ? 'disabled' : '' }}"
                            data-book-id="{{ $book->book_id }}">
                      <i class="fas {{ $inCart ? 'fa-check' : 'fa-cart-plus' }} me-1"></i>
                      {{ $inCart ? 'Added to Cart' : 'Add to Cart' }}
                    </button>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="text-center py-5 text-muted">
            <i class="fas fa-book-open fa-3x mb-3"></i>
            <h4>No books found in this genre</h4>
            <p>Try exploring another genre.</p>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function() {
  $('.btn-add-to-cart').on('click', function() {
    const btn = $(this);
    const bookId = btn.data('book-id');

    if (btn.hasClass('disabled')) {
      alert('This book is already in your cart.');
      return;
    }

    $.ajax({
      url: "{{ route('genre_books.addToCart') }}",
      type: 'POST',
      data: {
        _token: '{{ csrf_token() }}',
        book_id: bookId
      },
      success: function(res) {
        if (res.success) {
          btn.addClass('disabled')
             .html('<i class="fas fa-check me-1"></i>Added to Cart');
          $('.cart-count').text(res.cart_count);
          alert(res.message);
        } else {
          alert(res.message);
        }
      },
      error: function() {
        alert('Error adding book to cart.');
      }
    });
  });
});
</script>
@endsection
