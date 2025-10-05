{{-- resources/views/admin/book_edit.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard – Edit Book</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

  <style>
    /* ========= THEME TOKENS ========= */
    :root{
      --bg:#f5f7fb; --text:#1f2937; --text-muted:#6b7280;
      --primary:#3b82f6; --success:#10b981; --warning:#f59e0b; --danger:#ef4444;
      --card:#ffffff; --border:#e5e7eb; --table-header:#f3f4f6; --chip:#eef2ff;
      --shadow:0 1px 1px rgba(0,0,0,.02);
      --input-bg:#fff;
    }
    body.admin-dark-mode{
      --bg:#0f172a; --text:#e5e7eb; --text-muted:#9ca3af;
      --primary:#60a5fa; --success:#34d399; --warning:#fbbf24; --danger:#f87171;
      --card:#111827; --border:#1f2937; --table-header:#0b1220; --chip:#1f2937;
      --input-bg:#0b1220;
    }

    *{box-sizing:border-box;margin:0;padding:0}
    body{
      font-family:ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial;
      background:var(--bg); color:var(--text); line-height:1.5;
    }
    a{color:inherit;text-decoration:none}

    /* ========= HEADER ========= */
    .admin_header{
      display:flex; justify-content:space-between; align-items:center;
      padding:1rem 1.5rem; background:#1f2937; color:#fff;
      position:sticky; top:0; z-index:30; border-bottom:1px solid rgba(255,255,255,.06);
    }
    .logo img{height:40px}
    .admin_header_right{display:flex; align-items:center; gap:.75rem}
    .admin_header_right h1{font-size:1.1rem; font-weight:700}
    .admin_theme_toggle{
      background:transparent; border:1px solid rgba(255,255,255,.2); color:#fff;
      padding:.5rem .65rem; border-radius:.5rem; cursor:pointer;
    }
    .chip{ font-size:.75rem; padding:.25rem .5rem; border-radius:999px; background:var(--chip); color:var(--text-muted); font-weight:700 }

    /* ========= LAYOUT ========= */
    .admin_main{ display:grid; grid-template-columns:260px 1fr; min-height:calc(100vh - 64px) }
    .admin_sidebar{
      background:#111827; color:#cbd5e1; border-right:1px solid rgba(255,255,255,.06);
      position:sticky; top:64px; align-self:start; height:calc(100vh - 64px); overflow-y:auto;
    }
    .admin_sidebar_nav ul{ list-style:none; padding:.75rem }
    .admin_sidebar_nav a, .admin_sidebar_nav button.linklike{
      display:flex; align-items:center; gap:.75rem; padding:.65rem .75rem; margin-bottom:.25rem;
      color:inherit; border-radius:.5rem; border:none; background:transparent; width:100%; text-align:left; cursor:pointer;
    }
    .admin_sidebar_nav a:hover, .admin_sidebar_nav button.linklike:hover{ background:rgba(255,255,255,.06) }
    .admin_sidebar_nav a.active{ background:rgba(59,130,246,.18); color:#fff }
    .admin_main_content{ padding:1.25rem 1.5rem }

    /* ========= SECTIONS ========= */
    .page-head{
      display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:1rem;
    }
    .breadcrumbs{ color:var(--text-muted); font-size:.9rem }
    .breadcrumbs a{ color:inherit }
    .actions-inline{ display:flex; gap:.5rem; flex-wrap:wrap }

    .section{ background:var(--card); border:1px solid var(--border); border-radius:12px; box-shadow:var(--shadow); margin-bottom:1rem }
    .section-head{
      padding:1rem 1.25rem; border-bottom:1px solid var(--border);
      display:flex; align-items:center; justify-content:space-between; gap:1rem;
    }
    .title{ font-weight:800; font-size:1.1rem }
    .muted{ color:var(--text-muted) }
    .pad{ padding:1.25rem }

    /* ========= FORM ========= */
    .grid{ display:grid; gap:1rem }
    .grid.g2{ grid-template-columns:repeat(2,minmax(0,1fr)) }
    @media (max-width:920px){ .admin_main{ grid-template-columns:1fr } .admin_sidebar{ display:none } .grid.g2{ grid-template-columns:1fr } }

    .form-row{ display:grid; gap:.5rem }
    label{ font-weight:800; font-size:.92rem }
    .input, .textarea, .select{
      width:100%; padding:.7rem .85rem; border-radius:.6rem; border:1px solid var(--border);
      background:var(--input-bg); color:var(--text); outline:none; transition:border-color .15s ease;
    }
    .textarea{ min-height:140px; resize:vertical }
    .input:focus, .textarea:focus, .select:focus{ border-color:var(--primary) }

    .checkboxes{ display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:.5rem 1rem }
    @media (max-width:620px){ .checkboxes{ grid-template-columns:1fr } }
    .check{ display:flex; align-items:center; gap:.5rem }
    .check input{ width:16px; height:16px }

    .media-wrap{ display:flex; gap:1rem; align-items:flex-start; flex-wrap:wrap }
    .cover{
      width:160px; height:220px; border-radius:12px; border:1px solid var(--border);
      object-fit:cover; background:var(--table-header);
    }

    /* ========= BUTTONS & ALERTS ========= */
    .btn{ padding:.7rem 1rem; border:none; border-radius:.65rem; cursor:pointer; font-weight:800 }
    .btn.primary{ background:var(--primary); color:#fff }
    .btn.ghost{ background:transparent; border:1px solid var(--border); color:var(--text) }
    .btn.success{ background:var(--success); color:#052e26 }
    .btn.muted{ background:#e5e7eb; color:#111827 }
    body.admin-dark-mode .btn.muted{ background:#1f2937; color:#e5e7eb }
    .bar{ display:flex; gap:.5rem; flex-wrap:wrap }

    .alert{ padding:1rem; margin:1rem 0; border-radius:12px; border:1px solid var(--border); background:var(--card) }
    .alert-error{ border-color:rgba(239,68,68,.35); color:#991b1b; background:#fee2e2 }
    .alert-success{ border-color:rgba(16,185,129,.35); color:#065f46; background:#d1fae5 }
    body.admin-dark-mode .alert-error{ color:#fecaca; background:#3b0f11; border-color:#7f1d1d }
    body.admin-dark-mode .alert-success{ color:#a7f3d0; background:#052e26; border-color:#065f46 }

    /* ========= STICKY FOOTER BAR (submit) ========= */
    .sticky-actions{
      position:sticky; bottom:0; z-index:10; padding:.75rem 1rem;
      background:linear-gradient(to top, var(--card), rgba(0,0,0,0));
      border-top:1px solid var(--border); display:flex; justify-content:flex-end; gap:.5rem; border-radius:0 0 12px 12px;
    }
  </style>
</head>
<body>
  <!-- HEADER -->
  <header class="admin_header">
    <div class="logo"><img src="{{ asset('assets/images/download.png') }}" alt="Logo"></div>
    <div class="admin_header_right">
      <h1>Edit Book</h1>
      <span class="chip">#{{ $book->book_id }}</span>
      <button class="admin_theme_toggle" id="themeToggle" aria-label="Toggle theme"><i class="fas fa-moon"></i></button>
    </div>
  </header>

  <main class="admin_main">
    <!-- SIDEBAR -->
    <aside class="admin_sidebar">
      <nav class="admin_sidebar_nav">
        <ul>
          <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
          <li><a href="{{ route('admin.add') }}"><i class="fas fa-plus-circle"></i> Add</a></li>
          <li><a href="{{ route('admin.users') }}"><i class="fas fa-users"></i> Users</a></li>
          <li><a href="{{ route('admin.partners') }}"><i class="fas fa-handshake"></i> Partners</a></li>
          <li><a href="{{ route('admin.writers') }}"><i class="fas fa-pen-fancy"></i> Writers</a></li>
          <li><a href="{{ route('admin.books') }}" class="active"><i class="fas fa-book"></i> Books</a></li>
          <li><a href="{{ route('admin.audiobooks') }}"><i class="fas fa-headphones"></i> Audio Books</a></li>
          <li><a href="{{ route('admin.partnerbooks') }}"><i class="fas fa-book-reader"></i> Partner Books</a></li>
          <li><a href="{{ route('admin.orders') }}"><i class="fas fa-shopping-cart"></i> Orders</a></li>
          <li><a href="{{ route('admin.subscription') }}"><i class="fas fa-star"></i> Subscription</a></li>
          <li><a href="{{ route('admin.events') }}"><i class="fas fa-calendar-alt"></i> Events</a></li>
          <li><a href="{{ route('admin.question') }}"><i class="fa-solid fa-question"></i> User Questions</a></li>
          <li><a href="{{ route('admin.community') }}"><i class="fas fa-users"></i> Community</a></li>
          <li><a href="{{ route('admin.reports') }}"><i class="fas fa-chart-bar"></i> Reports</a></li>
          <li>
            <form method="POST" action="{{ route('admin.logout') }}">
              @csrf
              <button type="submit" class="linklike"><i class="fas fa-sign-out-alt"></i> Logout</button>
            </form>
          </li>
        </ul>
      </nav>
    </aside>

    <!-- CONTENT -->
    <div class="admin_main_content">

      <div class="page-head">
        <div class="breadcrumbs">
          <a href="{{ route('admin.books') }}"><i class="fa-solid fa-angle-left"></i> Back to Books</a>
        </div>
        <div class="actions-inline">
          <a href="{{ route('admin.books') }}" class="btn ghost"><i class="fa-solid fa-list"></i> All Books</a>
        </div>
      </div>

      {{-- Alerts --}}
      @if(session('error'))   <div class="alert alert-error"><i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}</div> @endif
      @if(session('success')) <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div> @endif
      @if ($errors->any())
        <div class="alert alert-error">
          <b>Fix the following:</b>
          <ul style="margin:.5rem 0 0 1rem">
            @foreach ($errors->all() as $msg)
              <li>{{ $msg }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('admin.books.update') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="book_id" value="{{ $book->book_id }}">
        <input type="hidden" name="current_cover" value="{{ $book->cover_image_url }}">

        <!-- Book Meta -->
        <div class="section">
          <div class="section-head">
            <div class="title"><i class="fa-solid fa-info-circle"></i> Book Meta</div>
            <span class="muted">Primary information</span>
          </div>
          <div class="pad grid g2">
            <div class="form-row">
              <label for="title">Title</label>
              <input id="title" name="title" type="text" class="input" value="{{ old('title', $book->title) }}" required>
            </div>

            <div class="form-row">
              <label for="writer_id">Author</label>
              <select id="writer_id" name="writer_id" class="select" required>
                @foreach($writers as $w)
                  <option value="{{ $w->writer_id }}" {{ (old('writer_id', $book->writer_id) == $w->writer_id) ? 'selected' : '' }}>
                    {{ $w->name }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="form-row">
              <label for="published">Published Date</label>
              <input id="published" name="published" type="date" class="input" value="{{ old('published', $book->published) }}">
            </div>

            <div class="form-row">
              <label for="price">Price</label>
              <input id="price" name="price" type="number" step="0.01" class="input" value="{{ old('price', $book->price) }}" required>
            </div>

            <div class="form-row">
              <label for="quantity">Quantity</label>
              <input id="quantity" name="quantity" type="number" class="input" value="{{ old('quantity', $book->quantity) }}" required>
            </div>

            <div class="form-row" style="grid-column:1/-1">
              <label for="details">Details</label>
              <textarea id="details" name="details" class="textarea">{{ old('details', $book->details) }}</textarea>
            </div>
          </div>
        </div>

        <!-- Classification -->
        <div class="section">
          <div class="section-head">
            <div class="title"><i class="fa-solid fa-tags"></i> Classification</div>
            <span class="muted">Categories • Genres • Languages</span>
          </div>
          <div class="pad grid g2">
            <div class="form-row">
              <label>Categories</label>
              <div class="checkboxes">
                @foreach($categories as $c)
                  <label class="check" for="cat_{{ $c->id }}">
                    <input type="checkbox" id="cat_{{ $c->id }}" name="categories[]" value="{{ $c->id }}"
                      {{ in_array($c->id, old('categories', $bookCategoryIds)) ? 'checked' : '' }}>
                    <span>{{ $c->name }}</span>
                  </label>
                @endforeach
              </div>
            </div>

            <div class="form-row">
              <label>Genres</label>
              <div class="checkboxes">
                @foreach($genres as $g)
                  <label class="check" for="genre_{{ $g->genre_id }}">
                    <input type="checkbox" id="genre_{{ $g->genre_id }}" name="genres[]" value="{{ $g->genre_id }}"
                      {{ in_array($g->genre_id, old('genres', $bookGenreIds)) ? 'checked' : '' }}>
                    <span>{{ $g->name }}</span>
                  </label>
                @endforeach
              </div>
            </div>

            <div class="form-row" style="grid-column:1/-1">
              <label>Languages</label>
              <div class="checkboxes" style="grid-template-columns:repeat(3,minmax(0,1fr))">
                @foreach($languages as $l)
                  <label class="check" for="lang_{{ $l->language_id }}">
                    <input type="checkbox" id="lang_{{ $l->language_id }}" name="languages[]" value="{{ $l->language_id }}"
                      {{ in_array($l->language_id, old('languages', $bookLanguageIds)) ? 'checked' : '' }}>
                    <span>{{ $l->name }}</span>
                  </label>
                @endforeach
              </div>
            </div>
          </div>
        </div>

        <!-- Media -->
        <div class="section">
          <div class="section-head">
            <div class="title"><i class="fa-solid fa-image"></i> Media</div>
            <span class="muted">Cover image</span>
          </div>
          <div class="pad">
            <div class="media-wrap">
              <img
                id="coverPreview"
                class="cover"
                src="{{ $book->cover_image_url ? asset($book->cover_image_url) : 'https://via.placeholder.com/160x220?text=No+Cover' }}"
                alt="Book Cover"
              />
              <div class="grid" style="min-width:240px">
                <div class="form-row">
                  <label for="cover_image">Change Cover Image</label>
                  <input id="cover_image" name="cover_image" type="file" class="input" accept="image/*">
                  <small class="muted">JPG, JPEG, PNG, WEBP</small>
                </div>
              </div>
            </div>
          </div>
          <div class="sticky-actions">
            <a href="{{ route('admin.books') }}" class="btn ghost"><i class="fa-solid fa-xmark"></i> Cancel</a>
            <button type="submit" class="btn primary"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
          </div>
        </div>
      </form>
    </div>
  </main>

  <script>
    // Theme toggle
    const themeToggle = document.getElementById('themeToggle');
    const icon = themeToggle?.querySelector('i');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const savedTheme = localStorage.getItem('admin-theme');
    if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
      document.body.classList.add('admin-dark-mode'); icon?.classList.replace('fa-moon','fa-sun');
    }
    themeToggle?.addEventListener('click', () => {
      document.body.classList.toggle('admin-dark-mode');
      if (document.body.classList.contains('admin-dark-mode')) {
        localStorage.setItem('admin-theme','dark'); icon?.classList.replace('fa-moon','fa-sun');
      } else {
        localStorage.setItem('admin-theme','light'); icon?.classList.replace('fa-sun','fa-moon');
      }
    });

    // Live cover preview
    const coverInput = document.getElementById('cover_image');
    const coverPreview = document.getElementById('coverPreview');
    coverInput?.addEventListener('change', (e) => {
      const file = e.target.files?.[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = (ev) => { coverPreview.src = ev.target.result; };
      reader.readAsDataURL(file);
    });
  </script>
</body>
</html>
