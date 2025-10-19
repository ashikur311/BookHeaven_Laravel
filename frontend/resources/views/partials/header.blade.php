@php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Auth;

    $page_title = $page_title ?? 'Book Heaven';
    $cart_count = 0;
    $is_logged_in = Auth::check();

    if ($is_logged_in) {
        $cart_count = DB::table('cart')
            ->where('user_id', Auth::id())
            ->distinct('book_id')
            ->count();
    }
@endphp

<header>
    <div class="header-container">
        <a href="{{ route('home') }}" class="logo">
            <i class="fas fa-book-open"></i>
            <span>Book Heaven</span>
        </a>

        {{-- 🔍 Search Bar --}}
        <div class="search-container">
            <form action="{{ route('search') }}" method="GET" id="searchForm">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-bar" name="query" id="searchInput"
                       placeholder="Search books, authors..." autocomplete="off">
                <div class="search-suggestions" id="searchSuggestions"></div>
            </form>
        </div>

        {{-- 🌐 Navbar --}}
        <nav>
            <button class="menu-toggle" aria-label="Toggle menu">
                <i class="fas fa-bars"></i>
            </button>

            <ul class="nav-links">
                <li><a href="{{ route('music.player') }}"><i class="fas fa-music"></i><span>Audio</span></a></li>
                <li><a href="{{ route('cart') }}"><i class="fas fa-shopping-cart"></i>
                    <span class="cart-count">{{ $cart_count }}</span></a></li>
                <li><a href="{{ route('profile') }}"><i class="fas fa-user"></i><span>Profile</span></a></li>
                <li><a href="{{ route('partner.dashboard') }}"><i class="fas fa-handshake"></i><span>Partner</span></a></li>
                <li><a href="{{ route('community.dashboard') }}"><i class="fas fa-users"></i><span>Community</span></a></li>
            </ul>

            <div class="nav-buttons">
                @auth
                    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button class="btn btn-outline">
                            <i class="fas fa-sign-out-alt"></i><span>Logout</span>
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline">
                        <i class="fas fa-sign-in-alt"></i><span>Login</span>
                    </a>
                @endauth

                <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
                    <i class="fas fa-moon"></i>
                </button>
            </div>
        </nav>
    </div>
</header>


{{-- ✅ SCRIPT (Laravel-updated version, no "/php/" paths) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 🔸 Toggle mobile menu
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    menuToggle.addEventListener('click', () => {
        navLinks.classList.toggle('active');
        document.body.classList.toggle('menu-open');
    });
    document.addEventListener('click', (e) => {
        if (!e.target.closest('nav') && navLinks.classList.contains('active')) {
            navLinks.classList.remove('active');
            document.body.classList.remove('menu-open');
        }
    });

    // 🌙 Theme toggle with localStorage
    const themeToggle = document.getElementById('themeToggle');
    const icon = themeToggle.querySelector('i');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    let darkMode = localStorage.getItem('darkMode');
    if (darkMode === 'enabled' || (!darkMode && prefersDark)) enableDarkMode();

    themeToggle.addEventListener('click', () => {
        darkMode = localStorage.getItem('darkMode');
        if (darkMode !== 'enabled') enableDarkMode();
        else disableDarkMode();
    });

    function enableDarkMode() {
        document.body.classList.add('dark-mode');
        icon.classList.replace('fa-moon', 'fa-sun');
        localStorage.setItem('darkMode', 'enabled');
    }

    function disableDarkMode() {
        document.body.classList.remove('dark-mode');
        icon.classList.replace('fa-sun', 'fa-moon');
        localStorage.setItem('darkMode', 'disabled');
    }

    // 🔍 Search suggestions (AJAX)
    const searchInput = $('#searchInput');
    const searchSuggestions = $('#searchSuggestions');

    searchInput.on('input', function () {
        const query = $(this).val().trim();
        if (query.length > 0) {
            $.ajax({
                url: '{{ route("search.suggestions") }}',
                method: 'GET',
                data: { query: query },
                success: function (data) {
                    if (data.length > 0) {
                        let suggestionsHtml = '';
                        data.forEach(item => {
                            suggestionsHtml += `
                                <div class="suggestion-item" data-type="${item.type}" data-id="${item.id}">
                                    ${item.name}
                                    <span class="suggestion-type">${item.type}</span>
                                </div>`;
                        });
                        searchSuggestions.html(suggestionsHtml).show();
                    } else {
                        searchSuggestions.html('<div class="no-results">No results found</div>').show();
                    }
                }
            });
        } else {
            searchSuggestions.hide();
        }
    });

    // 🧭 Suggestion click redirects (using Laravel routes)
    $(document).on('click', '.suggestion-item', function () {
        const type = $(this).data('type');
        const id = $(this).data('id');

        if (type === 'book') {
            window.location.href = `/books/${id}`;
        } else if (type === 'author') {
            window.location.href = `/writers/${id}`;
        } else if (type === 'genre') {
            window.location.href = `/genres/${id}`;
        }
    });

    // 🔍 Submit search form
    $('#searchForm').on('submit', function (e) {
        e.preventDefault();
        const query = searchInput.val().trim();
        if (query) {
            window.location.href = '{{ route("search") }}' + '?query=' + encodeURIComponent(query);
        }
    });

    // Hide suggestions when clicking outside
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.search-container').length) {
            searchSuggestions.hide();
        }
    });
});
</script>
