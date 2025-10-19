<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard – Events</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

  <style>
    /* ========= SAME THEME TOKENS AS REPORTS ========= */
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
    a{color:inherit;text-decoration:none}

    /* ========= HEADER (reports-like) ========= */
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
    .chip{ font-size:.75rem; padding:.25rem .5rem; border-radius:999px; background:var(--chip); color:var(--text-muted) }

    /* ========= LAYOUT / SIDEBAR (sticky + scroll) ========= */
    .admin_main{ display:grid; grid-template-columns:260px 1fr; min-height:calc(100vh - 64px) }
    .admin_sidebar{
      background:#111827; color:#cbd5e1; border-right:1px solid rgba(255,255,255,.06);
      position:sticky; top:64px; align-self:start; height:calc(100vh - 64px); overflow-y:auto;
    }
    .admin_sidebar_nav ul{ list-style:none; padding:.75rem }
    .admin_sidebar_nav a, .admin_sidebar_nav button.linklike{
      display:flex; align-items:center; gap:.75rem; padding:.65rem .75rem; margin-bottom:.25rem;
      text-decoration:none; color:inherit; border-radius:.5rem; border:none; background:transparent; width:100%; text-align:left; cursor:pointer;
    }
    .admin_sidebar_nav a:hover, .admin_sidebar_nav button.linklike:hover{ background:rgba(255,255,255,.06) }
    .admin_sidebar_nav a.active{ background:rgba(59,130,246,.18); color:#fff }

    .admin_main_content{ padding:1.5rem }

    /* ========= SECTIONS / CARDS ========= */
    .section{ background:var(--card); border:1px solid var(--border); border-radius:12px; box-shadow:var(--shadow); margin-bottom:1rem }
    .section-head{
      padding:1rem 1.25rem; border-bottom:1px solid var(--border);
      display:flex; align-items:center; justify-content:space-between; gap:1rem;
    }
    .title{ font-weight:800; font-size:1.1rem }
    .muted{ color:var(--text-muted) }
    .pad{ padding:1.25rem }

    /* ========= STATS ========= */
    .grid{ display:grid; gap:1rem }
    .grid.stats{ grid-template-columns:repeat(4,minmax(0,1fr)) }
    @media (max-width:1100px){ .grid.stats{ grid-template-columns:repeat(2,minmax(0,1fr)) } }
    @media (max-width:900px){ .admin_main{ grid-template-columns:1fr } .admin_sidebar{ display:none } }

    .stat{
      background:var(--card); border:1px solid var(--border); border-radius:12px; padding:1rem 1.25rem;
      display:grid; gap:.25rem; box-shadow:var(--shadow); position:relative; font-weight:700;
    }
    .stat .k{ font-size:.85rem; color:var(--text-muted); font-weight:800 }
    .stat .v{ font-size:1.85rem }
    .stat.primary   { border-left:4px solid var(--primary) }
    .stat.success   { border-left:4px solid var(--success) }
    .stat.warning   { border-left:4px solid var(--warning) }
    .stat.danger    { border-left:4px solid var(--danger) }

    /* ========= TOOLBAR ========= */
    .toolbar{ display:flex; flex-wrap:wrap; gap:.6rem }
    .control{
      display:flex; align-items:center; gap:.5rem;
      background:var(--bg); border:1px solid var(--border); border-radius:.65rem; padding:.55rem .7rem;
    }
    .control input, .control select{ border:none; outline:none; background:transparent; color:var(--text); font:inherit }
    .btn{ padding:.55rem .9rem; border:none; border-radius:.55rem; cursor:pointer; font-weight:800 }
    .btn.primary{ background:var(--primary); color:#fff }
    .btn.ghost{ background:transparent; border:1px solid var(--border); color:var(--text) }

    /* ========= TABLE ========= */
    table{ width:100%; border-collapse:collapse }
    thead th{ background:var(--table-header); color:var(--text-muted); font-weight:800; text-align:left }
    th, td{ padding:.9rem 1rem; border-bottom:1px solid var(--border); vertical-align:middle }
    tbody tr:hover{ background:var(--table-hover) }
    .event-image{ width:88px; height:64px; object-fit:cover; border-radius:6px; border:1px solid var(--border) }

    .tag{ padding:.15rem .5rem; border-radius:.5rem; font-size:.75rem; background:var(--chip); color:var(--text-muted); font-weight:700 }

    .actions{ display:flex; gap:.5rem; flex-wrap:wrap }
    .action{ padding:.45rem .75rem; border-radius:.5rem; border:1px solid var(--border); background:transparent; cursor:pointer; font-weight:800 }
    .action.edit{ border-color:rgba(59,130,246,.35); color:var(--primary) }
    .action.del { border-color:rgba(239,68,68,.35); color:var(--danger) }
    .action:hover{ filter:brightness(.95) }

    /* ========= ALERTS ========= */
    .alert{ padding:1rem; margin:1rem 0; border-radius:12px; border:1px solid var(--border); background:var(--card) }
    .alert-error{ border-color:rgba(239,68,68,.35); color:#991b1b; background:#fee2e2 }
    .alert-success{ border-color:rgba(16,185,129,.35); color:#065f46; background:#d1fae5 }
    body.admin-dark-mode .alert-error{ color:#fecaca; background:#3b0f11; border-color:#7f1d1d }
    body.admin-dark-mode .alert-success{ color:#a7f3d0; background:#052e26; border-color:#065f46 }

    /* ========= RESPONSIVE ========= */
    @media (max-width:560px){
      .kv{ grid-template-columns:1fr }
    }
  </style>
</head>
<body>
  <!-- HEADER -->
  <header class="admin_header">
    <div class="logo"><img src="{{ asset('assets/images/download.png') }}" alt="Logo"></div>
    <div class="admin_header_right">
      <h1>Events</h1>
      <span class="chip">Manage & review</span>
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
      <div class="section">
        <div class="section-head">
          <div class="title">Events Management</div>
          <div class="muted">Live snapshot</div>
        </div>
        <div class="pad">
          <div class="grid stats">
            <div class="stat primary">
              <div class="k">Total Events</div>
              <div class="v">{{ number_format($total_events) }}</div>
            </div>
            <div class="stat success">
              <div class="k">Upcoming</div>
              <div class="v">{{ number_format($upcoming_count) }}</div>
            </div>
            <div class="stat">
              <div class="k">Completed</div>
              <div class="v">{{ number_format($finished_count) }}</div>
            </div>
            <div class="stat danger">
              <div class="k">Canceled</div>
              <div class="v">{{ number_format($canceled_count) }}</div>
            </div>
          </div>
        </div>
      </div>

      {{-- ALERTS --}}
      @if (session('error_message'))
        <div class="alert alert-error"><i class="fa-solid fa-triangle-exclamation"></i> {{ session('error_message') }}</div>
      @endif
      @if (session('success_message'))
        <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success_message') }}</div>
      @endif

      <!-- TOOLBAR -->
      <div class="section">
        <div class="section-head">
          <div class="title">Filter & Search</div>
          <div class="muted">Find events fast</div>
        </div>
        <div class="pad">
          <div class="toolbar">
            <div class="control">
              <i class="fa-solid fa-magnifying-glass"></i>
              <input id="searchInput" type="text" placeholder="Search event name or venue…">
            </div>
            <div class="control">
              <i class="fa-solid fa-filter"></i>
              <select id="statusFilter">
                <option value="all">All statuses</option>
                <option value="upcoming">Upcoming</option>
                <option value="completed">Completed</option>
                <option value="canceled">Canceled</option>
              </select>
            </div>
            <div class="control">
              <i class="fa-regular fa-calendar"></i>
              <input id="fromDate" type="date" title="From date">
            </div>
            <div class="control">
              <i class="fa-regular fa-calendar"></i>
              <input id="toDate" type="date" title="To date">
            </div>
            <!-- Apply/Reset kept for convenience, but filtering auto-runs on input -->
            <button class="btn primary" id="applyBtn"><i class="fa-solid fa-filter"></i> Apply</button>
            <button class="btn ghost" id="resetBtn"><i class="fa-solid fa-rotate"></i> Reset</button>
          </div>
        </div>
      </div>

      <!-- UPCOMING -->
      <div class="section">
        <div class="section-head">
          <div class="title"><i class="fas fa-calendar-check"></i> Upcoming Events</div>
          <span class="muted">Soon happening</span>
        </div>
        <div class="pad" style="overflow-x:auto">
          @if(count($upcoming_events))
            <table>
              <thead>
              <tr>
                <th>Image</th><th>Name</th><th>Date</th><th>Venue</th><th>Status</th><th>Actions</th>
              </tr>
              </thead>
              <tbody id="upcomingTable">
              @foreach($upcoming_events as $e)
                @php
                  $src = $e->banner_url ? asset('storage/'.$e->banner_url) : asset('images/default-event.jpg');
                  $dateIso = \Carbon\Carbon::parse($e->event_date)->toDateString();
                @endphp
                <tr data-status="upcoming"
                    data-date="{{ $dateIso }}"
                    data-search="{{ Str::lower($e->name.' '.$e->venue) }}">
                  <td><img src="{{ $src }}" alt="{{ $e->name }}" class="event-image"></td>
                  <td>{{ $e->name }}</td>
                  <td>{{ \Carbon\Carbon::parse($e->event_date)->format('M j, Y H:i') }}</td>
                  <td>{{ $e->venue }}</td>
                  <td><span class="tag">Upcoming</span></td>
                  <td class="actions">
                    <a href="{{ route('admin.events.edit', $e->event_id) }}" class="action edit"><i class="fas fa-edit"></i> Edit</a>
                    <form action="{{ route('admin.events.destroy', $e->event_id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this event?');">
                      @csrf @method('DELETE')
                      <button type="submit" class="action del"><i class="fas fa-trash"></i> Delete</button>
                    </form>
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>
          @else
            <div class="muted">No upcoming events found.</div>
          @endif
        </div>
      </div>

      <!-- COMPLETED -->
      <div class="section">
        <div class="section-head">
          <div class="title"><i class="fas fa-check-circle"></i> Completed Events</div>
          <span class="muted">Finished</span>
        </div>
        <div class="pad" style="overflow-x:auto">
          @if(count($finished_events))
            <table>
              <thead>
              <tr>
                <th>Image</th><th>Name</th><th>Date</th><th>Venue</th><th>Status</th><th>Actions</th>
              </tr>
              </thead>
              <tbody id="completedTable">
              @foreach($finished_events as $e)
                @php
                  $src = $e->banner_url ? asset('storage/'.$e->banner_url) : asset('images/default-event.jpg');
                  $dateIso = \Carbon\Carbon::parse($e->event_date)->toDateString();
                @endphp
                <tr data-status="completed"
                    data-date="{{ $dateIso }}"
                    data-search="{{ Str::lower($e->name.' '.$e->venue) }}">
                  <td><img src="{{ $src }}" alt="{{ $e->name }}" class="event-image"></td>
                  <td>{{ $e->name }}</td>
                  <td>{{ \Carbon\Carbon::parse($e->event_date)->format('M j, Y H:i') }}</td>
                  <td>{{ $e->venue }}</td>
                  <td><span class="tag">Completed</span></td>
                  <td class="actions">
                    <a href="{{ route('admin.events.edit', $e->event_id) }}" class="action edit"><i class="fas fa-edit"></i> Edit</a>
                    <form action="{{ route('admin.events.destroy', $e->event_id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this event?');">
                      @csrf @method('DELETE')
                      <button type="submit" class="action del"><i class="fas fa-trash"></i> Delete</button>
                    </form>
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>
          @else
            <div class="muted">No completed events found.</div>
          @endif
        </div>
      </div>

      <!-- CANCELED -->
      <div class="section">
        <div class="section-head">
          <div class="title"><i class="fas fa-times-circle"></i> Canceled Events</div>
          <span class="muted">No longer happening</span>
        </div>
        <div class="pad" style="overflow-x:auto">
          @if(count($canceled_events))
            <table>
              <thead>
              <tr>
                <th>Image</th><th>Name</th><th>Date</th><th>Venue</th><th>Status</th><th>Actions</th>
              </tr>
              </thead>
              <tbody id="canceledTable">
              @foreach($canceled_events as $e)
                @php
                  $src = $e->banner_url ? asset('storage/'.$e->banner_url) : asset('images/default-event.jpg');
                  $dateIso = \Carbon\Carbon::parse($e->event_date)->toDateString();
                @endphp
                <tr data-status="canceled"
                    data-date="{{ $dateIso }}"
                    data-search="{{ Str::lower($e->name.' '.$e->venue) }}">
                  <td><img src="{{ $src }}" alt="{{ $e->name }}" class="event-image"></td>
                  <td>{{ $e->name }}</td>
                  <td>{{ \Carbon\Carbon::parse($e->event_date)->format('M j, Y H:i') }}</td>
                  <td>{{ $e->venue }}</td>
                  <td><span class="tag">Canceled</span></td>
                  <td class="actions">
                    <a href="{{ route('admin.events.edit', $e->event_id) }}" class="action edit"><i class="fas fa-edit"></i> Edit</a>
                    <form action="{{ route('admin.events.destroy', $e->event_id) }}" method="POST" style="display:inline" onsubmit="return confirm('Delete this event?');">
                      @csrf @method('DELETE')
                      <button type="submit" class="action del"><i class="fas fa-trash"></i> Delete</button>
                    </form>
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>
          @else
            <div class="muted">No canceled events found.</div>
          @endif
        </div>
      </div>

    </div>
  </main>

  <script>
    // Theme toggle (same as Reports)
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

    // ----- Client-side Search + Filters (auto-apply) -----
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const fromDate = document.getElementById('fromDate');
    const toDate = document.getElementById('toDate');
    const applyBtn = document.getElementById('applyBtn');
    const resetBtn = document.getElementById('resetBtn');

    function rowMatchesFilters(tr, term, status, from, to) {
      const hay = (tr.getAttribute('data-search') || '').toLowerCase();
      const st  = tr.getAttribute('data-status') || '';
      const dt  = tr.getAttribute('data-date') || ''; // yyyy-mm-dd
      if (term && !hay.includes(term)) return false;
      if (status !== 'all' && st !== status) return false;
      if (from && dt && dt < from) return false;
      if (to && dt && dt > to) return false;
      return true;
    }

    function applyFilters() {
      const term = (searchInput?.value || '').toLowerCase().trim();
      const status = statusFilter?.value || 'all';
      const from = (fromDate?.value || '');
      const to   = (toDate?.value || '');

      document.querySelectorAll('tbody tr').forEach(tr => {
        tr.style.display = rowMatchesFilters(tr, term, status, from, to) ? '' : 'none';
      });
    }
    function resetFilters() {
      if (searchInput) searchInput.value = '';
      if (statusFilter) statusFilter.value = 'all';
      if (fromDate) fromDate.value = '';
      if (toDate) toDate.value = '';
      applyFilters();
    }

    // Auto-apply on every change
    searchInput?.addEventListener('input', applyFilters);
    statusFilter?.addEventListener('change', applyFilters);
    fromDate?.addEventListener('change', applyFilters);
    toDate?.addEventListener('change', applyFilters);

    // Buttons (optional)
    applyBtn?.addEventListener('click', applyFilters);
    resetBtn?.addEventListener('click', resetFilters);
  </script>
</body>
</html>
