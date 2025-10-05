<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Reports | BKH</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

  <style>
    :root {
      --bg: #f5f7fb;
      --text: #1f2937;
      --text-muted: #6b7280;
      --primary: #3b82f6;
      --success: #10b981;
      --warning: #f59e0b;
      --danger: #ef4444;
      --card: #ffffff;
      --border: #e5e7eb;
      --table-header: #f3f4f6;
      --table-hover: #f9fafb;
      --chip: #eef2ff;
    }
    body.admin-dark-mode {
      --bg: #0f172a;
      --text: #e5e7eb;
      --text-muted: #9ca3af;
      --primary: #60a5fa;
      --success: #34d399;
      --warning: #fbbf24;
      --danger: #f87171;
      --card: #111827;
      --border: #1f2937;
      --table-header: #0b1220;
      --table-hover: #0c1427;
      --chip: #1f2937;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji","Segoe UI Emoji";
      background: var(--bg);
      color: var(--text);
      line-height: 1.5;
    }

    .admin_header {
      display: flex; justify-content: space-between; align-items: center;
      padding: 1rem 1.5rem; background: #1f2937; color: #fff;
      position: sticky; top: 0; z-index: 30;
      border-bottom: 1px solid rgba(255,255,255,.06);
    }
    .logo img { height: 40px; }
    .admin_header_right { display: flex; align-items: center; gap: .75rem; }
    .admin_header_right h1 { font-size: 1.1rem; font-weight: 600; }
    .admin_theme_toggle {
      background: transparent; border: 1px solid rgba(255,255,255,.2); color: #fff;
      padding: .5rem .65rem; border-radius: .5rem; cursor: pointer;
    }

    .admin_main { display: grid; grid-template-columns: 260px 1fr; min-height: calc(100vh - 64px); }

    .admin_sidebar {
      background: #111827; color: #cbd5e1; border-right: 1px solid rgba(255,255,255,.06);
      position: sticky; top: 64px; align-self: start; height: calc(100vh - 64px); overflow-y: auto;
    }
    .admin_sidebar_nav ul { list-style: none; padding: .75rem; }
    .admin_sidebar_nav a, .admin_sidebar_nav button.linklike {
      display: flex; align-items: center; gap: .75rem;
      padding: .65rem .75rem; margin-bottom: .25rem;
      text-decoration: none; color: inherit; border-radius: .5rem;
      border: none; background: transparent; width: 100%; text-align: left; cursor: pointer;
    }
    .admin_sidebar_nav a:hover, .admin_sidebar_nav button.linklike:hover { background: rgba(255,255,255,.06); }
    .admin_sidebar_nav a.active { background: rgba(59,130,246,.18); color: #fff; }

    .admin_main_content { padding: 1.5rem; }

    .section {
      background: var(--card); border: 1px solid var(--border); border-radius: 12px; margin-bottom: 1rem;
      box-shadow: 0 1px 1px rgba(0,0,0,.02);
    }
    .section-header {
      padding: 1rem 1.25rem; border-bottom: 1px solid var(--border);
      display: flex; align-items: baseline; justify-content: space-between; gap: 1rem;
    }
    .section-title { font-size: 1.1rem; font-weight: 700; }
    .muted { color: var(--text-muted); font-size: .9rem; }

    .grid {
      display: grid; gap: 1rem;
    }
    .grid.stats { grid-template-columns: repeat(4,minmax(0,1fr)); }
    .grid.two { grid-template-columns: repeat(2,minmax(0,1fr)); }

    @media (max-width: 1100px) { .grid.stats { grid-template-columns: repeat(2,minmax(0,1fr)); } }
    @media (max-width: 900px)  { .admin_main { grid-template-columns: 1fr; } .admin_sidebar { display: none; } .grid.two { grid-template-columns: 1fr; } }

    .stat {
      background: var(--card); border: 1px solid var(--border);
      border-radius: 12px; padding: 1rem 1.25rem; display: grid; gap: .25rem;
    }
    .stat .k { font-size: .85rem; color: var(--text-muted); }
    .stat .v { font-size: 1.75rem; font-weight: 800; color: var(--text); }
    .chip { font-size: .75rem; padding: .25rem .5rem; border-radius: 999px; background: var(--chip); color: var(--text-muted); }

    .canvas-wrap { padding: 1rem 1.25rem; }
    .chart-box { position: relative; height: 360px; }

    table { width: 100%; border-collapse: collapse; }
    th, td { padding: .75rem .9rem; border-bottom: 1px solid var(--border); font-size: .95rem; }
    thead th { background: var(--table-header); color: var(--text-muted); font-weight: 700; }
    tbody tr:hover { background: var(--table-hover); }

    .currency { font-variant-numeric: tabular-nums; }
  </style>
</head>
<body>
  <header class="admin_header">
    <div class="logo"><img src="\BookHeaven2.0\assets\images\download.png" alt="Logo"></div>
    <div class="admin_header_right">
      <h1>Admin Reports</h1>
      <span class="chip">Updated {{ now()->format('Y-m-d H:i:s') }}</span>
      <button class="admin_theme_toggle" id="themeToggle"><i class="fas fa-moon"></i></button>
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
          <li><a href="{{ route('admin.subscription') }}"><i class="fas fa-star"></i> Subscription</a></li>
          <li><a href="{{ route('admin.events') }}"><i class="fas fa-calendar-alt"></i> Events</a></li>
          <li><a href="{{ route('admin.community') }}"><i class="fas fa-users"></i> Community</a></li>
          <li><a href="{{ route('admin.question') }}"><i class="fa-solid fa-question"></i> User Questions</a></li>
          <li><a href="{{ route('admin.reports') }}" class="active"><i class="fas fa-chart-bar"></i> Reports</a></li>
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

      {{-- Alerts --}}
      @if(session('error_message'))
        <div class="section" style="border-left: 4px solid var(--danger);">
          <div class="section-header">
            <div class="section-title" style="color: var(--danger)"><i class="fa-solid fa-triangle-exclamation"></i> Error</div>
          </div>
          <div class="canvas-wrap">
            {{ session('error_message') }}
          </div>
        </div>
      @endif

      {{-- KEY STATS --}}
      <div class="section">
        <div class="section-header">
          <div class="section-title">Key Statistics</div>
          <div class="muted">Live snapshot</div>
        </div>
        <div class="canvas-wrap">
          <div class="grid stats">
            <div class="stat">
              <div class="k">Total Users</div>
              <div class="v">{{ number_format($stats['total_users'] ?? 0) }}</div>
            </div>
            <div class="stat">
              <div class="k">Total Books</div>
              <div class="v">{{ number_format($stats['total_books'] ?? 0) }}</div>
            </div>
            <div class="stat">
              <div class="k">Audio Books</div>
              <div class="v">{{ number_format($stats['total_audiobooks'] ?? 0) }}</div>
            </div>
            <div class="stat">
              <div class="k">Total Orders</div>
              <div class="v">{{ number_format($stats['total_orders'] ?? 0) }}</div>
            </div>
            <div class="stat">
              <div class="k">Total Revenue</div>
              <div class="v currency">${{ number_format((float)($stats['total_revenue'] ?? 0), 2) }}</div>
            </div>
            <div class="stat">
              <div class="k">Active Subscriptions</div>
              <div class="v">{{ number_format($stats['active_subscriptions'] ?? 0) }}</div>
            </div>
            <div class="stat">
              <div class="k">Total Writers</div>
              <div class="v">{{ number_format($stats['total_writers'] ?? 0) }}</div>
            </div>
            <div class="stat">
              <div class="k">Events</div>
              <div class="v">{{ number_format($stats['total_events'] ?? 0) }}</div>
            </div>
          </div>
        </div>
      </div>

      {{-- SALES PERFORMANCE --}}
      <div class="section">
        <div class="section-header">
          <div class="section-title">Sales Performance</div>
          <div class="muted">Last 6 months</div>
        </div>
        <div class="canvas-wrap">
          <div class="chart-box">
            <canvas id="salesChart"></canvas>
          </div>
        </div>
      </div>

      {{-- SPLIT: CATEGORIES / GENRES --}}
      <div class="grid two">
        <div class="section">
          <div class="section-header">
            <div class="section-title">Book Categories</div>
            <div class="muted">Top 10</div>
          </div>
          <div class="canvas-wrap">
            <div class="chart-box" style="height: 320px;">
              <canvas id="categoriesChart"></canvas>
            </div>
            <div style="overflow-x:auto;">
              <table>
                <thead>
                  <tr><th>Category</th><th>Book Count</th></tr>
                </thead>
                <tbody>
                @foreach($categories_data as $c)
                  <tr>
                    <td>{{ $c['category'] }}</td>
                    <td>{{ $c['book_count'] }}</td>
                  </tr>
                @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="section">
          <div class="section-header">
            <div class="section-title">Book Genres</div>
            <div class="muted">Top 10</div>
          </div>
          <div class="canvas-wrap">
            <div class="chart-box" style="height: 320px;">
              <canvas id="genresChart"></canvas>
            </div>
            <div style="overflow-x:auto;">
              <table>
                <thead>
                  <tr><th>Genre</th><th>Book Count</th></tr>
                </thead>
                <tbody>
                @foreach($genres_data as $g)
                  <tr>
                    <td>{{ $g['genre'] }}</td>
                    <td>{{ $g['book_count'] }}</td>
                  </tr>
                @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      {{-- SUBSCRIPTION PERFORMANCE --}}
      <div class="section">
        <div class="section-header">
          <div class="section-title">Subscription Plan Performance</div>
          <div class="muted">Last 6 months</div>
        </div>
        <div class="canvas-wrap">
          <div class="chart-box">
            <canvas id="subscriptionChart"></canvas>
          </div>
          <div style="overflow-x:auto;">
            <table>
              <thead>
                <tr><th>Month</th><th>New Subscriptions</th><th>Renewals</th><th>Total Revenue</th></tr>
              </thead>
              <tbody>
              @foreach($subscription_monthly_data as $m)
                <tr>
                  <td>{{ \Carbon\Carbon::parse($m['month'].'-01')->format('M Y') }}</td>
                  <td>{{ $m['new_subs'] }}</td>
                  <td>{{ $m['renewals'] }}</td>
                  <td class="currency">${{ number_format((float)($m['revenue'] ?? 0), 2) }}</td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- TOP BOOKS --}}
      <div class="section">
        <div class="section-header">
          <div class="section-title">Top Selling Books</div>
          <div class="muted">By number of sales</div>
        </div>
        <div class="canvas-wrap" style="overflow-x:auto;">
          <table>
            <thead>
              <tr><th>Title</th><th>Price</th><th>Sales Count</th><th>Total Quantity</th><th>Avg Rating</th></tr>
            </thead>
            <tbody>
            @foreach($top_books as $b)
              <tr>
                <td>{{ $b['title'] }}</td>
                <td class="currency">${{ number_format((float)($b['price'] ?? 0), 2) }}</td>
                <td>{{ $b['sales_count'] }}</td>
                <td>{{ $b['total_quantity'] }}</td>
                <td>
                  @if(!empty($b['avg_rating']))
                    {{ number_format($b['avg_rating'], 1) }} <i class="fas fa-star" style="color: var(--warning)"></i>
                  @else
                    N/A
                  @endif
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>

      {{-- USER ACTIVITY --}}
      <div class="section">
        <div class="section-header">
          <div class="section-title">Top Active Users</div>
          <div class="muted">By order count</div>
        </div>
        <div class="canvas-wrap" style="overflow-x:auto;">
          <table>
            <thead>
              <tr><th>Username</th><th>Orders</th><th>Reviews</th><th>Wishlist</th><th>Last Login</th></tr>
            </thead>
            <tbody>
            @foreach($user_activity as $u)
              <tr>
                <td>{{ $u['username'] }}</td>
                <td>{{ $u['order_count'] }}</td>
                <td>{{ $u['review_count'] }}</td>
                <td>{{ $u['wishlist_count'] }}</td>
                <td>{{ $u['last_login'] ? \Carbon\Carbon::parse($u['last_login'])->format('Y-m-d') : 'Never' }}</td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>

      {{-- EVENT PARTICIPATION --}}
      <div class="section">
        <div class="section-header">
          <div class="section-title">Event Participation</div>
          <div class="muted">Recent events</div>
        </div>
        <div class="canvas-wrap" style="overflow-x:auto;">
          <table>
            <thead>
              <tr><th>Event Name</th><th>Date</th><th>Participants</th></tr>
            </thead>
            <tbody>
            @foreach($event_participation as $e)
              <tr>
                <td>{{ $e['event_name'] }}</td>
                <td>{{ \Carbon\Carbon::parse($e['event_date'])->format('Y-m-d') }}</td>
                <td>{{ $e['participant_count'] }}</td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </main>

  <script>
    // Theme toggle
    const themeToggle = document.getElementById('themeToggle');
    const icon = themeToggle.querySelector('i');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (localStorage.getItem('admin-theme') === 'dark' || (!localStorage.getItem('admin-theme') && prefersDark)) {
      document.body.classList.add('admin-dark-mode');
      icon.classList.replace('fa-moon', 'fa-sun');
    }
    themeToggle.addEventListener('click', () => {
      document.body.classList.toggle('admin-dark-mode');
      if (document.body.classList.contains('admin-dark-mode')) {
        localStorage.setItem('admin-theme','dark'); icon.classList.replace('fa-moon','fa-sun');
      } else {
        localStorage.setItem('admin-theme','light'); icon.classList.replace('fa-sun','fa-moon');
      }
      // refresh chart colors
      buildCharts(true);
    });

    // Chart helpers
    function makeGradient(ctx, from, to) {
      const g = ctx.createLinearGradient(0, 0, 0, 300);
      g.addColorStop(0, from);
      g.addColorStop(1, to);
      return g;
    }
    function textColor() { return getComputedStyle(document.body).getPropertyValue('--text').trim(); }
    function gridColor() { return getComputedStyle(document.body).getPropertyValue('--border').trim(); }

    let charts = [];
    function buildCharts(rebuild=false) {
      // Destroy old charts on rebuild
      if (rebuild) { charts.forEach(c => c.destroy()); charts = []; }

      const salesEl = document.getElementById('salesChart');
      const catEl   = document.getElementById('categoriesChart');
      const genEl   = document.getElementById('genresChart');
      const subEl   = document.getElementById('subscriptionChart');

      // SALES (bar + line)
      if (salesEl) {
        const sctx = salesEl.getContext('2d');
        const barGrad = makeGradient(sctx, 'rgba(59,130,246,0.6)', 'rgba(59,130,246,0.05)');
        const lineGrad = makeGradient(sctx, 'rgba(16,185,129,0.6)', 'rgba(16,185,129,0.05)');

        const salesChart = new Chart(sctx, {
          type: 'bar',
          data: {
            labels: @json($sales_chart['labels'] ?? []),
            datasets: [
              {
                label: 'Orders',
                data: @json($sales_chart['orders'] ?? []),
                backgroundColor: barGrad,
                borderColor: 'rgba(59,130,246,1)',
                borderWidth: 1,
                yAxisID: 'y'
              },
              {
                label: 'Revenue ($)',
                data: @json($sales_chart['revenue'] ?? []),
                type: 'line',
                tension: .35,
                fill: true,
                backgroundColor: lineGrad,
                borderColor: 'rgba(16,185,129,1)',
                pointRadius: 2,
                yAxisID: 'y1'
              }
            ]
          },
          options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
              legend: { labels: { color: textColor() }},
              tooltip: {
                callbacks: {
                  label: (ctx) => {
                    const v = ctx.parsed.y;
                    if (ctx.dataset.label.includes('Revenue')) return ` Revenue: $${Number(v ?? 0).toLocaleString()}`;
                    return ` Orders: ${Number(v ?? 0).toLocaleString()}`;
                  }
                }
              }
            },
            scales: {
              x: { grid: { color: gridColor() }, ticks: { color: textColor() }},
              y: { position: 'left', grid: { color: gridColor() }, ticks: { color: textColor() }},
              y1:{ position: 'right', grid: { drawOnChartArea: false, color: gridColor() }, ticks: { color: textColor(),
                callback: (v)=>'$'+Number(v).toLocaleString() } }
            }
          }
        });
        charts.push(salesChart);
      }

      // CATEGORIES (doughnut)
      if (catEl) {
        const cctx = catEl.getContext('2d');
        const labels = @json(array_column($categories_data ?? [], 'category'));
        const data   = @json(array_column($categories_data ?? [], 'book_count'));
        const colors = [
          'rgba(59,130,246,.8)','rgba(99,102,241,.8)','rgba(16,185,129,.8)','rgba(245,158,11,.8)',
          'rgba(239,68,68,.8)','rgba(20,184,166,.8)','rgba(234,88,12,.8)','rgba(168,85,247,.8)',
          'rgba(29,78,216,.8)','rgba(34,197,94,.8)'
        ];
        const categoriesChart = new Chart(cctx, {
          type: 'doughnut',
          data: { labels, datasets: [{ data, backgroundColor: colors, borderWidth: 0 }]},
          options: {
            responsive: true,
            plugins: {
              legend: { position: 'right', labels: { color: textColor() } },
              tooltip: { callbacks: { label: (ctx)=> ` ${ctx.label}: ${ctx.raw}` } }
            },
            cutout: '60%'
          }
        });
        charts.push(categoriesChart);
      }

      // GENRES (polarArea for variety)
      if (genEl) {
        const gctx = genEl.getContext('2d');
        const labels = @json(array_column($genres_data ?? [], 'genre'));
        const data   = @json(array_column($genres_data ?? [], 'book_count'));
        const colors = [
          'rgba(239,68,68,.75)','rgba(20,184,166,.75)','rgba(234,88,12,.75)','rgba(168,85,247,.75)',
          'rgba(29,78,216,.75)','rgba(34,197,94,.75)','rgba(59,130,246,.75)','rgba(99,102,241,.75)',
          'rgba(16,185,129,.75)','rgba(245,158,11,.75)'
        ];
        const genresChart = new Chart(gctx, {
          type: 'polarArea',
          data: { labels, datasets: [{ data, backgroundColor: colors, borderWidth: 0 }]},
          options: {
            responsive: true,
            plugins: {
              legend: { position: 'right', labels: { color: textColor() } }
            },
            scales: { r: { grid: { color: gridColor() }, pointLabels: { color: textColor() }, ticks: { color: textColor() } } }
          }
        });
        charts.push(genresChart);
      }

      // SUBSCRIPTIONS (stacked bars + line revenue)
      if (subEl) {
        const s2ctx = subEl.getContext('2d');
        const labels = @json(array_map(fn($m)=> \Carbon\Carbon::parse($m['month'].'-01')->format('M Y'), $subscription_monthly_data ?? []));
        const newSubs = @json(array_column($subscription_monthly_data ?? [], 'new_subs'));
        const renewals = @json(array_column($subscription_monthly_data ?? [], 'renewals'));
        const revenue = @json(array_column($subscription_monthly_data ?? [], 'revenue'));

        const revGrad = makeGradient(s2ctx, 'rgba(16,185,129,.55)', 'rgba(16,185,129,.05)');

        const subscriptionChart = new Chart(s2ctx, {
          type: 'bar',
          data: {
            labels,
            datasets: [
              { label: 'New', data: newSubs, backgroundColor: 'rgba(59,130,246,.8)', stack: 'subs', yAxisID: 'y' },
              { label: 'Renewals', data: renewals, backgroundColor: 'rgba(99,102,241,.8)', stack: 'subs', yAxisID: 'y' },
              { label: 'Revenue ($)', data: revenue, type: 'line', tension: .35, fill: true,
                backgroundColor: revGrad, borderColor: 'rgba(16,185,129,1)', pointRadius: 2, yAxisID: 'y1' }
            ]
          },
          options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
              legend: { labels: { color: textColor() }},
              tooltip: {
                callbacks: {
                  label: (ctx) => {
                    const v = ctx.parsed.y ?? 0;
                    if (ctx.dataset.label.includes('Revenue')) return ` Revenue: $${Number(v).toLocaleString()}`;
                    return ` ${ctx.dataset.label}: ${Number(v).toLocaleString()}`;
                  }
                }
              }
            },
            scales: {
              x: { stacked: true, grid: { color: gridColor() }, ticks: { color: textColor() }},
              y: { stacked: true, position: 'left', grid: { color: gridColor() }, ticks: { color: textColor() }},
              y1:{ position: 'right', grid: { drawOnChartArea: false, color: gridColor() }, ticks: { color: textColor(),
                callback: (v)=>'$'+Number(v).toLocaleString() } }
            }
          }
        });
        charts.push(subscriptionChart);
      }
    }

    // Initial build
    document.addEventListener('DOMContentLoaded', () => buildCharts(false));
  </script>
</body>
</html>
