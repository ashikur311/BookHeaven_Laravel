<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Partner Books — Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

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

    /* ========= HEADER (reports palette) ========= */
    .admin_header{
      display:flex; justify-content:space-between; align-items:center;
      padding:1rem 1.5rem; background:#1f2937; color:#fff;
      position:sticky; top:0; z-index:30; border-bottom:1px solid rgba(255,255,255,.06);
    }
    .logo img{height:40px}
    .admin_header_right{display:flex; align-items:center; gap:.75rem}
    .admin_header_right h1{font-size:1.1rem; font-weight:700}
    .chip{ font-size:.75rem; padding:.25rem .5rem; border-radius:999px; background:var(--chip); color:var(--text-muted) }
    .admin_theme_toggle{
      background:transparent; border:1px solid rgba(255,255,255,.2); color:#fff;
      padding:.5rem .65rem; border-radius:.5rem; cursor:pointer;
    }

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

    /* ========= SECTION / CARD ========= */
    .section{ background:var(--card); border:1px solid var(--border); border-radius:12px; box-shadow:var(--shadow); margin-bottom:1rem }
    .section-head{
      padding:1rem 1.25rem; border-bottom:1px solid var(--border);
      display:flex; align-items:center; justify-content:space-between; gap:1rem;
    }
    .title{ font-weight:800; font-size:1.05rem }
    .muted{ color:var(--text-muted) }
    .pad{ padding:1.25rem }

    /* ========= STATS ========= */
    .grid{ display:grid; gap:1rem }
    .grid.stats{ grid-template-columns:repeat(5,minmax(0,1fr)) }
    @media (max-width:1200px){ .grid.stats{ grid-template-columns:repeat(3,minmax(0,1fr)) } }
    @media (max-width:900px){ .admin_main{ grid-template-columns:1fr } .admin_sidebar{ display:none } .grid.stats{ grid-template-columns:repeat(2,minmax(0,1fr)) } }
    @media (max-width:520px){ .grid.stats{ grid-template-columns:1fr } }

    .stat{
      background:var(--card); border:1px solid var(--border); border-radius:12px; padding:1rem 1.25rem;
      display:grid; gap:.25rem;
    }
    .stat .k{ font-size:.85rem; color:var(--text-muted) }
    .stat .v{ font-size:1.75rem; font-weight:800 }
    .pill{ font-size:.7rem; padding:.2rem .45rem; border-radius:.5rem; color:#fff }
    .pill.pending{ background:var(--warning) }
    .pill.visible{ background:var(--success) }
    .pill.rent{ background:var(--primary) }
    .pill.return{ background:var(--danger) }

    /* ========= TOOLBAR ========= */
    .toolbar{ display:flex; flex-wrap:wrap; gap:.6rem }
    .control{
      display:flex; align-items:center; gap:.5rem;
      background:var(--bg); border:1px solid var(--border); border-radius:.65rem; padding:.55rem .7rem;
    }
    .control input, .control select{ border:none; outline:none; background:transparent; color:var(--text); font:inherit }
    .btn{ padding:.55rem .9rem; border:none; border-radius:.55rem; cursor:pointer; font-weight:700 }
    .btn.primary{ background:var(--primary); color:#fff }
    .btn.ghost{ background:transparent; border:1px solid var(--border); color:var(--text) }

    /* ========= TABLES ========= */
    table{ width:100%; border-collapse:collapse }
    thead th{ background:var(--table-header); color:var(--text-muted); font-weight:800; text-align:left }
    th, td{ padding:.9rem 1rem; border-bottom:1px solid var(--border) }
    tbody tr:hover{ background:var(--table-hover) }
    .book-cover{ width:56px; height:76px; object-fit:cover; border-radius:.35rem; border:1px solid var(--border) }

    .tag{ padding:.25rem .5rem; border-radius:.65rem; font-size:.75rem; font-weight:700; color:#fff; text-transform:capitalize }
    .tag.pending{ background:var(--warning) }
    .tag.visible{ background:var(--success) }
    .tag.on-rent{ background:var(--primary) }
    .tag.return-apply{ background:var(--danger) }

    .actions{ display:flex; gap:.5rem; flex-wrap:wrap }
    .action{ padding:.45rem .75rem; border-radius:.5rem; border:1px solid var(--border); background:transparent; cursor:pointer; font-weight:700 }
    .action.approve{ border-color:rgba(16,185,129,.35); color:var(--success) }
    .action.view{ border-color:rgba(59,130,246,.35); color:var(--primary) }
    .action.delete{ border-color:rgba(239,68,68,.35); color:var(--danger) }
    .action:hover{ filter:brightness(.95) }

    /* ========= ALERTS ========= */
    .alert{ padding:1rem; margin:.75rem 0 1rem; border-radius:.65rem; border:1px solid }
    .alert-success{ background:#d1fae5; color:#065f46; border-color:#a7f3d0 }
    .alert-error{ background:#fee2e2; color:#7f1d1d; border-color:#fecaca }
    body.admin-dark-mode .alert-success{ background:#072c22; color:#9ef7d1; border-color:#0d3b2e }
    body.admin-dark-mode .alert-error{ background:#3a1a1d; color:#ffb8c6; border-color:#4d2227 }
  </style>
</head>
<body>
  <!-- HEADER -->
  <header class="admin_header">
    <div class="logo"><img src="{{ asset('assets/images/download.png') }}" alt="Logo"></div>
    <div class="admin_header_right">
      <h1>Partner Books</h1>
      <span class="chip">Unified admin style</span>
      <button class="admin_theme_toggle" id="themeToggle" aria-label="Toggle theme"><i class="fas fa-moon"></i></button>
    </div>
  </header>

  <main class="admin_main">
    <!-- SIDEBAR (sticky + scroll, same palette as Reports) -->
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
          <li><a href="{{ route('admin.partnerbooks') }}" class="active"><i class="fas fa-book-reader"></i> Partner Books</a></li>
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

      {{-- ALERTS --}}
      @if(session('error'))   <div class="alert alert-error">{{ session('error') }}</div> @endif
      @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
      @if(!empty($error_message)) <div class="alert alert-error">{{ $error_message }}</div> @endif

      <!-- STATS -->
      <div class="section">
        <div class="section-head">
          <div class="title">Partner Books Statistics</div>
          <span class="muted">Live snapshot</span>
        </div>
        <div class="pad">
          <div class="grid stats">
            <div class="stat"><div class="k">Total Books</div><div class="v">{{ number_format($stats['total_books']) }}</div></div>
            <div class="stat"><div class="k">Pending</div><div class="v">{{ number_format($stats['pending']) }}</div><span class="pill pending">Awaiting</span></div>
            <div class="stat"><div class="k">Visible</div><div class="v">{{ number_format($stats['visible']) }}</div><span class="pill visible">Live</span></div>
            <div class="stat"><div class="k">On Rent</div><div class="v">{{ number_format($stats['on_rent']) }}</div><span class="pill rent">Active</span></div>
            <div class="stat"><div class="k">Return Apply</div><div class="v">{{ number_format($stats['return_apply']) }}</div><span class="pill return">Action</span></div>
          </div>
        </div>
      </div>

      <!-- FILTERS -->
      <div class="section">
        <div class="section-head">
          <div class="title">Filter & Search</div>
          <span class="muted">Works across all tables</span>
        </div>
        <div class="pad">
          <div class="toolbar">
            <div class="control"><i class="fa-solid fa-magnifying-glass"></i><input id="searchInput" type="text" placeholder="Search title, writer, partner…"></div>
            <div class="control">
              <i class="fa-solid fa-layer-group"></i>
              <select id="statusFilter">
                <option value="all">All statuses</option>
                <option value="pending">Pending</option>
                <option value="visible">Visible</option>
                <option value="on-rent">On Rent</option>
                <option value="return-apply">Return Apply</option>
              </select>
            </div>
            <button class="btn ghost" id="resetFilters"><i class="fa-solid fa-rotate"></i> Reset</button>
          </div>
        </div>
      </div>

      {{-- PENDING --}}
      <div class="section">
        <div class="section-head">
          <div class="title">Pending Approval</div>
          <span class="muted">Newly submitted by partners</span>
        </div>
        <div class="pad" style="overflow-x:auto">
          <table class="filterable">
            <thead>
            <tr>
              <th>Cover</th><th>Title</th><th>Writer</th><th>Genre</th><th>Partner</th><th>Added</th><th>Status</th><th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($pendingBooks as $book)
              <tr data-status="pending" data-search="{{ Str::lower($book->title.' '.$book->writer.' '.$book->partner_name.' '.$book->genre) }}">
                <td><img src="{{ asset($book->poster_url) }}" class="book-cover" alt="cover of {{ $book->title }}"></td>
                <td>{{ $book->title }}</td>
                <td>{{ $book->writer }}</td>
                <td>{{ $book->genre }}</td>
                <td>#{{ $book->partner_id }} ({{ $book->partner_name }})</td>
                <td>{{ \Carbon\Carbon::parse($book->added_at)->format('Y-m-d') }}</td>
                <td><span class="tag pending">{{ $book->status }}</span></td>
                <td class="actions">
                  <form method="POST" action="{{ route('admin.partnerbooks.approve') }}">
                    @csrf
                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                    <button type="submit" class="action approve"><i class="fa-solid fa-check"></i> Approve</button>
                  </form>
                  <form method="POST" action="{{ route('admin.partnerbooks.delete') }}" onsubmit="return confirm('Delete this book?');">
                    @csrf
                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                    <button type="submit" class="action delete"><i class="fa-solid fa-trash"></i> Delete</button>
                  </form>
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>

      {{-- ON RENT --}}
      <div class="section">
        <div class="section-head">
          <div class="title">Books On Rent</div>
          <span class="muted">Currently with users</span>
        </div>
        <div class="pad" style="overflow-x:auto">
          <table class="filterable">
            <thead>
            <tr>
              <th>Cover</th><th>Title</th><th>Writer</th><th>Genre</th><th>Partner</th><th>Rented By</th><th>Added</th><th>Status</th><th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($onRentBooks as $book)
              <tr data-status="on-rent" data-search="{{ Str::lower($book->title.' '.$book->writer.' '.$book->partner_name.' '.$book->renter_name.' '.$book->genre) }}">
                <td><img src="{{ asset($book->poster_url) }}" class="book-cover" alt="cover of {{ $book->title }}"></td>
                <td>{{ $book->title }}</td>
                <td>{{ $book->writer }}</td>
                <td>{{ $book->genre }}</td>
                <td>#{{ $book->partner_id }} ({{ $book->partner_name }})</td>
                <td>#{{ $book->user_id }} ({{ $book->renter_name }})</td>
                <td>{{ \Carbon\Carbon::parse($book->added_at)->format('Y-m-d') }}</td>
                <td><span class="tag on-rent">{{ $book->status }}</span></td>
                <td class="actions">
                  <button class="action view" title="View"><i class="fa-solid fa-eye"></i> View</button>
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>

      {{-- RETURN APPLY --}}
      <div class="section">
        <div class="section-head">
          <div class="title">Return Apply Requests</div>
          <span class="muted">Set an expected return date</span>
        </div>
        <div class="pad" style="overflow-x:auto">
          <table class="filterable">
            <thead>
            <tr>
              <th>Cover</th><th>Title</th><th>Writer</th><th>Genre</th><th>Partner</th><th>Rented By</th><th>Added</th><th>Status</th><th>Set Return</th>
            </tr>
            </thead>
            <tbody>
            @foreach($returnApplyBooks as $book)
              <tr data-status="return-apply" data-search="{{ Str::lower($book->title.' '.$book->writer.' '.$book->partner_name.' '.$book->renter_name.' '.$book->genre) }}">
                <td><img src="{{ asset($book->poster_url) }}" class="book-cover" alt="cover of {{ $book->title }}"></td>
                <td>{{ $book->title }}</td>
                <td>{{ $book->writer }}</td>
                <td>{{ $book->genre }}</td>
                <td>#{{ $book->partner_id }} ({{ $book->partner_name }})</td>
                <td>#{{ $book->user_id }} ({{ $book->renter_name }})</td>
                <td>{{ \Carbon\Carbon::parse($book->added_at)->format('Y-m-d') }}</td>
                <td><span class="tag return-apply">{{ $book->status }}</span></td>
                <td>
                  <form method="POST" action="{{ route('admin.partnerbooks.returnDate') }}" class="actions" style="align-items:center">
                    @csrf
                    <input type="hidden" name="book_id" value="{{ $book->id }}">
                    <input type="date" name="return_date" required
                           style="border:1px solid var(--border); background:var(--card); color:var(--text); padding:.45rem .6rem; border-radius:.5rem">
                    <button type="submit" class="action approve"><i class="fa-solid fa-calendar-check"></i> Set</button>
                  </form>
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </main>

  <script>
    // Theme toggle (consistent with Reports)
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

    // Enforce min date = today
    document.addEventListener('DOMContentLoaded', () => {
      const today = new Date().toISOString().split('T')[0];
      document.querySelectorAll('input[type="date"]').forEach(el => el.min = today);
    });

    // Global search + status filter for all .filterable tables
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const resetFilters = document.getElementById('resetFilters');

    function applyFilters(){
      const term = (searchInput?.value || '').toLowerCase().trim();
      const status = statusFilter?.value || 'all';
      document.querySelectorAll('table.filterable tbody tr').forEach(tr=>{
        const hay = (tr.getAttribute('data-search')||'').toLowerCase();
        const st  = tr.getAttribute('data-status')||'';
        const okText = !term || hay.includes(term);
        const okStatus = status==='all' || st===status;
        tr.style.display = (okText && okStatus) ? '' : 'none';
      });
    }
    searchInput?.addEventListener('input', applyFilters);
    statusFilter?.addEventListener('change', applyFilters);
    resetFilters?.addEventListener('click', ()=>{ if (searchInput) searchInput.value=''; if (statusFilter) statusFilter.value='all'; applyFilters(); });
  </script>
</body>
</html>
