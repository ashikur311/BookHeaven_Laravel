@extends('layouts.app')

@section('title', 'Books by Writers | Book Heaven')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/writer_books.css') }}">

<section class="writer-header">
  <div class="container text-center">
    <h1><i class="fas fa-feather-alt me-2"></i>Books by Writers</h1>
  </div>
</section>

<div class="container mb-5">
  <div class="writer-container row g-0">
    {{-- Sidebar --}}
    <div class="col-lg-3 writer-sidebar">
      <h3><i class="fas fa-users me-2"></i>Writers</h3>
      <div class="writer-list">
        @foreach($writers as $w)
        <a href="{{ route('writer.books', ['writer_id' => $w->writer_id]) }}" class="text-decoration-none">
          <div class="writer-item {{ ($writer && $writer->writer_id == $w->writer_id) ? 'active' : '' }}">
            <span>
              {{ $w->name }}
              <span class="badge">
                {{ DB::table('book_writers')->where('writer_id', $w->writer_id)->count() }}
              </span>
            </span>
          </div>
        </a>
        @endforeach
      </div>
    </div>

    {{-- Main Content --}}
    <div class="col-lg-9 writer-content">
      @if($writer)
      <div class="writer-info d-flex align-items-center gap-3 mb-4">
        <img src="{{ asset($writer->image_url ?? 'https://via.placeholder.com/100') }}" class="writer-image" alt="{{ $writer->name }}">
        <div>
          <h3 class="writer-name">{{ $writer->name }}</h3>
          @if($writer->bio)
          <p class="writer-bio">{{ $writer->bio }}</p>
          @endif
        </div>
      </div>
      @endif

      <h2 class="writer-title">
        <i class="fas fa-book me-2"></i>{{ $writer->name ?? 'Select a Writer' }}'s Books
      </h2>

      @if(count($books) > 0)
      <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
        @foreach($books as $book)
        @php
          $rating = $book->avg_rating ? round($book->avg_rating, 1) : 0;
          $inCart = in_array($book->book_id, $in_cart);
        @endphp
        <div class="col">
          <div class="book-card card h-100">
            <div class="book-card-img-container">
              <a href="{{ url('/book_details/'.$book->book_id) }}">
                <img src="{{ asset($book->cover_image_url ?? 'images/default_book.jpg') }}" class="book-card-img" alt="{{ $book->title }}">
              </a>
            </div>
            <div class="book-card-body">
              <h5 class="book-title">
                <a href="{{ url('/book_details/'.$book->book_id) }}">{{ $book->title }}</a>
              </h5>

              <div class="book-rating">
                <div class="rating-stars">
                  {!! str_repeat('<i class="fas fa-star"></i>', floor($rating)) !!}
                  {!! ($rating - floor($rating) >= 0.5) ? '<i class="fas fa-star-half-alt"></i>' : '' !!}
                  {!! str_repeat('<i class="far fa-star"></i>', 5 - ceil($rating)) !!}
                </div>
                <span class="rating-value">{{ $rating > 0 ? $rating : 'No ratings' }}</span>
              </div>

              <p class="book-price">৳{{ number_format($book->price, 2) }}</p>

              <button class="btn btn-add-to-cart btn-success {{ $inCart ? 'disabled' : '' }}" data-book-id="{{ $book->book_id }}">
                <i class="fas {{ $inCart ? 'fa-check' : 'fa-cart-plus' }} me-2"></i>
                {{ $inCart ? 'Added to Cart' : 'Add to Cart' }}
              </button>
            </div>
          </div>
        </div>
        @endforeach
      </div>
      @else
      <div class="no-books text-center py-5">
        <i class="fas fa-book-open fa-3x mb-3 text-muted"></i>
        <h4>No books found by this writer</h4>
        <p class="text-muted">Try another writer</p>
      </div>
      @endif
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('.btn-add-to-cart').click(function() {
  const btn = $(this);
  const id = btn.data('book-id');
  if (btn.hasClass('disabled')) return alert('Already in cart');
  $.post('{{ route("writer.addToCart") }}', {
    _token: '{{ csrf_token() }}', book_id: id
  }, function(res) {
    if (res.success) {
      btn.addClass('disabled').html('<i class="fas fa-check me-2"></i>Added to Cart');
      $('.cart-count').text(res.cart_count);
      alert('Book added to your cart');
    } else alert(res.message);
  }).fail(() => alert('Error adding book'));
});
</script>
@endsection
