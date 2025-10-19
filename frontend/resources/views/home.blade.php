@extends('layouts.app')

@section('title', 'BookHeaven - Your Literary Paradise')

@section('content')
<div class="header-section">
  <div class="carousel-wrapper">
    <div id="promoCarousel" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <img src="{{ asset('assets/images/1.png') }}" class="d-block w-100" alt="Book Rental Promotion">
        </div>
        <div class="carousel-item">
          <img src="{{ asset('assets/images/2.png') }}" class="d-block w-100" alt="Audiobook Collection">
        </div>
        <div class="carousel-item">
          <img src="{{ asset('assets/images/3.png') }}" class="d-block w-100" alt="Become a Writer">
        </div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#promoCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#promoCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>
    </div>
  </div>
</div>

<div class="content-container">
  <div class="left-content">
    <div class="writer-section">
      <h3 class="section-title">Popular Writers</h3>
      <ul class="writer-list">
        @foreach ($writers as $writer)
          <li class="writer-item">
            <img src="{{ $writer->image_url ?? 'https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?auto=format&fit=crop&w=100&q=80' }}" alt="{{ $writer->name }}" class="writer-img">
            <div class="writer-info">
              <a href="{{ url('/writer_books/' . $writer->writer_id) }}" class="writer-name">{{ $writer->name }}</a>
              <div class="writer-genre">
                {{ $writer->bio ? Str::limit($writer->bio, 30) : 'Not specified' }}
              </div>
            </div>
          </li>
        @endforeach
      </ul>
    </div>

    <div class="genre-section">
      <h3 class="section-title">Browse Genres</h3>
      <ul class="genre-list">
        @foreach ($genres as $genre)
          <li>
            <a href="{{ url('/genre_books/' . $genre->genre_id) }}">
              <i class="fas fa-book"></i> {{ $genre->name }}
            </a>
          </li>
        @endforeach
      </ul>
    </div>
  </div>

  <div class="right-content">
    <div class="promo-section">
      <div class="promo-card">
        <img src="https://images.unsplash.com/photo-1507842217343-583bb7270b66?auto=format&fit=crop&w=800&q=80" alt="Subscription Plans">
        <h3>Subscribe plans</h3>
        <p>Get unlimited access to thousands of books with our subscription plans.</p>
        <a href="{{ url('/subscription') }}">
          <button class="btn-promo"><i class="fas fa-shopping-cart"></i> Subscribe now</button>
        </a>
      </div>

      <div class="promo-card">
        <img src="https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?auto=format&fit=crop&w=800&q=80" alt="Upcoming Events">
        <h3>Upcoming Events</h3>
        <p>Join our book clubs, author meetups, and literary festivals.</p>
        <a href="{{ url('/events') }}">
          <button class="btn-promo"><i class="fas fa-calendar-alt"></i> View Events</button>
        </a>
      </div>
    </div>

    {{-- BOOK SECTIONS --}}
    <div class="books-section">
      <div class="filter-tabs">
        <div class="filter-tab active" data-filter="all">All Books</div>
        <div class="filter-tab" data-filter="popular">Popular</div>
        <div class="filter-tab" data-filter="top-rated">Top Rated</div>
        <div class="filter-tab" data-filter="recent">Recently Added</div>
      </div>

      {{-- All Books --}}
      <div class="filter-content active" id="all-books">
        <div class="book-grid">
          @foreach ($all_books as $book)
            @include('partials.book_card', ['book' => $book])
          @endforeach
        </div>
      </div>

      {{-- Popular --}}
      <div class="filter-content" id="popular-books">
        <div class="book-grid">
          @foreach ($popular_books as $book)
            @include('partials.book_card', ['book' => $book])
          @endforeach
        </div>
      </div>

      {{-- Top Rated --}}
      <div class="filter-content" id="top-rated-books">
        <div class="book-grid">
          @foreach ($top_rated_books as $book)
            @include('partials.book_card', ['book' => $book])
          @endforeach
        </div>
      </div>

      {{-- Recent --}}
      <div class="filter-content" id="recent-books">
        <div class="book-grid">
          @foreach ($recent_books as $book)
            @include('partials.book_card', ['book' => $book])
          @endforeach
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
// ================== FILTER TABS ==================
document.addEventListener('DOMContentLoaded', function () {
  const tabs = document.querySelectorAll('.filter-tab');

  tabs.forEach(tab => {
    tab.addEventListener('click', function () {
      tabs.forEach(t => t.classList.remove('active'));
      this.classList.add('active');

      document.querySelectorAll('.filter-content').forEach(content => {
        content.classList.remove('active');
      });

      const filterType = this.getAttribute('data-filter');
      document.getElementById(`${filterType}-books`).classList.add('active');
    });
  });

  // Auto-hide message modal after 5 seconds
  const messageModal = document.getElementById('messageModal');
  if (messageModal) {
    setTimeout(() => {
      closeMessageModal();
    }, 5000);
  }
});

function closeMessageModal() {
  const modal = document.getElementById('messageModal');
  if (modal) {
    modal.classList.add('hide');
    setTimeout(() => modal.remove(), 500);
  }
}

// ================== CHATBOT ==================
const chatbot = document.getElementById('chatbot');
const chatbotHeader = document.getElementById('chatbotHeader');
const closeChatbotButton = document.getElementById('closeChatbot');
const openChatbotButton = document.getElementById('openChatbot');
const chatMessages = document.getElementById('chatMessages');
const chatInput = document.getElementById('chatInput');
const sendMessageButton = document.getElementById('sendMessage');
const summarizeButton = document.getElementById('summarizeButton');
const brainstormButton = document.getElementById('brainstormButton');
const clearButton = document.getElementById('clearButton');
const loadingIndicator = document.getElementById('loadingIndicator');
const settingsButton = document.getElementById('settingsButton');
const settingsModal = document.getElementById('settingsModal');
const projectPathInput = document.getElementById('projectPathInput');
const apiKeyInput = document.getElementById('apiKeyInput');
const modelSelect = document.getElementById('modelSelect');
const saveSettingsButton = document.getElementById('saveSettingsButton');
const cancelSettingsButton = document.getElementById('cancelSettingsButton');

// (You can keep the rest of your chatbot JS here – no change required)
</script>
@endsection
