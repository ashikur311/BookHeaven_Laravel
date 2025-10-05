<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Subscription</title>
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
      font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      background:var(--bg); color:var(--text); line-height:1.5;
    }

    /* ========= HEADER (identical palette to Reports) ========= */
    .admin_header{
      display:flex; justify-content:space-between; align-items:center;
      padding:1rem 1.5rem; background:#1f2937; color:#fff;
      position:sticky; top:0; z-index:30; border-bottom:1px solid rgba(255,255,255,.06);
    }
    .logo img{height:40px}
    .admin_header_right{display:flex;align-items:center;gap:.75rem}
    .admin_header_right h1{font-size:1.1rem;font-weight:600}
    .admin_theme_toggle{
      background:transparent; border:1px solid rgba(255,255,255,.2); color:#fff;
      padding:.5rem .65rem; border-radius:.5rem; cursor:pointer;
    }

    /* ========= LAYOUT / SIDEBAR (sticky + scroll) ========= */
    .admin_main{display:grid; grid-template-columns:260px 1fr; min-height:calc(100vh - 64px)}
    .admin_sidebar{
      background:#111827; color:#cbd5e1; border-right:1px solid rgba(255,255,255,.06);
      position:sticky; top:64px; align-self:start; height:calc(100vh - 64px); overflow-y:auto;
    }
    .admin_sidebar_nav ul{list-style:none; padding:.75rem}
    .admin_sidebar_nav a, .admin_sidebar_nav button.linklike{
      display:flex; align-items:center; gap:.75rem; padding:.65rem .75rem; margin-bottom:.25rem;
      text-decoration:none; color:inherit; border-radius:.5rem; border:none; background:transparent;
      width:100%; text-align:left; cursor:pointer;
    }
    .admin_sidebar_nav a:hover, .admin_sidebar_nav button.linklike:hover{ background:rgba(255,255,255,.06) }
    .admin_sidebar_nav a.active{ background:rgba(59,130,246,.18); color:#fff }
    .admin_main_content{ padding:1.5rem }

    /* ========= STATS ========= */
    .grid{display:grid; gap:1rem}
    .grid.stats{ grid-template-columns:repeat(5,minmax(0,1fr)) }
    @media (max-width:1200px){ .grid.stats{ grid-template-columns:repeat(3,minmax(0,1fr)) } }
    @media (max-width:900px){ .admin_main{ grid-template-columns:1fr } .admin_sidebar{ display:none } .grid.stats{ grid-template-columns:repeat(2,minmax(0,1fr)) } }
    @media (max-width:560px){ .grid.stats{ grid-template-columns:1fr } }

    .stat-card{
      background:var(--card); border:1px solid var(--border); border-radius:12px;
      padding:1rem 1.25rem; box-shadow:var(--shadow); text-align:center; display:grid; gap:.25rem;
    }
    .stat-card h3{margin:0; font-size:.9rem; color:var(--text-muted)}
    .stat-value{ font-size:1.75rem; font-weight:800; color:var(--text) }

    /* ========= TOOLBAR ========= */
    .toolbar{
      background:var(--card); border:1px solid var(--border); border-radius:12px;
      padding:.9rem 1rem; margin:1rem 0; display:flex; gap:.75rem; align-items:center; flex-wrap:wrap;
      box-shadow:var(--shadow);
    }
    .toolbar .title{ font-weight:700; margin-right:auto }
    .input{
      display:inline-flex; align-items:center; gap:.5rem;
      background:var(--bg); border:1px solid var(--border); border-radius:.6rem; padding:.55rem .75rem;
    }
    .input input{ border:none; outline:none; background:transparent; color:var(--text); min-width:220px }
    .select{
      background:var(--bg); border:1px solid var(--border); border-radius:.6rem; padding:.55rem .6rem; color:var(--text);
    }
    .btn{ padding:.6rem .9rem; border:none; border-radius:.55rem; cursor:pointer; font-weight:600 }
    .btn.primary{ background:var(--primary); color:#fff }
    .btn.ghost{ background:transparent; border:1px solid var(--border); color:var(--text) }

    /* ========= TABLE ========= */
    .table-card{
      background:var(--card); border:1px solid var(--border); border-radius:12px; box-shadow:var(--shadow); overflow:clip;
    }
    .table-wrap{ width:100%; overflow-x:auto }
    table{ width:100%; border-collapse:collapse }
    thead th{
      position:sticky; top:0; z-index:1; background:var(--table-header);
      color:var(--text-muted); font-weight:700; text-align:left; padding:.75rem .9rem; border-bottom:1px solid var(--border);
    }
    tbody td{ padding:.75rem .9rem; border-bottom:1px solid var(--border) }
    tbody tr:hover{ background:var(--table-hover) }

    .badge{
      display:inline-block; padding:.25rem .5rem; border-radius:999px; font-size:.75rem; font-weight:700;
    }
    .badge-success{ background:rgba(16,185,129,.15); color:#059669 }
    .badge-warning{ background:rgba(245,158,11,.15); color:#b45309 }
    .badge-muted{ background:var(--chip); color:var(--text-muted) }

    .action-btns{ display:flex; gap:.4rem; flex-wrap:wrap }
    .action-btn{
      padding:.45rem .8rem; border:1px solid var(--border); background:transparent; color:var(--text);
      border-radius:.5rem; cursor:pointer; font-size:.9rem;
    }
    .edit-btn{ border-color:var(--primary); color:var(--primary) }
    .delete-btn{ border-color:var(--danger); color:var(--danger) }

    /* ========= ALERTS ========= */
    .alert{ padding:.9rem 1rem; border-radius:.6rem; margin:1rem 0; border:1px solid }
    .alert-success{ background:#dcfce7; color:#14532d; border-color:#bbf7d0 }
    .alert-error{ background:#fee2e2; color:#7f1d1d; border-color:#fecaca }
  </style>
</head>
<body>
<header class="admin_header">
  <div class="logo"><img src="{{ asset('assets/images/download.png') }}" alt="Logo"></div>
  <div class="admin_header_right">
    <h1>Admin Dashboard</h1>
    <span class="chip" style="background:var(--chip);color:var(--text-muted);padding:.25rem .5rem;border-radius:999px;font-size:.75rem;">Subscriptions</span>
    <button class="admin_theme_toggle" id="themeToggle" aria-label="Toggle theme"><i class="fas fa-moon"></i></button>
  </div>
</header>

<main class="admin_main">
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
        <li><a href="{{ route('admin.subscription') }}" class="active"><i class="fas fa-star"></i> Subscription</a></li>
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

  <div class="admin_main_content">
    @if(session('error'))   <div class="alert alert-error">{{ session('error') }}</div> @endif
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(!empty($error_message)) <div class="alert alert-error">{{ $error_message }}</div> @endif

    <h1 style="margin-bottom:.6rem">Subscription Management</h1>

    <!-- Stats -->
    <div class="grid stats">
      <div class="stat-card">
        <h3>Total Plans</h3>
        <div class="stat-value">{{ number_format($stats['total_plans']) }}</div>
      </div>
      <div class="stat-card">
        <h3>Total Sales</h3>
        <div class="stat-value">{{ number_format($stats['total_sales']) }}</div>
      </div>
      <div class="stat-card">
        <h3>Premium Sales</h3>
        <div class="stat-value">{{ number_format($stats['premium_sales']) }}</div>
      </div>
      <div class="stat-card">
        <h3>Gold Sales</h3>
        <div class="stat-value">{{ number_format($stats['gold_sales']) }}</div>
      </div>
      <div class="stat-card">
        <h3>Basic Sales</h3>
        <div class="stat-value">{{ number_format($stats['basic_sales']) }}</div>
      </div>
    </div>

    <!-- Toolbar -->
    <div class="toolbar">
      <div class="title">Subscription Plans</div>
      <div class="input">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="planSearch" placeholder="Search plan name...">
      </div>
      <select id="statusFilter" class="select" aria-label="Filter by status">
        <option value="">All statuses</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
        <option value="archived">Archived</option>
      </select>
      <button class="btn ghost" id="resetFilters"><i class="fa-solid fa-rotate-left"></i> Reset</button>
      <a href="{{ route('admin.subscription') }}" class="btn primary" style="text-decoration:none;"><i class="fa-solid fa-list"></i> Refresh</a>
    </div>

    <!-- Plans table -->
    <div class="table-card">
      <div class="table-wrap">
        <table class="admin_table" id="plansTable">
          <thead>
            <tr>
              <th>Plan ID</th>
              <th>Name</th>
              <th>Price</th>
              <th>Validity (Days)</th>
              <th>Books</th>
              <th>Audiobooks</th>
              <th>Status</th>
              <th style="min-width:180px">Actions</th>
            </tr>
          </thead>
          <tbody>
          @foreach($subscription_plans as $plan)
            @php
              $status = strtolower($plan->status);
              $badgeClass = $status === 'active' ? 'badge-success' : ($status === 'inactive' ? 'badge-warning' : 'badge-muted');
            @endphp
            <tr data-name="{{ strtolower($plan->plan_name) }}" data-status="{{ $status }}">
              <td>#{{ $plan->plan_id }}</td>
              <td class="col-name">{{ $plan->plan_name }}</td>
              <td>${{ number_format($plan->price, 2) }}</td>
              <td>{{ $plan->validity_days }}</td>
              <td>{{ $plan->book_quantity }}</td>
              <td>{{ $plan->audiobook_quantity }}</td>
              <td><span class="badge {{ $badgeClass }}">{{ ucfirst($plan->status) }}</span></td>
              <td>
                <div class="action-btns">
                  <a href="{{ route('admin.subscription.edit', $plan->plan_id) }}" class="action-btn edit-btn"><i class="fa-regular fa-pen-to-square"></i> Edit</a>
                  <form class="action-form" method="POST" action="{{ route('admin.subscription.delete') }}"
                        onsubmit="return confirm('Delete subscription plan {{ addslashes($plan->plan_name) }}?');">
                    @csrf
                    <input type="hidden" name="plan_id" value="{{ $plan->plan_id }}">
                    <button type="submit" class="action-btn delete-btn"><i class="fa-regular fa-trash-can"></i> Delete</button>
                  </form>
                </div>
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
  // ===== Theme toggle (same as Reports) =====
  const themeToggle = document.getElementById('themeToggle');
  const icon = themeToggle.querySelector('i');
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  const savedTheme = localStorage.getItem('admin-theme');
  if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
    document.body.classList.add('admin-dark-mode'); icon.classList.replace('fa-moon','fa-sun');
  }
  themeToggle.addEventListener('click', () => {
    document.body.classList.toggle('admin-dark-mode');
    if (document.body.classList.contains('admin-dark-mode')) {
      localStorage.setItem('admin-theme','dark'); icon.classList.replace('fa-moon','fa-sun');
    } else {
      localStorage.setItem('admin-theme','light'); icon.classList.replace('fa-sun','fa-moon');
    }
  });

  // ===== Client-side filter/search =====
  const planSearch = document.getElementById('planSearch');
  const statusFilter = document.getElementById('statusFilter');
  const resetFilters = document.getElementById('resetFilters');
  const rows = Array.from(document.querySelectorAll('#plansTable tbody tr'));

  // Preselect status filter if your statuses are known (optional)
  // statusFilter.value = '';

  function applyFilters(){
    const q = (planSearch.value || '').toLowerCase().trim();
    const status = (statusFilter.value || '').toLowerCase().trim();

    rows.forEach(r=>{
      const name = (r.dataset.name || '').toLowerCase();
      const st   = (r.dataset.status || '').toLowerCase();
      const matchesName = !q || name.includes(q);
      const matchesStatus = !status || st === status;
      r.style.display = (matchesName && matchesStatus) ? '' : 'none';
    });
  }
  planSearch.addEventListener('input', applyFilters);
  statusFilter.addEventListener('change', applyFilters);
  resetFilters.addEventListener('click', ()=>{
    planSearch.value=''; statusFilter.value=''; applyFilters(); planSearch.focus();
  });
</script>
</body>
</html>
