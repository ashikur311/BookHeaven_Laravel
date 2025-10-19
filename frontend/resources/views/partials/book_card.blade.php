<div class="book-card">
  <a href="{{ url('/book_details/' . $book->book_id) }}">
    <img src="{{ $book->cover_image_url ?? 'https://images.unsplash.com/photo-1589998059171-988d887df646?auto=format&fit=crop&w=800&q=80' }}" alt="{{ $book->title }}" class="book-cover">
  </a>
  <div class="book-info">
    <h3 class="book-title">{{ $book->title }}</h3>
    <p class="book-author">{{ $book->writers }}</p>
    <div class="book-rating">
      @php
        $rating = $book->rating ?? 0;
        $full = floor($rating);
        $half = ($rating - $full) >= 0.5 ? 1 : 0;
      @endphp
      @for ($i = 0; $i < $full; $i++) <i class="fas fa-star"></i> @endfor
      @if ($half) <i class="fas fa-star-half-alt"></i> @endif
      @for ($i = 0; $i < 5 - $full - $half; $i++) <i class="far fa-star"></i> @endfor
      <span>{{ number_format($rating, 1) }}</span>
    </div>
    <p class="book-price">৳{{ $book->price }}</p>
    <form method="post" action="{{ url('/cart/add') }}">
      @csrf
      <input type="hidden" name="book_id" value="{{ $book->book_id }}">
      <button type="submit" class="add-to-cart"><i class="fas fa-cart-plus"></i> Add to Cart</button>
    </form>
  </div>
</div>
