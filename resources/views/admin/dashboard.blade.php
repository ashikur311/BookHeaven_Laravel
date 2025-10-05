<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  {{-- Icons + Chart.js --}}
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  {{-- Your external CSS (kept) --}}
  <link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}?v={{ filemtime(public_path('css/admin_dashboard.css')) }}">

  <style>
    /* == Core layout + variables to keep colors in sync == */
    :root{
      --header-h: 64px;

      /* Light theme */
      --nav-bg: #1f2937;        /* header + sidebar */
      --nav-fg: #ffffff;
      --nav-border: rgba(255,255,255,.06);

      --page-bg: #f5f7fb;       /* content */
      --card: #ffffff;
      --border: #e5e7eb;
      --text: #1f2937;
      --text-muted: #6b7280;

      --shadow: 0 1px 1px rgba(0,0,0,.02);
    }
    body.admin-dark-mode{
      --nav-bg: #0f172a;
      --nav-fg: #e5e7eb;
      --nav-border: rgba(255,255,255,.12);

      --page-bg: #0b1220;
      --card: #0f172a;
      --border: #1e293b;
      --text: #e5e7eb;
      --text-muted: #94a3b8;

      --shadow: 0 1px 1px rgba(0,0,0,.25);
    }

    /* Reset + page frame */
    *{box-sizing:border-box;margin:0;padding:0}
    html, body { height: 100%; }
    body{
      font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      background: var(--page-bg);
      color: var(--text);
      line-height: 1.5;
      overflow: hidden; /* prevent full page scroll; panes will scroll */
    }
    a{ color: inherit; text-decoration: none; }

    /* Header (shared color with sidebar) */
    .admin_header{
      height: var(--header-h);
      display:flex; align-items:center; justify-content:space-between;
      padding: 0 1rem;
      background: var(--nav-bg);
      color: var(--nav-fg);
      border-bottom: 1px solid var(--nav-border);
      position: sticky; top: 0; z-index: 50;
    }
    .logo img{ height: 40px; }
    .admin_header_right{ display:flex; align-items:center; gap:.75rem }
    .admin_header_right h1{ font-size: 1rem; font-weight: 800; }
    .admin_theme_toggle{
      background: transparent; border: 1px solid var(--nav-border); color: var(--nav-fg);
      padding: .45rem .6rem; border-radius: .5rem; cursor: pointer;
    }

    /* Main layout: sidebar + content columns */
    .admin_main{
      height: calc(100vh - var(--header-h));
      display: grid;
      grid-template-columns: 260px 1fr;
    }

    /* Sidebar (same bg as header) */
    .admin_sidebar{
      background: var(--nav-bg);
      color: var(--nav-fg);
      border-right: 1px solid var(--nav-border);
      position: sticky; /* sticks under the header */
      top: var(--header-h);
      height: calc(100vh - var(--header-h));
      overflow-y: auto;    /* <-- independent scroll */
      overscroll-behavior: contain;
    }
    .admin_sidebar_nav ul{ list-style: none; padding: .5rem; }
    .admin_sidebar_nav a, .admin-logout-link{
      display:flex; align-items:center; gap:.6rem;
      padding: .7rem .8rem; border-radius: .5rem;
    }
    .admin_sidebar_nav a:hover{ background: rgba(255,255,255,.08); }
    .admin_sidebar_nav a.active{ background: rgba(59,130,246,.22); }
    .admin_sidebar_nav i{ width: 18px; text-align: center; }

    /* Content pane (own scroll) */
    .admin_main_content{
      background: var(--page-bg);
      height: calc(100vh - var(--header-h));
      overflow: auto;           /* <-- independent scroll */
      overscroll-behavior: contain;
      padding: 1.25rem;
    }

    /* Cards / tables */
    .stats-grid{
      display:grid; gap:1rem;
      grid-template-columns: repeat( auto-fit, minmax(210px,1fr) );
      margin-bottom: 1rem;
    }
    .stat-card{
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 1rem 1.25rem;
      box-shadow: var(--shadow);
      text-align: left;
    }
    .stat-card h3{ font-size:.9rem; color: var(--text-muted); margin-bottom:.25rem }
    .stat-card .stat-value{ font-size: 1.8rem; font-weight: 800; }

    .section-title{ margin: .75rem 0; font-size:1rem; color: var(--text-muted); font-weight: 800; }

    .admin_table{ width:100%; border-collapse: collapse; margin-top:.5rem }
    .admin_table th, .admin_table td{ padding:.8rem .9rem; border-bottom:1px solid var(--border); text-align:left }
    .admin_table thead th{ background: color-mix(in srgb, var(--card) 85%, var(--border) 15%); color: var(--text-muted); font-weight: 800 }
    .admin_table tbody tr:hover{ background: color-mix(in srgb, var(--card) 92%, var(--border) 8%); }

    .action-form{ display:inline }
    .action-btn{ padding:.45rem .8rem; border-radius:.5rem; border:1px solid var(--border); cursor:pointer }
    .view-btn{ background: transparent; color:#2563eb; border-color: rgba(37,99,235,.35) }
    .approve-btn{ background: transparent; color:#059669; border-color: rgba(5,150,105,.35) }
    .cancel-btn{ background: transparent; color:#dc2626; border-color: rgba(220,38,38,.35) }

    .alert{
      padding: .75rem 1rem; border-radius: 10px; border:1px solid var(--border);
      background: var(--card); margin-bottom: .75rem;
    }
    .alert-error{ border-color: rgba(220,38,38,.35); color:#991b1b; background: #fee2e2 }
    .alert-success{ border-color: rgba(5,150,105,.35); color:#065f46; background: #d1fae5 }

    /* Charts sizing (stable) */
    .charts-section {
      display: grid;
      gap: 1rem;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    }
    .chart-container {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 1rem;
      box-shadow: var(--shadow);
      min-height: 320px;
      display: flex; flex-direction: column;
    }
    .chart-container h3 { margin: 0 0 .5rem 0; font-size: .95rem; color: var(--text-muted); font-weight: 800; }
    .chart-wrap { position: relative; flex: 1; min-height: 260px; }
    .chart-wrap canvas { width: 100% !important; height: 260px !important; }

    /* Responsive: stack on small screens */
    @media (max-width: 900px){
      .admin_main{ grid-template-columns: 1fr; }
      .admin_sidebar{ position: static; height: auto; }
      body{ overflow: auto; } /* on phones, allow page scroll as usual */
    }
  </style>
</head>
<body>
  <header class="admin_header">
    <div class="logo"><img src="{{ asset('assets/images/download.png') }}" alt="Logo"></div>
    <div class="admin_header_right">
      <h1>Admin Dashboard</h1>
      <p>Welcome, {{ session('admin_full_name') ?? 'Admin' }}</p>
      <button class="admin_theme_toggle" id="themeToggle"><i class="fas fa-moon"></i></button>
    </div>
  </header>

  <main class="admin_main">
    <aside class="admin_sidebar">
      <nav class="admin_sidebar_nav">
        <ul>
          <li><a href="{{ route('admin.dashboard') }}" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
          <li><a href="{{ route('admin.add') }}"><i class="fas fa-plus-circle"></i> Add</a></li>
          <li><a href="{{ route('admin.users') }}"><i class="fas fa-users"></i> Users</a></li>
          <li><a href="{{ route('admin.partners') }}"><i class="fas fa-handshake"></i> Partners</a></li>
          <li><a href="{{ route('admin.writers') }}"><i class="fas fa-pen-fancy"></i> Writers</a></li>
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
              <button type="submit" class="admin-logout-link">
                <i class="fas fa-sign-out-alt"></i> Logout
              </button>
            </form>
          </li>
        </ul>
      </nav>
    </aside>

    <div class="admin_main_content">
      @if(session('error'))   <div class="alert alert-error">{{ session('error') }}</div> @endif
      @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

      <h1 style="margin-bottom:.5rem">Dashboard Overview</h1>

      {{-- Stats Grid --}}
      <div class="stats-grid">
        <div class="stat-card"><h3>Total Users</h3><div class="stat-value">{{ number_format($stats['total_users']) }}</div></div>
        <div class="stat-card"><h3>Total Books</h3><div class="stat-value">{{ number_format($stats['total_books']) }}</div></div>
        <div class="stat-card"><h3>Total Partners</h3><div class="stat-value">{{ number_format($stats['total_partners']) }}</div></div>
        <div class="stat-card"><h3>Audio Books</h3><div class="stat-value">{{ number_format($stats['total_audiobooks']) }}</div></div>
        <div class="stat-card"><h3>Total Writers</h3><div class="stat-value">{{ number_format($stats['total_writers']) }}</div></div>
        <div class="stat-card"><h3>Sales This Month</h3><div class="stat-value">{{ number_format($stats['total_sales_month']) }}</div></div>
        <div class="stat-card"><h3>Total Orders</h3><div class="stat-value">{{ number_format($stats['total_orders']) }}</div></div>
      </div>

      {{-- Charts --}}
      <h2 class="section-title">Performance Metrics</h2>
      <div class="charts-section">
        <div class="chart-container">
          <h3>Monthly Sales</h3>
          <div class="chart-wrap"><canvas id="salesChart"></canvas></div>
        </div>
        <div class="chart-container">
          <h3>Subscription Sales</h3>
          <div class="chart-wrap"><canvas id="subscriptionChart"></canvas></div>
        </div>
        <div class="chart-container">
          <h3>User Growth</h3>
          <div class="chart-wrap"><canvas id="usersChart"></canvas></div>
        </div>
      </div>

      {{-- Pending Orders --}}
      <h2 class="section-title">Pending Orders</h2>
      <table class="admin_table">
        <thead><tr><th>Order ID</th><th>User Name</th><th>Date</th><th>Amount</th><th>Actions</th></tr></thead>
        <tbody>
          @foreach($pending_orders as $order)
            <tr>
              <td>#{{ $order->id }}</td>
              <td>{{ $order->user_name }}</td>
              <td>{{ $order->date }}</td>
              <td>${{ number_format($order->amount,2) }}</td>
              <td><a href="{{ route('admin.orders.edit', $order->id) }}" class="action-btn view-btn">View</a></td>
            </tr>
          @endforeach
        </tbody>
      </table>

      {{-- Pending Partners --}}
      <h2 class="section-title">Pending Partner Requests</h2>
      <table class="admin_table">
        <thead><tr><th>Partner ID</th><th>Name</th><th>Joined Date</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
          @foreach($pending_partners as $partner)
            <tr>
              <td>#{{ $partner->id }}</td>
              <td>{{ $partner->name }}</td>
              <td>{{ $partner->joined_date }}</td>
              <td>{{ $partner->status }}</td>
              <td>
                <form method="POST" action="{{ route('admin.partner.approve') }}" class="action-form" onsubmit="return confirm('Approve this partner?');">
                  @csrf
                  <input type="hidden" name="partner_id" value="{{ $partner->id }}">
                  <button type="submit" class="action-btn approve-btn">Approve</button>
                </form>
                <form method="POST" action="{{ route('admin.partner.cancel') }}" class="action-form" onsubmit="return confirm('Cancel this partner request?');">
                  @csrf
                  <input type="hidden" name="partner_id" value="{{ $partner->id }}">
                  <button type="submit" class="action-btn cancel-btn">Cancel</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </main>

  <script>
    // ======= THEME TOGGLE =======
    const themeToggle = document.getElementById('themeToggle');
    const icon = themeToggle?.querySelector('i');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const savedTheme = localStorage.getItem('admin-theme');

    if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
      document.body.classList.add('admin-dark-mode');
      icon && icon.classList.replace('fa-moon','fa-sun');
    }
    themeToggle?.addEventListener('click', () => {
      const goingDark = !document.body.classList.contains('admin-dark-mode');
      document.body.classList.toggle('admin-dark-mode', goingDark);
      localStorage.setItem('admin-theme', goingDark ? 'dark' : 'light');
      if (icon) icon.classList.replace(goingDark ? 'fa-moon' : 'fa-sun', goingDark ? 'fa-sun' : 'fa-moon');
      // Re-theme charts
      window._dashCharts?.forEach(ch => { applyThemeToChart(ch); ch.update('none'); });
    });

    // ======= DATA FROM BACKEND =======
    const monthlySales      = @json($monthly_sales);
    const subscriptionSales = @json($subscription_sales);
    const userGrowth        = @json($user_growth);

    // Defensive helpers
    const toNumberArray = (arr) => Array.isArray(arr) ? arr.map(v => (v===''||v===null||isNaN(+v)) ? 0 : +v) : [];
    const safeLabels = (o) => Array.isArray(o?.labels) ? [...o.labels] : [];
    const safeData   = (o) => toNumberArray(o?.data || []);

    const getColors = () => {
      const dark = document.body.classList.contains('admin-dark-mode');
      return {
        text: dark ? '#e5e7eb' : '#475569',
        grid: dark ? 'rgba(255,255,255,0.12)' : 'rgba(0,0,0,0.08)',
        line: dark ? 'rgba(96,165,250,1)'  : 'rgba(37,99,235,1)',
        lineFill: dark ? 'rgba(96,165,250,0.18)' : 'rgba(37,99,235,0.18)',
        bar: dark ? 'rgba(155,89,182,0.75)' : 'rgba(155,89,182,0.85)',
        donut: [
          dark ? 'rgba(96,165,250,0.8)'  : 'rgba(52,152,219,0.8)',
          dark ? 'rgba(52,211,153,0.8)'  : 'rgba(46,204,113,0.8)',
          dark ? 'rgba(251,191,36,0.8)'  : 'rgba(241,196,15,0.8)'
        ]
      };
    };

    const baseOptions = (title) => {
      const c = getColors();
      return {
        responsive: true,
        maintainAspectRatio: false,
        animation: { duration: 350 },
        plugins: {
          legend: { labels: { color: c.text } },
          title:  { display: !!title, text: title, color: c.text, font: { weight: 'bold' } },
          tooltip:{ mode: 'index', intersect: false }
        },
        scales: {
          y: { beginAtZero: true, grid: { color: c.grid }, ticks: { color: c.text } },
          x: { grid: { color: c.grid }, ticks: { color: c.text } }
        }
      };
    };

    function applyThemeToChart(chart) {
      const c = getColors();
      if (chart.options?.plugins?.legend?.labels) chart.options.plugins.legend.labels.color = c.text;
      if (chart.options?.plugins?.title) chart.options.plugins.title.color = c.text;
      if (chart.options?.scales?.x) { chart.options.scales.x.ticks.color = c.text; chart.options.scales.x.grid.color = c.grid; }
      if (chart.options?.scales?.y) { chart.options.scales.y.ticks.color = c.text; chart.options.scales.y.grid.color = c.grid; }
      if (chart.config.type === 'line') {
        const ds = chart.data.datasets?.[0];
        if (ds) { ds.borderColor = c.line; ds.backgroundColor = c.lineFill; }
      }
      if (chart.config.type === 'bar') {
        const ds = chart.data.datasets?.[0];
        if (ds) ds.backgroundColor = c.bar;
      }
      if (chart.config.type === 'doughnut') {
        const ds = chart.data.datasets?.[0];
        if (ds) ds.backgroundColor = [...c.donut];
      }
    }

    function destroyOldCharts() {
      if (window._dashCharts && Array.isArray(window._dashCharts)) {
        window._dashCharts.forEach(ch => { try { ch.destroy(); } catch(_){} });
      }
      window._dashCharts = [];
    }

    function initCharts() {
      destroyOldCharts();
      const charts = [];

      const salesCtx = document.getElementById('salesChart')?.getContext('2d');
      const subsCtx  = document.getElementById('subscriptionChart')?.getContext('2d');
      const usersCtx = document.getElementById('usersChart')?.getContext('2d');

      if (salesCtx) {
        const labels = safeLabels(monthlySales);
        const nums   = safeData(monthlySales);
        const c = getColors();
        const ch = new Chart(salesCtx, {
          type: 'line',
          data: {
            labels: labels,
            datasets: [{
              label: 'Monthly Sales',
              data: nums,
              borderColor: c.line,
              backgroundColor: c.lineFill,
              borderWidth: 2,
              tension: .25,
              fill: true,
              pointRadius: 2,
              pointHoverRadius: 4
            }]
          },
          options: baseOptions('Sales')
        });
        charts.push(ch);
      }

      if (subsCtx) {
        const labels = safeLabels(subscriptionSales);
        const nums   = safeData(subscriptionSales);
        const ch = new Chart(subsCtx, {
          type: 'doughnut',
          data: {
            labels: labels,
            datasets: [{
              data: nums,
              backgroundColor: getColors().donut,
              borderWidth: 1
            }]
          },
          options: { ...baseOptions('Subscriptions'), cutout: '58%' }
        });
        charts.push(ch);
      }

      if (usersCtx) {
        const labels = safeLabels(userGrowth);
        const nums   = safeData(userGrowth);
        const ch = new Chart(usersCtx, {
          type: 'bar',
          data: {
            labels: labels,
            datasets: [{
              label: 'User Growth',
              data: nums,
              backgroundColor: getColors().bar,
              borderWidth: 0,
              borderRadius: 8,
              maxBarThickness: 36
            }]
          },
          options: baseOptions('Users')
        });
        charts.push(ch);
      }

      window._dashCharts = charts;
      charts.forEach(c => { applyThemeToChart(c); c.update('none'); });
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', initCharts, { once: true });
    } else {
      initCharts();
    }

    window.addEventListener('resize', () => {
      window._dashCharts?.forEach(c => c.resize());
    });
  </script>
</body>
</html>
