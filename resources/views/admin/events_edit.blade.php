{{-- resources/views/admin/events_edit.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Event – Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

  <style>
    /* ========= THEME TOKENS (same as Events/Orders) ========= */
    :root{
      --bg:#f5f7fb; --text:#1f2937; --text-muted:#6b7280;
      --primary:#3b82f6; --success:#10b981; --warning:#f59e0b; --danger:#ef4444;
      --card:#ffffff; --border:#e5e7eb; --table-header:#f3f4f6; --table-hover:#f9fafb; --chip:#eef2ff;
      --shadow:0 1px 1px rgba(0,0,0,.02);
    }
    body.admin-dark-mode{
      --bg:#0f172a; --text:#e5e7eb; --text-muted:#9ca3af;
      --primary:#60a5fa; --success:#34d399; --warning:#fbbf24; --danger:#f87171;
      --card:#111827; --border:#1f2937; --table-header:#0b1220; --table-hover:#0c1427; --chip:#1f2937;
    }

    *{box-sizing:border-box;margin:0;padding:0}
    body{
      font-family:ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial;
      background:var(--bg); color:var(--text); line-height:1.5;
    }
    a{color:inherit; text-decoration:none}

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

    .admin_main_content{ padding:1.5rem }

    /* ========= CARDS / GRID ========= */
    .section{ background:var(--card); border:1px solid var(--border); border-radius:12px; box-shadow:var(--shadow); margin-bottom:1rem }
    .section-head{
      padding:1rem 1.25rem; border-bottom:1px solid var(--border);
      display:flex; align-items:center; justify-content:space-between; gap:1rem;
    }
    .title{ font-weight:800; font-size:1.1rem }
    .muted{ color:var(--text-muted) }
    .pad{ padding:1.25rem }

    .page{ display:grid; grid-template-columns:1fr 360px; gap:1rem }
    @media (max-width:1000px){ .page{ grid-template-columns:1fr } }

    .form-grid{ display:grid; grid-template-columns:1fr 1fr; gap:1rem }
    @media (max-width:720px){ .form-grid{ grid-template-columns:1fr } }

    .form-group{ display:grid; gap:.45rem }
    .form-group label{ font-weight:800 }
    .form-control{
      width:100%; padding:.75rem .85rem; border:1px solid var(--border); border-radius:.65rem;
      background:transparent; color:inherit; font:inherit;
    }
    .form-control:focus{ outline:none; border-color:var(--primary) }

    textarea.form-control{ min-height:140px; resize:vertical }

    .btn{ padding:.65rem .95rem; border:none; border-radius:.55rem; cursor:pointer; font-weight:800 }
    .btn.primary{ background:var(--primary); color:#fff }
    .btn.ghost{ background:transparent; border:1px solid var(--border); color:var(--text) }
    .btn.danger{ background:var(--danger); color:#fff }

    .actions{ display:flex; flex-wrap:wrap; gap:.5rem }

    /* ========= RIGHT SIDEBAR (Preview) ========= */
    .preview-card{ background:var(--card); border:1px solid var(--border); border-radius:12px; box-shadow:var(--shadow) }
    .preview-body{ padding:1rem 1.25rem; display:grid; gap:1rem }
    .preview-img{ width:100%; aspect-ratio:16/9; object-fit:cover; border-radius:.75rem; border:1px solid var(--border) }

    .badge{ display:inline-flex; align-items:center; gap:.4rem; padding:.3rem .55rem; border-radius:999px; font-size:.8rem; font-weight:800; border:1px solid transparent; }
    .b-upcoming{ background:rgba(59,130,246,.12); color:#2563eb; border-color:rgba(59,130,246,.35) }
    .b-ongoing{ background:rgba(245,158,11,.12); color:#b45309; border-color:rgba(245,158,11,.35) }
    .b-completed{ background:rgba(16,185,129,.12); color:#047857; border-color:rgba(16,185,129,.35) }
    .b-cancelled{ background:rgba(239,68,68,.12); color:#991b1b; border-color:rgba(239,68,68,.35) }

    /* ========= ALERTS ========= */
    .alert{ padding:1rem; margin:1rem 0; border-radius:12px; border:1px solid var(--border); background:var(--card) }
    .alert-error{ border-color:rgba(239,68,68,.35); color:#991b1b; background:#fee2e2 }
    .alert-success{ border-color:rgba(16,185,129,.35); color:#065f46; background:#d1fae5 }
    body.admin-dark-mode .alert-error{ color:#fecaca; background:#3b0f11; border-color:#7f1d1d }
    body.admin-dark-mode .alert-success{ color:#a7f3d0; background:#052e26; border-color:#065f46 }
  </style>
</head>
<body>
  <!-- HEADER -->
  <header class="admin_header">
    <div class="logo"><img src="{{ asset('assets/images/download.png') }}" alt="Logo"></div>
    <div class="admin_header_right">
      <h1>Events</h1>
      <span class="chip">Edit Event</span>
      <button class="admin_theme_toggle" id="themeToggle" aria-label="Toggle theme"><i class="fas fa-moon"></i></button>
    </div>
  </header>

  <main class="admin_main">
    <!-- SIDEBAR (sticky + scroll) -->
    <aside class="admin_sidebar">
      <nav class="admin_sidebar_nav">
        <ul>
          <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
          <li><a href="{{ route('admin.add') }}"><i class="fas fa-plus-circle"></i> Add</a></li>
          <li><a href="{{ route('admin.users') }}"><i class="fas fa-users"></i> Users</a></li>
          <li><a href="{{ route('admin.partners') }}"><i class="fas fa-handshake"></i> Partners</a></li>
          <li><a href="{{ route('admin.writers') }}"><i class="fas fa-pen-fancy"></i> Writers</a></li>
          <li><a href="{{ route('admin.books') }}"><i class="fas fa-book"></i> Books</a></li>
          <li><a href="{{ route('admin.audiobooks') }}"><i class="fas fa-headphones"></i> Audio Books</a></li>
          <li><a href="{{ route('admin.partnerbooks') }}"><i class="fas fa-book-reader"></i> Partner Books</a></li>
          <li><a href="{{ route('admin.orders') }}"><i class="fas fa-shopping-cart"></i> Orders</a></li>
          <li><a href="{{ route('admin.subscription') }}"><i class="fas fa-star"></i> Subscription</a></li>
          <li><a href="{{ route('admin.events') }}" class="active"><i class="fas fa-calendar-alt"></i> Events</a></li>
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

      {{-- Alerts --}}
      @if (session('error_message'))
        <div class="alert alert-error"><i class="fa-solid fa-triangle-exclamation"></i> {{ session('error_message') }}</div>
      @endif
      @if (session('success_message'))
        <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success_message') }}</div>
      @endif
      @if ($errors->any())
        <div class="alert alert-error">
          <strong>There were some issues with your input:</strong>
          <ul style="margin-top:.5rem; padding-left:1.25rem">
            @foreach($errors->all() as $msg)
              <li>{{ $msg }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="page">
        <!-- LEFT: FORM -->
        <div class="section">
          <div class="section-head">
            <div class="title">Edit Event</div>
            <a href="{{ route('admin.events') }}" class="btn ghost"><i class="fa fa-arrow-left"></i> Back</a>
          </div>
          <div class="pad">
            <form method="POST" action="{{ route('admin.events.update', $event->event_id) }}" enctype="multipart/form-data">
              @csrf
              @method('PUT')

              <div class="form-grid">
                <div class="form-group">
                  <label for="name">Event Name</label>
                  <input id="name" name="name" type="text" class="form-control"
                         value="{{ old('name', $event->name) }}" required>
                </div>

                <div class="form-group">
                  <label for="venue">Venue</label>
                  <input id="venue" name="venue" type="text" class="form-control"
                         value="{{ old('venue', $event->venue) }}" required>
                </div>

                <div class="form-group">
                  <label for="event_date">Event Date & Time</label>
                  <input id="event_date" name="event_date" type="datetime-local" class="form-control"
                         value="{{ old('event_date', \Carbon\Carbon::parse($event->event_date)->format('Y-m-d\TH:i')) }}" required>
                </div>

                <div class="form-group">
                  <label for="status">Status</label>
                  <select id="status" name="status" class="form-control" required>
                    <option value="upcoming"  @selected(old('status', $event->status)==='upcoming')>Upcoming</option>
                    <option value="ongoing"   @selected(old('status', $event->status)==='ongoing')>Ongoing</option>
                    <option value="completed" @selected(old('status', $event->status)==='completed')>Completed</option>
                    <option value="cancelled" @selected(old('status', $event->status)==='cancelled')>Cancelled</option>
                  </select>
                </div>
              </div>

              <div class="form-group" style="margin-top:1rem">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" required>{{ old('description', $event->description) }}</textarea>
              </div>

              <div class="form-grid" style="margin-top:1rem">
                <div class="form-group">
                  <label>Current Banner</label>
                  @php
                    $currentSrc = null;
                    if ($event->banner_url) {
                      $currentSrc = \Illuminate\Support\Str::startsWith($event->banner_url, ['http://','https://','/'])
                        ? $event->banner_url
                        : asset($event->banner_url);
                    }
                  @endphp
                  @if ($currentSrc)
                    <img id="currentBanner" src="{{ $currentSrc }}" alt="Current Banner" class="preview-img">
                  @else
                    <div class="muted">No banner uploaded</div>
                  @endif
                </div>

                <div class="form-group">
                  <label for="banner">Update Banner (optional)</label>
                  <input id="banner" name="banner" type="file" accept="image/*" class="form-control">
                  <small class="muted">JPG, JPEG, PNG, WEBP • max 4MB</small>
                  <img id="livePreview" class="preview-img" style="display:none; margin-top:.75rem" alt="Live Preview">
                </div>
              </div>

              <div class="actions" style="margin-top:1rem">
                <a href="{{ route('admin.events') }}" class="btn ghost"><i class="fa fa-times"></i> Cancel</a>
                <button type="submit" class="btn primary"><i class="fa fa-save"></i> Update Event</button>
              </div>
            </form>
          </div>
        </div>

        <!-- RIGHT: PREVIEW / META -->
        <aside class="preview-card">
          <div class="section-head">
            <div class="title">Quick Preview</div>
            @php
              $badgeClass = match($event->status){
                'upcoming'  => 'b-upcoming',
                'ongoing'   => 'b-ongoing',
                'completed' => 'b-completed',
                'cancelled' => 'b-cancelled',
                default     => 'b-upcoming',
              };
            @endphp
            <span class="badge {{ $badgeClass }}"><i class="fa fa-circle"></i> {{ ucfirst($event->status) }}</span>
          </div>
          <div class="preview-body">
            <img id="sidePreview" class="preview-img"
                 src="{{ $currentSrc ?? asset('images/default-event.jpg') }}"
                 alt="Event Banner">
            <div style="display:grid; gap:.35rem">
              <div class="muted">Name</div>
              <div id="pvName" style="font-weight:800">{{ old('name',$event->name) }}</div>
              <div class="muted" style="margin-top:.5rem">Venue</div>
              <div id="pvVenue">{{ old('venue',$event->venue) }}</div>
              <div class="muted" style="margin-top:.5rem">Date</div>
              <div id="pvDate">
                {{ \Carbon\Carbon::parse(old('event_date', $event->event_date))->format('M j, Y g:i a') }}
              </div>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </main>

  <script>
    // Theme toggle
    const themeToggle = document.getElementById('themeToggle');
    const icon = themeToggle?.querySelector('i');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const savedTheme = localStorage.getItem('admin-theme');
    if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
      document.body.classList.add('admin-dark-mode');
      icon?.classList.replace('fa-moon','fa-sun');
    }
    themeToggle?.addEventListener('click', () => {
      document.body.classList.toggle('admin-dark-mode');
      if (document.body.classList.contains('admin-dark-mode')) {
        localStorage.setItem('admin-theme','dark'); icon?.classList.replace('fa-moon','fa-sun');
      } else {
        localStorage.setItem('admin-theme','light'); icon?.classList.replace('fa-sun','fa-moon');
      }
    });

    // Live banner preview (left & right)
    const fileInput = document.getElementById('banner');
    const leftPreview = document.getElementById('livePreview');
    const sidePreview = document.getElementById('sidePreview');
    fileInput?.addEventListener('change', e => {
      const file = e.target.files?.[0];
      if (!file) { leftPreview.style.display = 'none'; return; }
      const url = URL.createObjectURL(file);
      leftPreview.src = url; leftPreview.style.display = 'block';
      sidePreview.src = url;
    });

    // Sync quick preview with form fields
    const nameEl = document.getElementById('name');
    const venueEl = document.getElementById('venue');
    const dateEl = document.getElementById('event_date');
    const pvName = document.getElementById('pvName');
    const pvVenue = document.getElementById('pvVenue');
    const pvDate = document.getElementById('pvDate');

    nameEl?.addEventListener('input', ()=> pvName.textContent = nameEl.value || '—');
    venueEl?.addEventListener('input',()=> pvVenue.textContent = venueEl.value || '—');
    dateEl?.addEventListener('change',()=>{
      if (!dateEl.value) { pvDate.textContent = '—'; return; }
      try {
        const d = new Date(dateEl.value);
        pvDate.textContent = d.toLocaleString();
      } catch { pvDate.textContent = dateEl.value; }
    });
  </script>
</body>
</html>
