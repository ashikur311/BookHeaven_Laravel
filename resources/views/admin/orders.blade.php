<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Orders</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    /* ========= SAME THEME TOKENS AS REPORTS ========= */
    :root{
      --bg:#f5f7fb; --text:#1f2937; --text-muted:#6b7280;
      --primary:#3b82f6; --success:#10b981; --warning:#f59e0b; --danger:#ef4444; --purple:#8b5cf6;
      --card:#ffffff; --border:#e5e7eb; --table-header:#f3f4f6; --table-hover:#f9fafb; --chip:#eef2ff;
      --shadow:0 1px 1px rgba(0,0,0,.02);
    }
    body.admin-dark-mode{
      --bg:#0f172a; --text:#e5e7eb; --text-muted:#9ca3af;
      --primary:#60a5fa; --success:#34d399; --warning:#fbbf24; --danger:#f87171; --purple:#a78bfa;
      --card:#111827; --border:#1f2937; --table-header:#0b1220; --table-hover:#0c1427; --chip:#1f2937;
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
    .admin_header_right h1{font-size:1.1rem; font-weight:600}
    .admin_theme_toggle{
      background:transparent; border:1px solid rgba(255,255,255,.2); color:#fff;
      padding:.5rem .65rem; border-radius:.5rem; cursor:pointer;
    }
    .chip{ font-size:.75rem; padding:.25rem .5rem; border-radius:999px; background:var(--chip); color:var(--text-muted) }

    /* ========= LAYOUT / SIDEBAR ========= */
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

    /* ========= SECTIONS ========= */
    .section{ background:var(--card); border:1px solid var(--border); border-radius:12px; box-shadow:var(--shadow); margin-bottom:1rem }
    .section-head{
      padding:1rem 1.25rem; border-bottom:1px solid var(--border);
      display:flex; align-items:center; justify-content:space-between; gap:1rem;
    }
    .title{ font-weight:800; font-size:1.05rem }
    .muted{ color:var(--text-muted) }
    .pad{ padding:1.25rem }

    /* ========= STATS (bold + colored accents) ========= */
    .grid{ display:grid; gap:1rem }
    .grid.stats{ grid-template-columns:repeat(6,minmax(0,1fr)) }
    @media (max-width:1100px){ .grid.stats{ grid-template-columns:repeat(3,minmax(0,1fr)) } }
    @media (max-width:900px){ .admin_main{ grid-template-columns:1fr } .admin_sidebar{ display:none } .grid.stats{ grid-template-columns:repeat(2,minmax(0,1fr)) } }
    .stat{
      background:var(--card); border:1px solid var(--border); border-left-width:4px; border-radius:12px; padding:1rem 1.25rem;
      display:grid; gap:.25rem;
    }
    .stat .k{ font-size:.85rem; color:var(--text-muted); font-weight:700 }
    .stat .v{ font-size:1.9rem; font-weight:800 }
    .stat.total{ border-left-color:var(--primary) }
    .stat.pending{ border-left-color:var(--warning) }
    .stat.confirmed{ border-left-color:var(--primary) }
    .stat.shipped{ border-left-color:var(--purple) }
    .stat.delivered{ border-left-color:var(--success) }
    .stat.cancelled{ border-left-color:var(--danger) }

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

    /* ========= TABLES ========= */
    table{ width:100%; border-collapse:collapse }
    thead th{ background:var(--table-header); color:var(--text-muted); font-weight:800; text-align:left }
    th, td{ padding:.9rem 1rem; border-bottom:1px solid var(--border) }
    tbody tr:hover{ background:var(--table-hover) }

    .status{ font-weight:800 }
    .status-pending{ color:var(--warning) }
    .status-confirmed{ color:var(--primary) }
    .status-shipped{ color:var(--purple) }
    .status-delivered{ color:var(--success) }
    .status-cancelled{ color:var(--danger) }

    /* ========= ACTIONS ========= */
    .actions{ display:flex; gap:.5rem; flex-wrap:wrap }
    .action{ padding:.45rem .75rem; border-radius:.5rem; border:1px solid var(--border); background:transparent; cursor:pointer; font-weight:800 }
    .action.view{ border-color:rgba(59,130,246,.35); color:var(--primary) }
    .action.ok{ border-color:rgba(16,185,129,.35); color:var(--success) }
    .action.warn{ border-color:rgba(245,158,11,.35); color:var(--warning) }
    .action.purple{ border-color:rgba(139,92,246,.35); color:var(--purple) }
    .action.red{ border-color:rgba(239,68,68,.35); color:var(--danger) }
    .action:hover{ filter:brightness(.95) }

    /* ========= ALERTS ========= */
    .alert{ padding:1rem; border-radius:.75rem; margin-bottom:1rem; border:1px solid var(--border) }
    .alert-error{ background:#fee2e2; color:#991b1b; border-color:#fecaca }
    .alert-success{ background:#dcfce7; color:#065f46; border-color:#bbf7d0 }
    body.admin-dark-mode .alert-error{ background:#3a1a1d; color:#ffb8c6; border-color:#4d2227 }
    body.admin-dark-mode .alert-success{ background:#1a3a24; color:#a3ffc2; border-color:#224d2e }

    /* ========= MODAL (order items) with colored border ========= */
    .admin_modal{ display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:60; align-items:center; justify-content:center; padding:1rem }
    .admin_modal_content{
      background:var(--card); color:var(--text); border:2px solid var(--primary); border-radius:12px;
      width:min(900px, 96vw); max-height:85vh; overflow:auto; box-shadow:0 10px 30px rgba(0,0,0,.25);
    }
    .admin_modal_head{ display:flex; align-items:center; justify-content:space-between; padding:1rem 1.25rem; border-bottom:2px solid var(--primary); position:sticky; top:0; background:var(--card) }
    .admin_modal_body{ padding:1rem 1.25rem }
    .admin_modal_close{ border:none; background:transparent; font-size:1.3rem; color:var(--text-muted); cursor:pointer }

    .items-grid{ display:grid; gap:.75rem }
    .item{ display:flex; gap:.9rem; align-items:center; background:var(--table-hover); border:1px solid var(--border); border-radius:.65rem; padding:.75rem }
    .cover{ width:56px; height:72px; border-radius:.5rem; overflow:hidden; background:#e5e7eb }
    .cover img{ width:100%; height:100%; object-fit:cover }
    .meta{ font-size:.9rem; color:var(--text-muted) }
    .price{ min-width:210px; text-align:right }
    .total-box{ border-top:1px dashed var(--border); margin-top:1rem; padding-top:.75rem; display:flex; justify-content:flex-end }
    .totals{ min-width:300px }
    .trow{ display:flex; justify-content:space-between; margin:.25rem 0 }
    .trow.big{ font-weight:800; border-top:1px solid var(--border); margin-top:.5rem; padding-top:.5rem }

    .skeleton{ background:linear-gradient(90deg,#eee 25%,#f5f5f5 37%,#eee 63%); animation:skeleton 1.4s ease infinite; background-size:400% 100%; border-radius:.5rem }
    @keyframes skeleton{0%{background-position:100% 50%}to{background-position:0 50%}}
  </style>
</head>
<body>
  <!-- HEADER -->
  <header class="admin_header">
    <div class="logo"><img src="{{ asset('assets/images/download.png') }}" alt="Logo"></div>
    <div class="admin_header_right">
      <h1>Orders</h1>
      <span class="chip">Manage & fulfill</span>
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
          <li><a href="{{ route('admin.orders') }}" class="active"><i class="fas fa-shopping-cart"></i> Orders</a></li>
          <li><a href="{{ route('admin.subscription') }}"><i class="fas fa-star"></i> Subscription</a></li>
          <li><a href="{{ route('admin.events') }}"><i class="fas fa-calendar-alt"></i> Events</a></li>
          <li><a href="{{ route('admin.question') }}"><i class="fa-solid fa-question"></i> User Questions</a></li>
          <li><a href="{{ route('admin.community') }}"><i class="fas fa-users"></i> Community</a></li>
          <li><a href="{{ route('admin.reports') }}"><i class="fas fa-chart-bar"></i> Reports</a></li>
          <li>
            <form method="POST" action="{{ route('admin.logout') }}">
              @csrf
              <button type="submit" class="linklike" style="all:unset;display:flex;align-items:center;gap:.75rem;padding:.65rem .75rem;border-radius:.5rem;cursor:pointer">
                <i class="fas fa-sign-out-alt"></i> Logout
              </button>
            </form>
          </li>
        </ul>
      </nav>
    </aside>

    <!-- CONTENT -->
    <div class="admin_main_content">
      @if(session('error'))   <div class="alert alert-error">{{ session('error') }}</div> @endif
      @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
      @if(!empty($error_message)) <div class="alert alert-error">{{ $error_message }}</div> @endif

      <!-- STATS -->
      <div class="section">
        <div class="section-head">
          <div class="title">Order Statistics</div>
          <span class="muted">Live snapshot</span>
        </div>
        <div class="pad">
          <div class="grid stats">
            <div class="stat total"><div class="k">Total Orders</div><div class="v">{{ number_format($stats['total_orders']) }}</div></div>
            <div class="stat pending"><div class="k">Pending</div><div class="v">{{ number_format($stats['pending']) }}</div></div>
            <div class="stat confirmed"><div class="k">Confirmed</div><div class="v">{{ number_format($stats['confirmed']) }}</div></div>
            <div class="stat shipped"><div class="k">Shipped</div><div class="v">{{ number_format($stats['shipped']) }}</div></div>
            <div class="stat delivered"><div class="k">Delivered</div><div class="v">{{ number_format($stats['delivered']) }}</div></div>
            <div class="stat cancelled"><div class="k">Cancelled</div><div class="v">{{ number_format($stats['cancelled']) }}</div></div>
          </div>
        </div>
      </div>

      <!-- TOOLBAR: FILTERS (search + status + date range) -->
      <div class="section">
        <div class="section-head">
          <div class="title">Filter & Search</div>
          <span class="muted">Find orders fast</span>
        </div>
        <div class="pad">
          <div class="toolbar">
            <div class="control"><i class="fa-solid fa-magnifying-glass"></i><input id="searchInput" type="text" placeholder="Search user, order id, address…"></div>
            <div class="control">
              <i class="fa-solid fa-filter"></i>
              <select id="statusFilter">
                <option value="all">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
            <div class="control">
              <i class="fa-solid fa-calendar-day"></i>
              <input id="dateFrom" type="date" title="From date">
            </div>
            <div class="control">
              <i class="fa-solid fa-calendar-day"></i>
              <input id="dateTo" type="date" title="To date">
            </div>
            <button class="btn ghost" id="resetFilters"><i class="fa-solid fa-rotate"></i> Reset</button>
          </div>
        </div>
      </div>

      @php
        $sections = [
          'pending'   => ['title'=>'Pending Orders',   'class'=>'status-pending',   'actions'=>['confirm','cancel','edit']],
          'confirmed' => ['title'=>'Confirmed Orders', 'class'=>'status-confirmed', 'actions'=>['ship','cancel','edit']],
          'shipped'   => ['title'=>'Shipped Orders',   'class'=>'status-shipped',   'actions'=>['deliver','edit']],
          'delivered' => ['title'=>'Delivered Orders', 'class'=>'status-delivered', 'actions'=>['edit','delete']],
          'cancelled' => ['title'=>'Cancelled Orders', 'class'=>'status-cancelled', 'actions'=>['edit','delete']],
        ];
      @endphp

      @foreach($sections as $status => $meta)
        <div class="section">
          <div class="section-head">
            <div class="title">{{ $meta['title'] }}</div>
            <span class="muted">Latest first</span>
          </div>
          <div class="pad" style="overflow-x:auto">
            @if($orders_by_status[$status]->isNotEmpty())
              <table>
                <thead>
                  <tr>
                    <th>Order ID</th>
                    <th>User</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                @foreach($orders_by_status[$status] as $order)
                  @php
                    $isoDate = \Carbon\Carbon::parse($order->order_date)->format('Y-m-d');
                  @endphp
                  <tr
                    data-status="{{ $order->status }}"
                    data-search="{{ Str::lower('#'.$order->order_id.' '.$order->username.' '.$order->shipping_address.' '.$order->payment_method) }}"
                    data-date="{{ $isoDate }}"
                  >
                    <td>#{{ $order->order_id }}</td>
                    <td>{{ $order->username }} (ID: {{ $order->user_id }})</td>
                    <td>${{ number_format($order->total_amount, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($order->order_date)->format('M j, Y') }}</td>
                    <td class="status {{ $meta['class'] }}">{{ ucfirst($order->status) }}</td>
                    <td>
                      <div class="actions">
                        <button type="button"
                                class="action view"
                                title="View items"
                                data-items-url="{{ route('admin.orders.items', $order->order_id) }}"
                                data-order-id="{{ $order->order_id }}"
                                data-username="{{ $order->username }}"
                                onclick="openOrderItemsModal(this)">
                          <i class="fa-solid fa-receipt"></i> View Items
                        </button>

                        @if(in_array('confirm', $meta['actions']))
                          <form method="POST" action="{{ route('admin.orders.updateStatus') }}">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                            <input type="hidden" name="new_status" value="confirmed">
                            <button type="submit" class="action ok"><i class="fa-solid fa-check"></i> Confirm</button>
                          </form>
                        @endif

                        @if(in_array('ship', $meta['actions']))
                          <form method="POST" action="{{ route('admin.orders.updateStatus') }}">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                            <input type="hidden" name="new_status" value="shipped">
                            <button type="submit" class="action purple"><i class="fa-solid fa-truck"></i> Ship</button>
                          </form>
                        @endif

                        @if(in_array('deliver', $meta['actions']))
                          <form method="POST" action="{{ route('admin.orders.updateStatus') }}">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                            <input type="hidden" name="new_status" value="delivered">
                            <button type="submit" class="action ok"><i class="fa-solid fa-box-open"></i> Deliver</button>
                          </form>
                        @endif

                        @if(in_array('cancel', $meta['actions']))
                          <form method="POST" action="{{ route('admin.orders.updateStatus') }}">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                            <input type="hidden" name="new_status" value="cancelled">
                            <button type="submit" class="action warn"><i class="fa-solid fa-ban"></i> Cancel</button>
                          </form>
                        @endif

                        @if(in_array('edit', $meta['actions']))
                          <a class="action" href="{{ route('admin.orders.edit', $order->order_id) }}"><i class="fa-solid fa-pen"></i> Edit</a>
                        @endif

                        @if(in_array('delete', $meta['actions']))
                          <form method="POST" action="{{ route('admin.orders.delete') }}" onsubmit="return confirm('Delete this order?');">
                            @csrf
                            <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                            <button type="submit" class="action red"><i class="fa-solid fa-trash"></i> Delete</button>
                          </form>
                        @endif
                      </div>
                    </td>
                  </tr>
                @endforeach
                </tbody>
              </table>
            @else
              <div class="muted">No {{ $status }} orders found.</div>
            @endif
          </div>
        </div>
      @endforeach

    </div>
  </main>

  <!-- ITEMS MODAL -->
  <div id="orderItemsModal" class="admin_modal" role="dialog" aria-modal="true" aria-labelledby="orderItemsTitle">
    <div class="admin_modal_content" role="document">
      <div class="admin_modal_head">
        <h3 id="orderItemsTitle" class="title">Order Items</h3>
        <button class="admin_modal_close" onclick="closeOrderItemsModal()" aria-label="Close">&times;</button>
      </div>
      <div class="admin_modal_body">
        <div id="orderItemsHeader" class="muted" style="margin-bottom:.75rem;"></div>

        <!-- Loading skeleton -->
        <div id="orderItemsLoading" style="display:none;gap:.5rem" class="items-grid" aria-hidden="true">
          <div class="item">
            <div class="cover skeleton"></div>
            <div style="flex:1">
              <div class="skeleton" style="height:14px;width:60%;margin-bottom:.35rem;"></div>
              <div class="skeleton" style="height:12px;width:40%;"></div>
            </div>
            <div class="price" style="min-width:210px">
              <div class="skeleton" style="height:12px;"></div>
              <div class="skeleton" style="height:12px;margin-top:.25rem;"></div>
            </div>
          </div>
        </div>

        <!-- Items -->
        <div id="orderItemsList" class="items-grid"></div>

        <!-- Empty -->
        <div id="orderItemsEmpty" style="display:none;text-align:center;padding:1.25rem;color:#777;">
          No line items found.
        </div>

        <!-- Totals -->
        <div class="total-box">
          <div id="orderItemsTotals" class="totals" style="display:none;">
            <div class="trow"><span>Subtotal</span><strong id="tSubtotal">$0.00</strong></div>
            <div class="trow"><span>Tax</span><strong id="tTax">$0.00</strong></div>
            <div class="trow"><span>Shipping</span><strong id="tShipping">$0.00</strong></div>
            <div class="trow big"><span>Total</span><strong id="tGrand">$0.00</strong></div>
          </div>
        </div>
      </div>
    </div>
  </div>

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

    // ========= Client-side Filters: text + status + date =========
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const dateFrom = document.getElementById('dateFrom');
    const dateTo = document.getElementById('dateTo');
    const resetFilters = document.getElementById('resetFilters');

    function inDateRange(dateStr, fromStr, toStr){
      if(!dateStr) return true;
      const d = new Date(dateStr + 'T00:00:00');
      if(fromStr){
        const f = new Date(fromStr + 'T00:00:00');
        if(d < f) return false;
      }
      if(toStr){
        const t = new Date(toStr + 'T23:59:59');
        if(d > t) return false;
      }
      return true;
    }

    function filterRows(){
      const term = (searchInput?.value || '').toLowerCase().trim();
      const stat = (statusFilter?.value || 'all');
      const from = dateFrom?.value || '';
      const to   = dateTo?.value || '';

      document.querySelectorAll('tbody tr[data-status]').forEach(tr=>{
        const hay = (tr.getAttribute('data-search') || '').toLowerCase();
        const s   = tr.getAttribute('data-status') || '';
        const dt  = tr.getAttribute('data-date') || '';

        const okText = !term || hay.includes(term);
        const okStat = stat === 'all' || s === stat;
        const okDate = inDateRange(dt, from, to);

        tr.style.display = (okText && okStat && okDate) ? '' : 'none';
      });
    }
    [searchInput, statusFilter, dateFrom, dateTo].forEach(el => el?.addEventListener('input', filterRows));
    statusFilter?.addEventListener('change', filterRows);
    resetFilters?.addEventListener('click', ()=>{
      if (searchInput) searchInput.value='';
      if (statusFilter) statusFilter.value='all';
      if (dateFrom) dateFrom.value='';
      if (dateTo) dateTo.value='';
      filterRows();
    });

    // ========= Items modal =========
    const modal = document.getElementById('orderItemsModal');
    const elHeader = document.getElementById('orderItemsHeader');
    const elList = document.getElementById('orderItemsList');
    const elEmpty = document.getElementById('orderItemsEmpty');
    const elTotals = document.getElementById('orderItemsTotals');
    const elLoad = document.getElementById('orderItemsLoading');

    function money(n){ return new Intl.NumberFormat(undefined,{style:'currency',currency:'USD'}).format(Number(n||0)); }

    function openOrderItemsModal(btn){
      const url = btn.dataset.itemsUrl;
      const orderId = btn.dataset.orderId;
      const username = btn.dataset.username || '';

      document.getElementById('orderItemsTitle').textContent = `Order #${orderId} Items`;
      elHeader.textContent = `Loading items for ${username}…`;
      elList.innerHTML = '';
      elEmpty.style.display = 'none';
      elTotals.style.display = 'none';
      elLoad.style.display = 'grid';

      modal.style.display = 'flex';

      fetch(url, { headers: { 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' }})
        .then(res=>{ if(!res.ok) throw new Error('Failed to load items'); return res.json(); })
        .then(data=>{
          elLoad.style.display = 'none';

          if (data && data.order){
            const o = data.order;
            const dateStr = o.order_date ? new Date(o.order_date).toLocaleString() : '';
            elHeader.textContent = `Placed ${dateStr} • Payment: ${o.payment_method || 'N/A'} • Ship to: ${o.shipping_address || 'N/A'}`;
          } else {
            elHeader.textContent = '';
          }

          const items = (data && data.items) ? data.items : [];
          if(!items.length){ elEmpty.style.display='block'; return; }

          items.forEach(it=>{
            const title = it.title || it.book_title || 'Untitled';
            const qty = Number(it.quantity || it.qty || 1);
            const unit = Number(it.unit_price ?? it.price ?? 0);
            const total = Number(it.line_total ?? (qty*unit));
            const cover = it.poster_url || it.cover_url || '';

            const row = document.createElement('div');
            row.className = 'item';
            row.innerHTML = `
              <div class="cover">${cover ? `<img src="${cover}" alt="">` : ''}</div>
              <div style="flex:1">
                <div style="font-weight:800;margin-bottom:.15rem">${title}</div>
                <div class="meta">Qty: ${qty}</div>
              </div>
              <div class="price">
                <div>Unit: <strong>${money(unit)}</strong></div>
                <div>Line total: <strong>${money(total)}</strong></div>
              </div>
            `;
            elList.appendChild(row);
          });

          const t = data.totals || {};
          let subtotal = t.subtotal ?? items.reduce((s,it)=>{
            const q = Number(it.quantity || 1);
            const u = Number(it.unit_price ?? it.price ?? 0);
            return s + (q*u);
          },0);
          const tax = Number(t.tax ?? 0);
          const shipping = Number(t.shipping ?? 0);
          const grand = Number(t.total ?? (subtotal + tax + shipping));

          document.getElementById('tSubtotal').textContent = money(subtotal);
          document.getElementById('tTax').textContent = money(tax);
          document.getElementById('tShipping').textContent = money(shipping);
          document.getElementById('tGrand').textContent = money(grand);
          elTotals.style.display = 'block';
        })
        .catch(err=>{
          console.error(err);
          elLoad.style.display='none';
          elList.innerHTML='';
          elHeader.textContent='Could not load order items.';
          elEmpty.style.display='block';
        });
    }
    function closeOrderItemsModal(){ modal.style.display='none'; }
    window.addEventListener('click',e=>{ if(e.target===modal) closeOrderItemsModal(); });
    window.addEventListener('keydown',e=>{ if(e.key==='Escape') closeOrderItemsModal(); });
  </script>
</body>
</html>
