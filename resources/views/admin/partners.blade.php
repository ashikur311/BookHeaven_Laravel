<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Partners — Admin Dashboard</title>
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

    /* ========= HEADER (identical palette) ========= */
    .admin_header{
      display:flex; justify-content:space-between; align-items:center;
      padding:1rem 1.5rem; background:#1f2937; color:#fff;
      position:sticky; top:0; z-index:30; border-bottom:1px solid rgba(255,255,255,.06);
    }
    .logo img{height:40px}
    .admin_header_right{display:flex; align-items:center; gap:.75rem}
    .admin_header_right h1{font-size:1.1rem; font-weight:600}
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

    /* ========= CARDS / SECTIONS ========= */
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
    .grid.stats{ grid-template-columns:repeat(4,minmax(0,1fr)) }
    @media (max-width:1100px){ .grid.stats{ grid-template-columns:repeat(2,minmax(0,1fr)) } }
    @media (max-width:900px){ .admin_main{ grid-template-columns:1fr } .admin_sidebar{ display:none } }
    .stat{
      background:var(--card); border:1px solid var(--border); border-radius:12px; padding:1rem 1.25rem;
      display:grid; gap:.25rem;
    }
    .stat .k{ font-size:.85rem; color:var(--text-muted) }
    .stat .v{ font-size:1.75rem; font-weight:800 }

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
    .user-avatar{ width:42px; height:42px; border-radius:999px; object-fit:cover }

    .tag{ padding:.15rem .5rem; border-radius:.5rem; font-size:.75rem; background:var(--chip); color:var(--text-muted) }

    /* ========= ACTIONS ========= */
    .actions{ display:flex; gap:.5rem; flex-wrap:wrap }
    .action{ padding:.45rem .75rem; border-radius:.5rem; border:1px solid var(--border); background:transparent; cursor:pointer; font-weight:700 }
    .action.view{ border-color:rgba(59,130,246,.35); color:var(--primary) }
    .action.delete{ border-color:rgba(239,68,68,.35); color:var(--danger) }
    .action:hover{ filter:brightness(.95) }

    /* ========= MODAL ========= */
    .admin_modal{ display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:60; align-items:center; justify-content:center; padding:1rem }
    .admin_modal_content{
      background:var(--card); color:var(--text); border:1px solid var(--border); border-radius:12px;
      width:min(720px, 96vw); max-height:85vh; overflow:auto; box-shadow:0 10px 30px rgba(0,0,0,.25); padding:1.25rem;
    }
    .admin_modal_close{ position:sticky; top:0; float:right; font-size:1.4rem; cursor:pointer; color:var(--text-muted) }
    .details{ display:grid; gap:1rem; margin-top:.5rem }
    .flex{ display:flex; gap:1rem; align-items:center }
    .avatar-xl{ width:112px; height:112px; border-radius:16px; object-fit:cover; border:1px solid var(--border) }
    .kv{ display:grid; grid-template-columns:160px 1fr; gap:.5rem 1rem; align-items:center }
    .kv b{ color:var(--text-muted) }

    /* ========= RESPONSIVE ========= */
    @media (max-width:520px){
      .kv{ grid-template-columns:1fr }
      .avatar-xl{ width:96px; height:96px }
    }
  </style>
</head>
<body>
  <!-- HEADER -->
  <header class="admin_header">
    <div class="logo"><img src="{{ asset('assets/images/download.png') }}" alt="Logo"></div>
    <div class="admin_header_right">
      <h1>Partners</h1>
      <span class="chip">Manage & review</span>
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
          <li><a href="{{ route('admin.partners') }}" class="active"><i class="fas fa-handshake"></i> Partners</a></li>
          <li><a href="{{ route('admin.writers') }}"><i class="fas fa-pen-fancy"></i> Writers</a></li>
          <li><a href="{{ route('admin.books') }}"><i class="fas fa-book"></i> Books</a></li>
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

      {{-- ALERTS --}}
      @if(session('error'))   <div class="alert alert-error">{{ session('error') }}</div>   @endif
      @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

      <!-- STATS -->
      <div class="section">
        <div class="section-head">
          <div class="title">Partner Statistics</div>
          <span class="muted">Live snapshot</span>
        </div>
        <div class="pad">
          <div class="grid stats">
            <div class="stat"><div class="k">Total Partners</div><div class="v">{{ number_format($stats['total_partners']) }}</div></div>
            <div class="stat"><div class="k">New This Month</div><div class="v">{{ number_format($stats['new_this_month']) }}</div></div>
            <div class="stat"><div class="k">Pending</div><div class="v">{{ number_format($stats['pending_partners']) }}</div></div>
            <div class="stat"><div class="k">Approved</div><div class="v">{{ number_format($stats['approved_partners']) }}</div></div>
          </div>
        </div>
      </div>

      <!-- TOOLBAR -->
      <div class="section">
        <div class="section-head">
          <div class="title">Filter & Search</div>
          <span class="muted">Find partners fast</span>
        </div>
        <div class="pad">
          <div class="toolbar">
            <div class="control"><i class="fa-solid fa-magnifying-glass"></i><input id="searchInput" type="text" placeholder="Search username or email…"></div>
            <div class="control">
              <i class="fa-solid fa-filter"></i>
              <select id="statusFilter">
                <option value="all">All</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
              </select>
            </div>
            <button class="btn ghost" id="resetFilters"><i class="fa-solid fa-rotate"></i> Reset</button>
          </div>
        </div>
      </div>

      <!-- PENDING -->
      <div class="section">
        <div class="section-head">
          <div class="title">Pending Partners</div>
          <span class="muted">Awaiting approval</span>
        </div>
        <div class="pad" style="overflow-x:auto">
          <table id="pendingTable">
            <thead>
              <tr>
                <th>ID</th><th>Image</th><th>Username</th><th>Email</th><th>Books</th><th>Joined</th><th>Status</th><th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($pendingPartners as $p)
                @php $avatar = $p->user_profile ?: 'assets/images/default-avatar.jpg'; @endphp
                <tr data-status="pending"
                    data-search="{{ Str::lower($p->username.' '.$p->email) }}"
                    data-phone="{{ e($p->phone ?? 'N/A') }}"
                    data-address="{{ e($p->address ?? 'N/A') }}">
                  <td>#{{ $p->partner_id }}</td>
                  <td>
                    <img class="user-avatar"
                         src="{{ $p->user_profile ? asset($avatar) : 'https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?auto=format&fit=crop&w=100&q=80' }}"
                         alt="{{ $p->username }}">
                  </td>
                  <td>{{ $p->username }}</td>
                  <td>{{ $p->email }}</td>
                  <td>{{ $p->book_count }}</td>
                  <td>{{ \Carbon\Carbon::parse($p->joined_at)->format('Y-m-d') }}</td>
                  <td><span class="tag">Pending</span></td>
                  <td>
                    <div class="actions">
                      <button class="action view" onclick="showPartnerDetails({{ $p->partner_id }}, 'pending', this)">View</button>
                      <form method="POST" action="{{ route('admin.partners.delete') }}" onsubmit="return confirm('Delete this partner?');">
                        @csrf
                        <input type="hidden" name="partner_id" value="{{ $p->partner_id }}">
                        <button type="submit" class="action delete">Delete</button>
                      </form>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      <!-- APPROVED -->
      <div class="section">
        <div class="section-head">
          <div class="title">Approved Partners</div>
          <span class="muted">Active collaborators</span>
        </div>
        <div class="pad" style="overflow-x:auto">
          <table id="approvedTable">
            <thead>
              <tr>
                <th>ID</th><th>Image</th><th>Username</th><th>Email</th><th>Books</th><th>Joined</th><th>Status</th><th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($approvedPartners as $p)
                @php $avatar = $p->user_profile ?: 'assets/images/default-avatar.jpg'; @endphp
                <tr data-status="approved"
                    data-search="{{ Str::lower($p->username.' '.$p->email) }}"
                    data-phone="{{ e($p->phone ?? 'N/A') }}"
                    data-address="{{ e($p->address ?? 'N/A') }}">
                  <td>#{{ $p->partner_id }}</td>
                  <td>
                    <img class="user-avatar"
                         src="{{ $p->user_profile ? asset($avatar) : 'https://images.unsplash.com/photo-1570295999919-56ceb5ecca61?auto=format&fit=crop&w=100&q=80' }}"
                         alt="{{ $p->username }}">
                  </td>
                  <td>{{ $p->username }}</td>
                  <td>{{ $p->email }}</td>
                  <td>{{ $p->book_count }}</td>
                  <td>{{ \Carbon\Carbon::parse($p->joined_at)->format('Y-m-d') }}</td>
                  <td><span class="tag">Approved</span></td>
                  <td>
                    <div class="actions">
                      <button class="action view" onclick="showPartnerDetails({{ $p->partner_id }}, 'approved', this)">View</button>
                      <form method="POST" action="{{ route('admin.partners.delete') }}" onsubmit="return confirm('Delete this partner?');">
                        @csrf
                        <input type="hidden" name="partner_id" value="{{ $p->partner_id }}">
                        <button type="submit" class="action delete">Delete</button>
                      </form>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      <!-- MODAL -->
      <div id="partnerDetailsModal" class="admin_modal" role="dialog" aria-modal="true" aria-labelledby="partnerModalTitle">
        <div class="admin_modal_content">
          <span class="admin_modal_close" onclick="closeModal()" aria-label="Close">&times;</span>
          <h2 id="partnerModalTitle" class="title">Partner Details</h2>
          <div class="details">
            <div class="flex">
              <img id="modalUserImage" class="avatar-xl" alt="Partner">
              <div>
                <div class="chip" id="modalStatusChip">Status</div>
                <div class="muted" id="modalJoinedDate"></div>
              </div>
            </div>
            <div class="kv">
              <b>ID</b>        <span id="modalPartnerId"></span>
              <b>Username</b>  <span id="modalUsername"></span>
              <b>Email</b>     <span id="modalEmail"></span>
              <b>Phone</b>     <span id="modalPhone"></span>
              <b>Address</b>   <span id="modalAddress"></span>
              <b>Books</b>     <span id="modalBookCount"></span>
            </div>

            <form id="approveForm" method="POST" action="{{ route('admin.partners.approve') }}" style="display:none; margin-top:.5rem">
              @csrf
              <input type="hidden" name="partner_id" id="approvePartnerId">
              <button type="submit" class="btn primary"><i class="fa-solid fa-badge-check"></i> Approve Partner</button>
            </form>
          </div>
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

    // Search & Filter (client-side)
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const resetFilters = document.getElementById('resetFilters');

    function filterTables() {
      const term = (searchInput?.value || '').toLowerCase().trim();
      const status = statusFilter?.value || 'all';
      document.querySelectorAll('tbody tr').forEach(tr => {
        const haystack = (tr.getAttribute('data-search') || '').toLowerCase();
        const rowStatus = tr.getAttribute('data-status') || 'pending';
        const matchText = !term || haystack.includes(term);
        const matchStatus = status === 'all' || rowStatus === status;
        tr.style.display = (matchText && matchStatus) ? '' : 'none';
      });
    }
    searchInput?.addEventListener('input', filterTables);
    statusFilter?.addEventListener('change', filterTables);
    resetFilters?.addEventListener('click', () => { if (searchInput) searchInput.value=''; if (statusFilter) statusFilter.value='all'; filterTables(); });

    // Modal helpers
    function showPartnerDetails(id, status, btn){
      // Find the row in either table
      const row = [...document.querySelectorAll('tbody tr')].find(r => r.cells?.[0]?.textContent === `#${id}`);
      if (!row) return;
      const img = row.querySelector('img')?.src || '';
      const cells = row.cells;

      document.getElementById('modalPartnerId').textContent = cells[0].textContent;
      document.getElementById('modalUsername').textContent  = cells[2].textContent;
      document.getElementById('modalEmail').textContent     = cells[3].textContent;
      document.getElementById('modalBookCount').textContent = cells[4].textContent;
      document.getElementById('modalJoinedDate').textContent= `Joined ${cells[5].textContent}`;
      document.getElementById('modalUserImage').src         = img;
      document.getElementById('modalPhone').textContent     = row.dataset.phone || 'N/A';
      document.getElementById('modalAddress').textContent   = row.dataset.address || 'N/A';

      const chip = document.getElementById('modalStatusChip');
      chip.textContent = status.charAt(0).toUpperCase()+status.slice(1);

      const approveForm = document.getElementById('approveForm');
      if (status === 'pending') {
        document.getElementById('approvePartnerId').value = id;
        approveForm.style.display = 'block';
      } else {
        approveForm.style.display = 'none';
      }

      document.getElementById('partnerDetailsModal').style.display = 'flex';
    }
    function closeModal(){ document.getElementById('partnerDetailsModal').style.display = 'none'; }
    window.addEventListener('click', e => { if (e.target.classList?.contains('admin_modal')) closeModal(); });
  </script>
</body>
</html>
