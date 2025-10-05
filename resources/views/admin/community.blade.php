{{-- resources/views/admin/community.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Community Management – Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

  <style>
    /* ========= THEME TOKENS (shared across admin) ========= */
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
      display:grid; gap:.25rem; box-shadow:var(--shadow); font-weight:700; border-left:4px solid var(--primary);
    }
    .stat .k{ font-size:.85rem; color:var(--text-muted); font-weight:800 }
    .stat .v{ font-size:1.85rem }

    /* ========= TABLE ========= */
    table{ width:100%; border-collapse:collapse }
    thead th{ background:var(--table-header); color:var(--text-muted); font-weight:800; text-align:left }
    th, td{ padding:.9rem 1rem; border-bottom:1px solid var(--border); vertical-align:middle }
    tbody tr:hover{ background:var(--table-hover) }
    .cover{ width:72px; height:54px; border-radius:6px; object-fit:cover; border:1px solid var(--border) }
    .creator{ display:flex; align-items:center; gap:.5rem }
    .creator img{ width:32px; height:32px; border-radius:50%; object-fit:cover; border:1px solid var(--border) }

    .status-badge{
      display:inline-flex; align-items:center; gap:.35rem; padding:.25rem .55rem; border-radius:999px; font-size:.8rem; font-weight:800; border:1px solid transparent;
      text-transform:capitalize;
    }
    .status-badge.active   { background:rgba(16,185,129,.12); color:#047857; border-color:rgba(16,185,129,.35) }
    .status-badge.inactive { background:rgba(245,158,11,.12); color:#b45309; border-color:rgba(245,158,11,.35) }
    .status-badge.banned   { background:rgba(239,68,68,.12); color:#991b1b; border-color:rgba(239,68,68,.35) }

    .actions{ display:flex; gap:.5rem; flex-wrap:wrap }
    .action{ padding:.5rem .8rem; border-radius:.55rem; border:1px solid var(--border); background:transparent; cursor:pointer; font-weight:800 }
    .action.view{ color:var(--primary); border-color:rgba(59,130,246,.35) }
    .action.del { color:var(--danger);  border-color:rgba(239,68,68,.35) }
    .action:hover{ filter:brightness(.95) }

    /* ========= MODALS ========= */
    .admin_modal{ display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:60; align-items:center; justify-content:center; padding:1rem }
    .admin_modal_content{
      background:var(--card); color:var(--text); border:1px solid var(--border); border-left:4px solid var(--primary);
      border-radius:12px; width:min(760px, 96vw); max-height:85vh; overflow:auto; box-shadow:0 10px 30px rgba(0,0,0,.25); padding:1.25rem;
    }
    .admin_modal_close{ position:sticky; top:0; float:right; font-size:1.4rem; cursor:pointer; color:var(--text-muted) }

    .details{ display:grid; gap:1rem; margin-top:.25rem }
    .banner{ width:100%; aspect-ratio:16/9; object-fit:cover; border-radius:.75rem; border:1px solid var(--border) }
    .kv{ display:grid; grid-template-columns:180px 1fr; gap:.5rem 1rem; align-items:center }
    @media (max-width:560px){ .kv{ grid-template-columns:1fr } }

    .confirm{ text-align:center }
    .confirm .buttons{ display:flex; gap:.75rem; justify-content:center; margin-top:1rem }
    .btn.primary{ background:var(--primary); color:#fff }
    .btn.ghost{ background:transparent; border:1px solid var(--border); color:var(--text) }
    .btn.danger{ background:var(--danger); color:#fff }

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
      <h1>Community</h1>
      <span class="chip">Manage & review</span>
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
          <li><a href="{{ route('admin.books') }}"><i class="fas fa-book"></i> Books</a></li>
          <li><a href="{{ route('admin.audiobooks') }}"><i class="fas fa-headphones"></i> Audio Books</a></li>
          <li><a href="{{ route('admin.partnerbooks') }}"><i class="fas fa-book-reader"></i> Partner Books</a></li>
          <li><a href="{{ route('admin.orders') }}"><i class="fas fa-shopping-cart"></i> Orders</a></li>
          <li><a href="{{ route('admin.subscription') }}"><i class="fas fa-star"></i> Subscription</a></li>
          <li><a href="{{ route('admin.events') }}"><i class="fas fa-calendar-alt"></i> Events</a></li>
          <li><a href="{{ route('admin.question') }}"><i class="fa-solid fa-question"></i> User Questions</a></li>
          <li><a href="{{ route('admin.community') }}" class="active"><i class="fas fa-users"></i> Community</a></li>
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
      @if (!empty($error_message))
        <div class="alert alert-error"><i class="fa-solid fa-triangle-exclamation"></i> {{ $error_message }}</div>
      @endif
      @if (!empty($success_message))
        <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ $success_message }}</div>
      @endif

      <!-- Stats -->
      <div class="section">
        <div class="section-head">
          <div class="title">Community Statistics</div>
          <span class="muted">Overview</span>
        </div>
        <div class="pad">
          <div class="grid stats">
            <div class="stat"><div class="k">Total Communities</div><div class="v">{{ number_format($stats['total_communities']) }}</div></div>
            <div class="stat"><div class="k">Active</div><div class="v">{{ number_format($stats['active_communities']) }}</div></div>
            <div class="stat"><div class="k">Banned</div><div class="v">{{ number_format($stats['banned_communities']) }}</div></div>
            <div class="stat"><div class="k">Total Members</div><div class="v">{{ number_format($stats['total_members']) }}</div></div>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="section">
        <div class="section-head">
          <div class="title">Community Management</div>
          <span class="muted">List of communities</span>
        </div>
        <div class="pad" style="overflow-x:auto">
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Cover</th>
                <th>Name</th>
                <th>Creator</th>
                <th>Members</th>
                <th>Created</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
            @forelse($communities as $c)
              @php
                $cover = $c->cover_image_url ?: 'assets/images/default-community.jpg';
                $creatorImage = $c->creator_image ? asset($c->creator_image) : asset('assets/images/default-avatar.jpg');
                $statusClass = in_array($c->status, ['active','inactive','banned']) ? $c->status : 'inactive';
              @endphp
              <tr
                data-id="{{ $c->community_id }}"
                data-name="{{ e($c->name) }}"
                data-description="{{ e($c->description ?? 'No description') }}"
                data-privacy="{{ e($c->privacy ?? 'public') }}"
                data-status="{{ e($c->status) }}"
                data-created="{{ \Carbon\Carbon::parse($c->created_at)->format('Y-m-d') }}"
                data-members="{{ (int)$c->member_count }}"
                data-cover="{{ asset($cover) }}"
                data-creator-name="{{ e($c->creator_name) }}"
                data-creator-image="{{ $creatorImage }}"
              >
                <td>#{{ $c->community_id }}</td>
                <td><img class="cover" src="{{ asset($cover) }}" alt="{{ $c->name }}"></td>
                <td>{{ $c->name }}</td>
                <td>
                  <div class="creator">
                    <img src="{{ $creatorImage }}" alt="{{ $c->creator_name }}">
                    <span>{{ $c->creator_name }}</span>
                  </div>
                </td>
                <td>{{ (int)$c->member_count }}</td>
                <td>{{ \Carbon\Carbon::parse($c->created_at)->format('Y-m-d') }}</td>
                <td><span class="status-badge {{ $statusClass }}">{{ ucfirst($c->status) }}</span></td>
                <td class="actions">
                  <button class="action view" onclick="openViewModal(this)"><i class="fa-regular fa-eye"></i> View</button>
                  <button class="action del" onclick="openDeleteModal({{ $c->community_id }}, @js($c->name))"><i class="fas fa-trash"></i> Delete</button>
                </td>
              </tr>
            @empty
              <tr><td colspan="8" class="muted">No communities found.</td></tr>
            @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <!-- VIEW MODAL -->
      <div id="viewModal" class="admin_modal" role="dialog" aria-modal="true" aria-labelledby="viewModalTitle">
        <div class="admin_modal_content">
          <span class="admin_modal_close" onclick="closeModals()" aria-label="Close">&times;</span>
          <h2 id="viewModalTitle" class="title">Community Details</h2>
          <div class="details">
            <img id="viewCover" class="banner" alt="Community cover">
            <div class="kv">
              <b>Community ID</b>  <span id="vId"></span>
              <b>Name</b>          <span id="vName"></span>
              <b>Description</b>   <span id="vDesc"></span>
              <b>Privacy</b>       <span id="vPrivacy"></span>
              <b>Status</b>        <span id="vStatus"></span>
              <b>Created</b>       <span id="vCreated"></span>
              <b>Creator</b>
              <span style="display:flex;align-items:center;gap:.5rem">
                <img id="vCreatorImg" src="" alt="Creator" style="width:32px;height:32px;border-radius:50%;object-fit:cover;border:1px solid var(--border)">
                <span id="vCreatorName"></span>
              </span>
              <b>Total Members</b> <span id="vMembers"></span>
            </div>
          </div>
        </div>
      </div>

      <!-- DELETE MODAL -->
      <div id="deleteModal" class="admin_modal" role="dialog" aria-modal="true" aria-labelledby="deleteModalTitle">
        <div class="admin_modal_content">
          <span class="admin_modal_close" onclick="closeModals()" aria-label="Close">&times;</span>
          <h2 id="deleteModalTitle" class="title">Confirm Deletion</h2>
          <div class="confirm">
            <p id="deleteText" class="muted">Are you sure?</p>
            <form id="deleteForm" method="POST" action="{{ route('admin.community.destroy') }}">
              @csrf
              @method('DELETE')
              <input type="hidden" name="community_id" id="deleteId">
              <div class="buttons">
                <button type="button" class="btn ghost" onclick="closeModals()">Cancel</button>
                <button type="submit" class="btn danger"><i class="fa-solid fa-trash"></i> Delete</button>
              </div>
            </form>
          </div>
        </div>
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

    // Modals helpers
    function closeModals(){
      document.getElementById('viewModal').style.display = 'none';
      document.getElementById('deleteModal').style.display = 'none';
    }
    window.addEventListener('click', e => {
      if (e.target?.classList?.contains('admin_modal')) closeModals();
    });
    window.addEventListener('keydown', e => {
      if (e.key === 'Escape') closeModals();
    });

    // View modal
    function openViewModal(btn){
      const tr = btn.closest('tr');
      if (!tr) return;

      document.getElementById('vId').textContent      = '#'+(tr.dataset.id || '');
      document.getElementById('vName').textContent    = tr.dataset.name || '';
      document.getElementById('vDesc').textContent    = tr.dataset.description || '—';
      document.getElementById('vPrivacy').textContent = (tr.dataset.privacy || 'public').replace(/^./, m=>m.toUpperCase());
      document.getElementById('vStatus').textContent  = (tr.dataset.status || '').replace(/^./, m=>m.toUpperCase());
      document.getElementById('vCreated').textContent = tr.dataset.created || '';
      document.getElementById('vMembers').textContent = tr.dataset.members || '0';
      document.getElementById('viewCover').src        = tr.dataset.cover || '';
      document.getElementById('vCreatorName').textContent = tr.dataset.creatorName || '';
      document.getElementById('vCreatorImg').src      = tr.dataset.creatorImage || '';

      document.getElementById('viewModal').style.display = 'flex';
    }

    // Delete modal
    function openDeleteModal(id, name){
      document.getElementById('deleteId').value = id;
      document.getElementById('deleteText').textContent =
        `Are you sure you want to delete community "${name}" (ID: ${id})? This will permanently delete all related posts, comments, likes, messages and members.`;
      document.getElementById('deleteModal').style.display = 'flex';
    }
  </script>
</body>
</html>
