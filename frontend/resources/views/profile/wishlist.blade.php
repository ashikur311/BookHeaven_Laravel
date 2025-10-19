@extends('layouts.app')

@section('title', 'My Wishlist | Book Heaven')

@section('content')
<main>
    @include('profile.sidebar')

    <div class="wishlist_content">
        <div class="wishlist-header">
            <h2>My Wishlist (<span id="wishlist-count">{{ $wishlist_count }}</span> items)</h2>
        </div>

        <div class="books-grid">
            @if($wishlist_count > 0)
                @foreach($wishlist as $book)
                    <div class="book-card" id="book-{{ $book->book_id }}">
                        <img src="{{ asset($book->cover_image_url) }}" alt="{{ $book->title }}" class="book-image">
                        <div class="book-details">
                            <h3 class="book-title">{{ $book->title }}</h3>
                            <p class="book-author">{{ $book->writers }}</p>
                            <div class="book-price">৳{{ number_format($book->price, 2) }}</div>

                            <div class="book-actions">
                                <button class="btn btn-primary add-to-cart-btn" data-book-id="{{ $book->book_id }}">
                                    Add to Cart
                                </button>
                                <button class="btn btn-outline remove-from-wishlist-btn" data-book-id="{{ $book->book_id }}">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="empty-wishlist">
                    <i class="fas fa-heart-broken"></i>
                    <p>Your wishlist is empty</p>
                    <a href="{{ url('/') }}" class="btn btn-primary">Browse Books</a>
                </div>
            @endif
        </div>
    </div>
</main>

{{-- External Styles --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/user_wishlist.css') }}">

{{-- Inline Custom Styling --}}
<style>
.book-card { transition: all 0.3s ease; }
.book-card.removing { transform: scale(0.9); opacity: 0; }
.btn { cursor: pointer; transition: all 0.2s ease; text-align: center; }
.btn:disabled { opacity: 0.7; cursor: not-allowed; }
.added-to-cart { background-color: #28a745 !important; cursor: default; }
.added-to-cart:hover { background-color: #28a745 !important; }
.empty-wishlist { text-align: center; padding: 40px; width: 100%; grid-column: 1 / -1; }
.empty-wishlist i { font-size: 50px; color: #ccc; margin-bottom: 20px; }
.empty-wishlist p { font-size: 18px; color: #666; margin-bottom: 20px; }
</style>

{{-- JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', () => {
    const csrf = '{{ csrf_token() }}';

    // Update wishlist counter
    const updateWishlistCount = () => {
        const count = document.querySelectorAll('.book-card').length;
        document.getElementById('wishlist-count').textContent = count;
        if (count === 0) {
            document.querySelector('.books-grid').innerHTML = `
                <div class="empty-wishlist">
                    <i class="fas fa-heart-broken"></i>
                    <p>Your wishlist is empty</p>
                    <a href="{{ url('/') }}" class="btn btn-primary">Browse Books</a>
                </div>`;
        }
    };

    // Remove from wishlist
    document.querySelectorAll('.remove-from-wishlist-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.bookId;
            const card = document.getElementById('book-' + id);
            btn.disabled = true;
            card.classList.add('removing');

            const res = await fetch('{{ route('wishlist.remove') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ book_id: id })
            });
            const data = await res.json();

            if (data.success) {
                setTimeout(() => {
                    card.remove();
                    updateWishlistCount();
                    alert(data.message);
                }, 300);
            } else {
                btn.disabled = false;
                card.classList.remove('removing');
                alert(data.message || 'Error removing book');
            }
        });
    });

    // Add to cart
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.bookId;
            const card = document.getElementById('book-' + id);
            btn.disabled = true;
            btn.textContent = 'Adding...';

            const res = await fetch('{{ route('wishlist.addToCart') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ book_id: id })
            });
            const data = await res.json();

            if (data.success) {
                setTimeout(() => {
                    card.remove();
                    updateWishlistCount();
                    alert(data.message);
                }, 300);
            } else {
                btn.disabled = false;
                btn.textContent = 'Add to Cart';
                alert(data.message || 'Error adding to cart');
            }
        });
    });
});
</script>
@endsection
