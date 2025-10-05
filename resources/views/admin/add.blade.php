<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Add Content – Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>

  <style>
    /* ========= THEME TOKENS ========= */
    :root{
      --bg:#f5f7fb; --text:#111827; --muted:#6b7280;
      --primary:#3b82f6; --primary-600:#2563eb;
      --card:#ffffff; --input:#ffffff; --border:#e5e7eb; --ring:#93c5fd;
      --chip:#eef2ff; --shadow:0 6px 24px rgba(16,24,40,.06);
      --danger:#ef4444; --success:#10b981; --warn:#f59e0b;
    }
    body.admin-dark-mode{
      --bg:#0f172a; --text:#e5e7eb; --muted:#9ca3af;
      --primary:#60a5fa; --primary-600:#3b82f6;
      --card:#0b1220; --input:#0b1220; --border:#1f2937; --ring:#1d4ed8;
      --chip:#111827; --shadow:0 8px 30px rgba(0,0,0,.35);
      --danger:#f87171; --success:#34d399; --warn:#fbbf24;
    }

    /* ========= BASE ========= */
    *{box-sizing:border-box;margin:0;padding:0}
    body{
      font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      background:var(--bg); color:var(--text); line-height:1.55;
    }
    a{color:inherit;text-decoration:none}

    /* ========= HEADER ========= */
    .admin_header{
      position:sticky; top:0; z-index:50;
      display:flex; justify-content:space-between; align-items:center;
      padding:1rem 1.25rem; background:#1f2937; color:#fff;
      border-bottom:1px solid rgba(255,255,255,.08);
    }
    .logo img{height:40px}
    .admin_header_right{display:flex; align-items:center; gap:.5rem}
    .admin_header_right h1{font-size:1.05rem}
    .admin_theme_toggle{
      background:transparent; border:1px solid rgba(255,255,255,.25); color:#fff;
      padding:.45rem .6rem; border-radius:.6rem; cursor:pointer;
    }

    /* ========= LAYOUT ========= */
    .admin_main{
      display:grid; grid-template-columns:260px 1fr; min-height:calc(100vh - 64px);
    }
    .admin_sidebar{
      position:sticky; top:64px; align-self:start; height:calc(100vh - 64px);
      background:#111827; color:#cbd5e1; border-right:1px solid rgba(255,255,255,.06);
      overflow:auto;
    }
    .admin_sidebar_nav ul{list-style:none; padding:.75rem}
    .admin_sidebar_nav a, .admin_sidebar_nav button.linklike{
      width:100%; display:flex; align-items:center; gap:.65rem;
      padding:.6rem .75rem; margin-bottom:.15rem; color:inherit;
      background:transparent; border:none; cursor:pointer; border-radius:.5rem;
    }
    .admin_sidebar_nav a:hover{ background:rgba(255,255,255,.06) }
    .admin_sidebar_nav a.active{ background:rgba(59,130,246,.20); color:#fff }
    .admin_main_content{
      padding:1.25rem 1.25rem 2.5rem;
    }

    /* ========= SURFACE / CARD ========= */
    .surface{
      background:var(--card); border:1px solid var(--border);
      border-radius:14px; box-shadow:var(--shadow); overflow:hidden; margin-bottom:1rem;
    }
    .surface-head{
      display:flex; align-items:center; justify-content:space-between; gap:1rem;
      padding:1rem 1.25rem; border-bottom:1px solid var(--border);
    }
    .surface-body{ padding:1rem 1.25rem }

    /* ========= TABS ========= */
    .tabs{
      display:flex; gap:.25rem; border-bottom:1px solid var(--border); overflow:auto;
    }
    .tab{
      position:relative; padding:.8rem 1rem; font-weight:800; color:var(--muted);
      cursor:pointer; border-bottom:3px solid transparent; white-space:nowrap;
    }
    .tab.active{ color:var(--text); border-bottom-color:var(--primary) }
    .tab i{ margin-right:.5rem }
    .tab-panel{ display:none }
    .tab-panel.active{ display:block }

    /* ========= FORM ========= */
    .grid{ display:grid; gap:1rem }
    .g2{ grid-template-columns:repeat(2,minmax(0,1fr)) }
    .g3{ grid-template-columns:repeat(3,minmax(0,1fr)) }
    @media (max-width:980px){ .admin_main{ grid-template-columns:1fr } .admin_sidebar{ display:none } .g2,.g3{ grid-template-columns:1fr } }

    .field{ display:grid; gap:.4rem }
    .label{ font-weight:800; font-size:.92rem }
    .hint{ font-size:.8rem; color:var(--muted) }

    .control{
      display:flex; align-items:center; gap:.5rem;
      border:1px solid var(--border); background:var(--input); color:var(--text);
      border-radius:.7rem; padding:.7rem .85rem; transition:border-color .15s ease, box-shadow .15s ease;
    }
    .control:focus-within{
      border-color:var(--primary); box-shadow:0 0 0 3px var(--ring);
    }
    .control input[type="text"],
    .control input[type="number"],
    .control input[type="date"],
    .control input[type="datetime-local"],
    .control input[type="file"],
    .control textarea,
    .control select{
      border:none; outline:none; background:transparent; color:inherit; width:100%; font:inherit;
    }
    textarea{ min-height:120px; resize:vertical }

    .chips{ display:flex; flex-wrap:wrap; gap:.5rem }
    .chip{ background:var(--chip); color:var(--muted); padding:.25rem .6rem; border-radius:999px; font-size:.8rem; font-weight:800 }

    /* ========= BUTTONS / ALERTS ========= */
    .row{ display:flex; gap:.6rem; flex-wrap:wrap }
    .btn{ border:none; padding:.7rem 1rem; border-radius:.65rem; cursor:pointer; font-weight:800 }
    .btn.primary{ background:var(--primary); color:#fff }
    .btn.ghost{ background:transparent; border:1px solid var(--border); color:var(--text) }
    .btn.success{ background:var(--success); color:#052e26 }
    .btn.warn{ background:var(--warn); color:#111827 }
    .btn.full{ width:100% }

    .alert{ padding:.9rem 1rem; border:1px solid var(--border); border-radius:.75rem; background:var(--card); margin-bottom:1rem }
    .alert-success{ border-color:rgba(16,185,129,.35); background:rgba(16,185,129,.06) }
    .alert-error{ border-color:rgba(239,68,68,.35); background:rgba(239,68,68,.06) }

    /* ========= SELECT2 alignment ========= */
    .select2-container{ width:100%!important }
    .select2-selection--single{
      height:auto!important; border:1px solid var(--border)!important; border-radius:.7rem!important;
      background:var(--input)!important;
    }
    .select2-selection__rendered{ line-height:1.4!important; padding:.55rem .85rem!important; color:var(--text)!important }
    .select2-selection__arrow{ height:40px!important; right:.5rem!important }
    .select2-dropdown{
      background:var(--card)!important; color:var(--text)!important; border:1px solid var(--border)!important;
    }

    /* ========= MEDIA PREVIEW ========= */
    .media-row{ display:flex; align-items:flex-start; gap:1rem; flex-wrap:wrap }
    .preview{
      width:140px; height:200px; border-radius:12px; border:1px solid var(--border);
      background:linear-gradient(180deg, rgba(2,6,23,.05), transparent), var(--chip);
      object-fit:cover;
    }
  </style>
</head>
<body>
  <!-- HEADER -->
  <header class="admin_header">
    <div class="logo"><img src="{{ asset('assets/images/download.png') }}" alt="Logo"></div>
    <div class="admin_header_right">
      <h1>Add Content</h1>
      <button class="admin_theme_toggle" id="themeToggle" aria-label="Toggle theme"><i class="fas fa-moon"></i></button>
    </div>
  </header>

  <main class="admin_main">
    <!-- SIDEBAR -->
    <aside class="admin_sidebar">
      <nav class="admin_sidebar_nav">
        <ul>
          <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
          <li><a href="{{ route('admin.add') }}" class="active"><i class="fas fa-plus-circle"></i> Add</a></li>
          <li><a href="{{ route('admin.users') }}"><i class="fas fa-users"></i> Users</a></li>
          <li><a href="{{ route('admin.partners') }}"><i class="fas fa-handshake"></i> Partners</a></li>
          <li><a href="{{ route('admin.books') }}"><i class="fas fa-book"></i> Books</a></li>
          <li><a href="{{ route('admin.audiobooks') }}"><i class="fas fa-headphones"></i> Audio Books</a></li>
          <li><a href="{{ route('admin.partnerbooks') }}"><i class="fas fa-handshake"></i> Partners Books</a></li>
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

      @if(session('success')) <div class="alert alert-success"><i class="fa-regular fa-circle-check"></i> {{ session('success') }}</div> @endif
      @if(session('error'))   <div class="alert alert-error"><i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}</div> @endif

      <div class="surface">
        <div class="surface-head">
          <div class="row">
            <span class="chip">Create new content</span>
          </div>
          <div class="row" aria-hidden="true">
            <span class="chip">Use tabs below</span>
          </div>
        </div>
        <div class="surface-body">
          <!-- Tabs -->
          <div class="tabs" id="tabs">
            <div class="tab active" data-target="book-tab"><i class="fa-solid fa-book"></i> Book</div>
            <div class="tab" data-target="audiobook-tab"><i class="fa-solid fa-headphones"></i> Audio Book</div>
            <div class="tab" data-target="subscription-tab"><i class="fa-solid fa-star"></i> Subscription</div>
            <div class="tab" data-target="event-tab"><i class="fa-solid fa-calendar-alt"></i> Event</div>
          </div>

          <!-- BOOK -->
          <div class="tab-panel active" id="book-tab" role="tabpanel">
            <div class="grid g2" style="margin-top:1rem">
              <form action="{{ route('admin.add.book') }}" method="POST" enctype="multipart/form-data" class="grid" style="gap:1.25rem">
                @csrf

                <div class="grid g2">
                  <div class="field">
                    <label class="label" for="title">Book Title</label>
                    <div class="control"><i class="fa-regular fa-pen-to-square"></i><input id="title" name="title" type="text" required></div>
                  </div>

                  <div class="field">
                    <label class="label" for="published">Published Date</label>
                    <div class="control"><i class="fa-regular fa-calendar"></i><input id="published" name="published" type="date" required></div>
                  </div>

                  <div class="field">
                    <label class="label" for="price">Price</label>
                    <div class="control"><i class="fa-solid fa-dollar-sign"></i><input id="price" name="price" type="number" min="0" step="0.01"></div>
                  </div>

                  <div class="field">
                    <label class="label" for="quantity">Quantity</label>
                    <div class="control"><i class="fa-solid fa-boxes-stacked"></i><input id="quantity" name="quantity" type="number" min="1" value="1" required></div>
                  </div>

                  <div class="field">
                    <label class="label" for="rating">Rating</label>
                    <div class="control"><i class="fa-regular fa-star"></i><input id="rating" name="rating" type="number" min="1.00" max="5" step="0.01" value="4.00" required></div>
                  </div>

                  <div class="field">
                    <label class="label" for="writer_id">Writer</label>
                    <select id="writer_id" name="writer_id" class="admin_select2" required>
                      <option value="">Select Writer</option>
                      @foreach($writers as $w)
                        <option value="{{ $w->writer_id }}">{{ $w->name }}</option>
                      @endforeach
                    </select>
                    <div class="hint">Can’t find the writer? <a href="#" id="addNewWriter">Add New Writer</a></div>
                  </div>

                  <div class="field">
                    <label class="label" for="genre_id">Genre</label>
                    <select id="genre_id" name="genre_id" class="admin_select2" required>
                      <option value="">Select Genre</option>
                      @foreach($genres as $g)
                        <option value="{{ $g->genre_id }}">{{ $g->name }}</option>
                      @endforeach
                    </select>
                    <div class="hint">Can’t find the genre? <a href="#" id="addNewGenre">Add New Genre</a></div>
                  </div>

                  <div class="field">
                    <label class="label" for="category_id">Category</label>
                    <select id="category_id" name="category_id" class="admin_select2" required>
                      <option value="">Select Category</option>
                      @foreach($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                      @endforeach
                    </select>
                    <div class="hint">Can’t find the category? <a href="#" id="addNewCategory">Add New Category</a></div>
                  </div>

                  <div class="field">
                    <label class="label" for="language_id">Language</label>
                    <select id="language_id" name="language_id" class="admin_select2" required>
                      <option value="">Select Language</option>
                      @foreach($languages as $l)
                        <option value="{{ $l->language_id }}">{{ $l->name }}</option>
                      @endforeach
                    </select>
                    <div class="hint">Can’t find the language? <a href="#" id="addNewLanguage">Add New Language</a></div>
                  </div>
                </div>

                <div class="field">
                  <label class="label" for="details">Book Details</label>
                  <div class="control"><textarea id="details" name="details" placeholder="Write a short synopsis, edition info, etc."></textarea></div>
                </div>

                <div class="field">
                  <label class="label" for="cover_image">Cover Image</label>
                  <div class="media-row">
                    <img id="bookCoverPreview" class="preview" alt="">
                    <div class="control" style="flex:1"><i class="fa-regular fa-image"></i><input id="cover_image" name="cover_image" type="file" accept="image/*"></div>
                  </div>
                  <div class="hint">Image will be saved in <code>assets/book_covers/</code></div>
                </div>

                <div class="row" style="margin-top:.25rem">
                  <button type="submit" name="add_book" class="btn primary"><i class="fa-solid fa-plus"></i> Add Book</button>
                </div>
              </form>
            </div>
          </div>

          <!-- AUDIOBOOK -->
          <div class="tab-panel" id="audiobook-tab" role="tabpanel">
            <form action="{{ route('admin.add.audiobook') }}" method="POST" enctype="multipart/form-data" class="grid" style="gap:1rem; margin-top:1rem">
              @csrf
              <div class="grid g2">
                <div class="field">
                  <label class="label" for="audio_title">Title</label>
                  <div class="control"><i class="fa-regular fa-pen-to-square"></i><input id="audio_title" name="audio_title" type="text" required></div>
                </div>
                <div class="field">
                  <label class="label" for="audio_writer">Writer</label>
                  <div class="control"><i class="fa-regular fa-user"></i><input id="audio_writer" name="audio_writer" type="text" required></div>
                </div>
                <div class="field">
                  <label class="label" for="audio_genre">Genre</label>
                  <div class="control"><i class="fa-solid fa-tags"></i><input id="audio_genre" name="audio_genre" type="text" required></div>
                </div>
                <div class="field">
                  <label class="label" for="audio_category">Category</label>
                  <div class="control"><i class="fa-solid fa-layer-group"></i><input id="audio_category" name="audio_category" type="text" required></div>
                </div>
                <div class="field">
                  <label class="label" for="audio_language_id">Language</label>
                  <select id="audio_language_id" name="audio_language_id" class="admin_select2" required>
                    <option value="">Select Language</option>
                    @foreach($languages as $l)
                      <option value="{{ $l->language_id }}">{{ $l->name }}</option>
                    @endforeach
                  </select>
                  <div class="hint">Can’t find the language? <a href="#" id="addNewLanguage2">Add New Language</a></div>
                </div>
                <div class="field">
                  <label class="label" for="audio_duration">Duration (HH:MM:SS)</label>
                  <div class="control"><i class="fa-regular fa-clock"></i><input id="audio_duration" name="audio_duration" type="text" placeholder="00:45:30" required></div>
                </div>
              </div>

              <div class="field">
                <label class="label" for="audio_description">Description</label>
                <div class="control"><textarea id="audio_description" name="audio_description"></textarea></div>
              </div>

              <div class="grid g2">
                <div class="field">
                  <label class="label" for="audio_file">Audio File</label>
                  <div class="control"><i class="fa-solid fa-music"></i><input id="audio_file" name="audio_file" type="file" accept="audio/*" required></div>
                  <div class="hint">Supported: MP3, WAV, AAC</div>
                </div>
                <div class="field">
                  <label class="label" for="audio_poster">Poster Image (optional)</label>
                  <div class="media-row">
                    <img id="audioPosterPreview" class="preview" alt="">
                    <div class="control" style="flex:1"><i class="fa-regular fa-image"></i><input id="audio_poster" name="audio_poster" type="file" accept="image/*"></div>
                  </div>
                </div>
              </div>

              <div class="row" style="margin-top:.25rem">
                <button type="submit" name="add_audiobook" class="btn primary"><i class="fa-solid fa-plus"></i> Add Audio Book</button>
              </div>
            </form>
          </div>

          <!-- SUBSCRIPTION -->
          <div class="tab-panel" id="subscription-tab" role="tabpanel">
            <form action="{{ route('admin.add.subscription') }}" method="POST" class="grid" style="gap:1rem; margin-top:1rem">
              @csrf
              <div class="grid g3">
                <div class="field">
                  <label class="label" for="plan_name">Plan Name</label>
                  <div class="control"><i class="fa-regular fa-pen-to-square"></i><input id="plan_name" name="plan_name" type="text" required></div>
                </div>
                <div class="field">
                  <label class="label" for="price">Price</label>
                  <div class="control"><i class="fa-solid fa-dollar-sign"></i><input id="price" name="price" type="number" min="0" step="0.01" required></div>
                </div>
                <div class="field">
                  <label class="label" for="validity_days">Validity (Days)</label>
                  <div class="control"><i class="fa-regular fa-calendar"></i><input id="validity_days" name="validity_days" type="number" min="1" required></div>
                </div>
                <div class="field">
                  <label class="label" for="book_quantity">Books Allowed</label>
                  <div class="control"><i class="fa-solid fa-book"></i><input id="book_quantity" name="book_quantity" type="number" min="0" required></div>
                </div>
                <div class="field">
                  <label class="label" for="audiobook_quantity">Audiobooks Allowed</label>
                  <div class="control"><i class="fa-solid fa-headphones"></i><input id="audiobook_quantity" name="audiobook_quantity" type="number" min="0" required></div>
                </div>
                <div class="field">
                  <label class="label" for="status">Status</label>
                  <div class="control">
                    <select id="status" name="status" required>
                      <option value="active">Active</option>
                      <option value="inactive">Inactive</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="field">
                <label class="label" for="plan_description">Description</label>
                <div class="control"><textarea id="plan_description" name="plan_description"></textarea></div>
              </div>

              <div class="row" style="margin-top:.25rem">
                <button type="submit" name="add_subscription" class="btn primary"><i class="fa-solid fa-plus"></i> Add Subscription Plan</button>
              </div>
            </form>
          </div>

          <!-- EVENT -->
          <div class="tab-panel" id="event-tab" role="tabpanel">
            <form action="{{ route('admin.add.event') }}" method="POST" enctype="multipart/form-data" class="grid" style="gap:1rem; margin-top:1rem">
              @csrf
              <div class="grid g2">
                <div class="field">
                  <label class="label" for="event_name">Event Name</label>
                  <div class="control"><i class="fa-regular fa-pen-to-square"></i><input id="event_name" name="event_name" type="text" required></div>
                </div>
                <div class="field">
                  <label class="label" for="event_venue">Venue</label>
                  <div class="control"><i class="fa-solid fa-location-dot"></i><input id="event_venue" name="event_venue" type="text" required></div>
                </div>
                <div class="field">
                  <label class="label" for="event_date">Event Date & Time</label>
                  <div class="control"><i class="fa-regular fa-clock"></i><input id="event_date" name="event_date" type="datetime-local" required></div>
                </div>
                <div class="field">
                  <label class="label" for="event_status">Status</label>
                  <div class="control">
                    <select id="event_status" name="event_status" required>
                      <option value="upcoming">Upcoming</option>
                      <option value="ongoing">Ongoing</option>
                      <option value="completed">Completed</option>
                      <option value="cancelled">Cancelled</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="field">
                <label class="label" for="event_description">Description</label>
                <div class="control"><textarea id="event_description" name="event_description"></textarea></div>
              </div>

              <div class="field">
                <label class="label" for="event_banner">Banner Image (optional)</label>
                <div class="media-row">
                  <img id="eventBannerPreview" class="preview" alt="">
                  <div class="control" style="flex:1"><i class="fa-regular fa-image"></i><input id="event_banner" name="event_banner" type="file" accept="image/*"></div>
                </div>
              </div>

              <div class="row" style="margin-top:.25rem">
                <button type="submit" name="add_event" class="btn primary"><i class="fa-solid fa-plus"></i> Add Event</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      {{-- Modal: quick add (Writer/Genre/Category/Language) --}}
      <div id="adminModal" class="admin_modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:60;align-items:center;justify-content:center;padding:1rem">
        <div class="admin_modal_content" style="background:var(--card);color:var(--text);border:1px solid var(--border);border-radius:12px;box-shadow:var(--shadow);width:min(520px,96vw);position:relative">
          <button class="admin_modal_close" style="position:absolute;right:.75rem;top:.75rem;background:transparent;border:none;font-size:1.35rem;color:var(--muted);cursor:pointer">&times;</button>
          <div style="padding:1.25rem;border-bottom:1px solid var(--border);font-weight:800" id="modalTitle">Add New Item</div>
          <form id="modalForm" style="padding:1rem 1.25rem 1.25rem">
            @csrf
            <div class="field">
              <label id="modalFieldLabel" class="label">Name</label>
              <div class="control"><input type="text" id="modalFieldInput" required></div>
              <input type="hidden" id="modalType" value="">
            </div>
            <div class="row" style="margin-top:1rem;justify-content:flex-end">
              <button type="button" class="btn ghost" id="modalCancel">Cancel</button>
              <button type="submit" class="btn primary">Add</button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </main>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
    // Theme toggle
    const themeToggle=document.getElementById('themeToggle');
    const icon=themeToggle.querySelector('i');
    const prefersDark=window.matchMedia('(prefers-color-scheme: dark)').matches;
    const savedTheme=localStorage.getItem('admin-theme');
    if(savedTheme==='dark'||(!savedTheme&&prefersDark)){document.body.classList.add('admin-dark-mode');icon.classList.replace('fa-moon','fa-sun');}
    themeToggle.addEventListener('click',()=>{document.body.classList.toggle('admin-dark-mode'); if(document.body.classList.contains('admin-dark-mode')){localStorage.setItem('admin-theme','dark');icon.classList.replace('fa-moon','fa-sun');}else{localStorage.setItem('admin-theme','light');icon.classList.replace('fa-sun','fa-moon');} refreshSelect2();});

    // Tabs
    const tabs=document.querySelectorAll('.tab'); const panels=document.querySelectorAll('.tab-panel');
    tabs.forEach(t=>t.addEventListener('click',()=>{tabs.forEach(x=>x.classList.remove('active')); t.classList.add('active'); const target=t.dataset.target; panels.forEach(p=>p.classList.toggle('active', p.id===target)); refreshSelect2();}));

    // Select2 init / refresh
    function refreshSelect2(){
      $('.admin_select2').each(function(){
        if($(this).hasClass('select2-hidden-accessible')){ $(this).select2('destroy'); }
        $(this).select2({
          dropdownParent: $('.admin_main_content'),
          width:'100%',
        });
      });
    }
    $(document).ready(refreshSelect2);

    // Live image previews
    function bindPreview(inputId, imgId){
      const input=document.getElementById(inputId), img=document.getElementById(imgId);
      if(!input||!img) return;
      input.addEventListener('change',(e)=>{const f=e.target.files?.[0]; if(!f) return; const r=new FileReader(); r.onload=ev=>img.src=ev.target.result; r.readAsDataURL(f);});
    }
    bindPreview('cover_image','bookCoverPreview');
    bindPreview('audio_poster','audioPosterPreview');
    bindPreview('event_banner','eventBannerPreview');

    // Modal helpers
    const modal  = document.getElementById('adminModal');
    const mClose = document.querySelector('.admin_modal_close');
    const mCancel= document.getElementById('modalCancel');
    function openModal(title,label,type){
      document.getElementById('modalTitle').textContent=title;
      document.getElementById('modalFieldLabel').textContent=label;
      document.getElementById('modalType').value=type;
      document.getElementById('modalFieldInput').value='';
      modal.style.display='flex';
    }
    function closeModal(){ modal.style.display='none'; }
    mClose.addEventListener('click',closeModal);
    mCancel.addEventListener('click',closeModal);
    window.addEventListener('click',e=>{ if(e.target===modal) closeModal(); });
    window.addEventListener('keydown',e=>{ if(e.key==='Escape') closeModal(); });

    // Quick-add links
    document.getElementById('addNewWriter')?.addEventListener('click',e=>{e.preventDefault(); openModal('Add New Writer','Writer Name','writer');});
    document.getElementById('addNewGenre')?.addEventListener('click',e=>{e.preventDefault(); openModal('Add New Genre','Genre Name','genre');});
    document.getElementById('addNewCategory')?.addEventListener('click',e=>{e.preventDefault(); openModal('Add New Category','Category Name','category');});
    // For Audiobook tab language shortcut
    document.getElementById('addNewLanguage')?.addEventListener('click',e=>{e.preventDefault(); openModal('Add New Language','Language Name','language');});
    document.getElementById('addNewLanguage2')?.addEventListener('click',e=>{e.preventDefault(); openModal('Add New Language','Language Name','language');});

    // Modal submit (AJAX)
    $('#modalForm').on('submit', function(e){
      e.preventDefault();
      const type=$('#modalType').val();
      const name=($('#modalFieldInput').val()||'').trim();
      if(!name){ alert('Please enter a name.'); return; }

      $.ajax({
        url: "{{ route('admin.add.item') }}",
        method: 'POST',
        headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
        data: { type, name, status: (type==='language'?'active':null) },
        dataType:'json',
        success: function(res){
          if(res?.success){
            const id = res.id;
            if(type==='language'){
              ['language_id','audio_language_id'].forEach(sel=>{
                const $el = $('#'+sel);
                const opt = new Option(name, id, true, true);
                $el.append(opt).trigger('change');
              });
            }else{
              const map={writer:'writer_id', genre:'genre_id', category:'category_id'};
              const sel=map[type]; if(sel){ const $el=$('#'+sel); const opt=new Option(name,id,true,true); $el.append(opt).trigger('change'); }
            }
            closeModal();
          }else{
            alert('Error: ' + (res?.message || 'Unknown error'));
          }
        },
        error: function(xhr){ alert('Error adding item: ' + (xhr.responseJSON?.message || xhr.statusText)); }
      });
    });
  </script>
</body>
</html>
