<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Edit Order #{{ $order->order_id }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    /* ========= SAME THEME TOKENS AS ORDERS/REPORTS ========= */
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
      background:var(--bg); color:var(--text); line-height:1.55;
    }
    a{color:inherit;text-decoration:none}

    /* ========= HEADER (same as Orders) ========= */
    .admin_header{
      display:flex; justify-content:space-between; align-items:center;
      padding:1rem 1.5rem; background:#1f2937; color:#fff;
      position:sticky; top:0; z-index:30; border-bottom:1px solid rgba(255,255,255,.06);
    }
    .logo img{height:40px}
    .admin_header_right{display:flex;align-items:center;gap:.75rem}
    .admin_header_right h1{font-size:1.1rem;font-weight:600}
    .admin_theme_toggle{
      background:transparent;border:1px solid rgba(255,255,255,.2);color:#fff;
      padding:.5rem .65rem;border-radius:.5rem;cursor:pointer;
    }
    .chip{font-size:.75rem;padding:.25rem .5rem;border-radius:999px;background:var(--chip);color:var(--text-muted)}

    /* ========= LAYOUT / SIDEBAR (sticky + scroll) ========= */
    .admin_main{display:grid;grid-template-columns:260px 1fr;min-height:calc(100vh - 64px)}
    .admin_sidebar{
      background:#111827;color:#cbd5e1;border-right:1px solid rgba(255,255,255,.06);
      position:sticky;top:64px;align-self:start;height:calc(100vh - 64px);overflow-y:auto;
    }
    .admin_sidebar_nav ul{list-style:none;padding:.75rem}
    .admin_sidebar_nav a, .admin_sidebar_nav button.linklike{
      display:flex;align-items:center;gap:.75rem;padding:.65rem .75rem;margin-bottom:.25rem;
      color:inherit;border-radius:.5rem;border:none;background:transparent;width:100%;text-align:left;cursor:pointer;
    }
    .admin_sidebar_nav a:hover, .admin_sidebar_nav button.linklike:hover{background:rgba(255,255,255,.06)}
    .admin_sidebar_nav a.active{background:rgba(59,130,246,.18);color:#fff}

    .admin_main_content{padding:1.5rem}

    /* ========= CARDS / SECTIONS ========= */
    .section{background:var(--card);border:1px solid var(--border);border-radius:12px;box-shadow:var(--shadow);margin-bottom:1rem}
    .section-head{
      padding:1rem 1.25rem;border-bottom:1px solid var(--border);
      display:flex;align-items:center;justify-content:space-between;gap:1rem;
    }
    .title{font-weight:800;font-size:1.05rem}
    .muted{color:var(--text-muted)}
    .pad{padding:1.25rem}

    .grid{display:grid;gap:1rem}
    .two{grid-template-columns:1fr 1fr}
    @media (max-width:1024px){ .admin_main{grid-template-columns:1fr} .admin_sidebar{display:none} }
    @media (max-width:900px){ .two{grid-template-columns:1fr} }

    .btn{padding:.6rem .9rem;border:none;border-radius:.55rem;cursor:pointer;font-weight:800}
    .btn.primary{background:var(--primary);color:#fff}
    .btn.ghost{background:transparent;border:1px solid var(--border);color:var(--text)}
    .btn.red{background:var(--danger);color:#fff}

    .form-group{display:grid;gap:.4rem}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:1rem}
    .form-control{padding:.7rem .75rem;border:1px solid var(--border);border-radius:.55rem;background:transparent;color:inherit}
    .form-control:focus{outline:none;border-color:var(--primary)}
    textarea.form-control{min-height:110px;resize:vertical}

    /* Badges + progress */
    .badge{display:inline-flex;align-items:center;gap:.4rem;padding:.25rem .55rem;border-radius:.5rem;font-weight:800;font-size:.8rem;background:var(--chip);color:var(--text-muted)}
    .status-pending{color:var(--warning)}
    .status-confirmed{color:var(--primary)}
    .status-shipped{color:var(--purple)}
    .status-delivered{color:var(--success)}
    .status-cancelled{color:var(--danger)}

    .progress{display:flex;gap:.5rem;align-items:center;margin-top:.5rem}
    .step{display:flex;align-items:center;gap:.4rem}
    .dot{width:10px;height:10px;border-radius:50%;background:var(--border)}
    .step.done .dot{background:var(--primary)}
    .bar{height:2px;background:var(--border);flex:1;border-radius:1px}
    .step.done + .bar{background:var(--primary)}

    /* Table */
    table{width:100%;border-collapse:collapse}
    thead th{background:var(--table-header);color:var(--text-muted);font-weight:800;text-align:left}
    th,td{padding:1rem;border-bottom:1px solid var(--border)}
    tbody tr:hover{background:var(--table-hover)}
    .qty{display:inline-block;min-width:34px;text-align:center;background:#eef2ff;color:#3730a3;border-radius:999px;padding:.15rem .5rem;font-weight:800}
    .right{text-align:right}
    .bold{font-weight:800}

    /* Alerts */
    .alert{padding:1rem;border-radius:.75rem;margin-bottom:1rem;border:1px solid var(--border)}
    .alert-error{background:#fee2e2;color:#991b1b;border-color:#fecaca}
    .alert-success{background:#dcfce7;color:#065f46;border-color:#bbf7d0}
    body.admin-dark-mode .alert-error{background:#3a1a1d;color:#ffb8c6;border-color:#4d2227}
    body.admin-dark-mode .alert-success{background:#1a3a24;color:#a3ffc2;border-color:#224d2e}

    /* Sticky aside summary */
    .sticky{position:sticky;top:1rem;align-self:start}
  </style>
</head>
<body>
  <!-- HEADER -->
  <header class="admin_header">
    <div class="logo"><img src="{{ asset('assets/images/download.png') }}" alt="Logo"></div>
    <div class="admin_header_right">
      <h1>Edit Order</h1>
      <span class="chip">#{{ $order->order_id }}</span>
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

      {{-- ALERTS --}}
      @if(session('error'))   <div class="alert alert-error">{{ session('error') }}</div> @endif
      @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
      @if ($errors->any())
        <div class="alert alert-error">
          <strong>There were some issues with your input:</strong>
          <ul style="margin-top:.5rem;padding-left:1.25rem">
            @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
          </ul>
        </div>
      @endif

      <div class="grid" style="grid-template-columns: 1fr 360px; gap:1rem;">
        <!-- LEFT COLUMN -->
        <div class="grid">
          <!-- Order header actions -->
          <div class="section">
            <div class="section-head">
              <div>
                <div class="title">Order #{{ $order->order_id }}</div>
                <div class="muted">Placed {{ \Carbon\Carbon::parse($order->order_date)->format('M j, Y \a\t g:i a') }}</div>
              </div>
              <a href="{{ route('admin.orders') }}" class="btn ghost"><i class="fa fa-arrow-left"></i> Back to Orders</a>
            </div>

            <div class="pad">
              <form method="POST" action="{{ route('admin.orders.update', $order->order_id) }}" class="grid two">
                @csrf

                <!-- Order Info -->
                <div class="grid">
                  <div class="title" style="margin-bottom:.5rem">Order Information</div>

                  <div class="form-row">
                    <div class="form-group">
                      <label for="status">Order Status</label>
                      <select id="status" name="status" class="form-control">
                        @foreach(['pending','confirmed','shipped','delivered','cancelled'] as $s)
                          <option value="{{ $s }}" @selected(old('status',$order->status)===$s)>{{ ucfirst($s) }}</option>
                        @endforeach
                      </select>
                    </div>

                    <div class="form-group">
                      <label for="payment_method">Payment Method</label>
                      <select id="payment_method" name="payment_method" class="form-control">
                        <option value="cod" @selected(old('payment_method',$order->payment_method)==='cod')>Cash on Delivery</option>
                        <option value="online" @selected(old('payment_method',$order->payment_method)==='online')>Online Payment</option>
                      </select>
                    </div>
                  </div>

                  <div class="form-group">
                    <label for="shipping_address">Shipping Address</label>
                    <textarea id="shipping_address" name="shipping_address" class="form-control" placeholder="Street, City, State, ZIP">{{ old('shipping_address', $order->shipping_address) }}</textarea>
                  </div>

                  <div class="form-group">
                    <label for="notes">Admin Notes</label>
                    <textarea id="notes" name="notes" class="form-control" placeholder="Internal notes (not visible to the customer)">{{ old('notes', $order->notes ?? '') }}</textarea>
                  </div>
                </div>

                <!-- Customer Info -->
                <div class="grid">
                  <div class="title" style="margin-bottom:.5rem">Customer Information</div>

                  <div class="form-group">
                    <label>Customer Name</label>
                    <div class="muted">{{ $order->username }}</div>
                  </div>

                  <div class="form-group">
                    <label>Customer Email</label>
                    <div class="muted">{{ $order->email }}</div>
                  </div>

                  <div class="form-group">
                    <label>Order Date</label>
                    <div class="muted">{{ \Carbon\Carbon::parse($order->order_date)->format('M j, Y g:i a') }}</div>
                  </div>

                  <div class="form-group">
                    <label>Current Status</label>
                    @php
                      $statusClass = 'status-'.$order->status;
                      $steps = ['pending','confirmed','shipped','delivered'];
                      $idx = array_search($order->status, $steps);
                      if ($idx === false) $idx = 0;
                    @endphp
                    <span class="badge {{ $statusClass }}"><i class="fa fa-circle"></i> {{ ucfirst($order->status) }}</span>

                    <div class="progress" aria-hidden="true">
                      @foreach($steps as $i => $step)
                        <div class="step {{ $i <= $idx ? 'done' : '' }}"><div class="dot"></div><small class="muted" style="text-transform:capitalize">{{ $step }}</small></div>
                        @if($i < count($steps)-1)<div class="bar"></div>@endif
                      @endforeach
                    </div>
                  </div>
                </div>

                <div style="grid-column:1/-1; display:flex; gap:.6rem; justify-content:flex-end; margin-top:.5rem">
                  <a href="{{ route('admin.orders') }}" class="btn ghost"><i class="fa fa-times"></i> Cancel</a>
                  <button type="submit" class="btn primary"><i class="fa fa-save"></i> Update Order</button>
                </div>
              </form>
            </div>
          </div>

          <!-- Items -->
          <div class="section">
            <div class="section-head">
              <div class="title">Order Items</div>
              <span class="muted">Read-only</span>
            </div>

            <div class="pad" style="padding:0">
              <table>
                <thead>
                  <tr>
                    <th>Book</th>
                    <th style="width:140px">Quantity</th>
                    <th style="width:160px">Price</th>
                    <th class="right" style="width:180px">Line Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  @php $computedSubtotal = 0; @endphp
                  @forelse($order_items as $item)
                    @php
                      $qty = (int) $item->quantity;
                      $unit = (float) $item->price;
                      $line = $qty * $unit;
                      $computedSubtotal += $line;
                    @endphp
                    <tr>
                      <td>{{ $item->title ?? 'Untitled' }}</td>
                      <td><span class="qty">{{ $qty }}</span></td>
                      <td>${{ number_format($unit, 2) }}</td>
                      <td class="right bold">${{ number_format($line, 2) }}</td>
                    </tr>
                  @empty
                    <tr><td colspan="4" class="muted">No line items found for this order.</td></tr>
                  @endforelse
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="3" class="right bold" style="padding-right:1rem">Order Total:</td>
                    <td class="right bold">${{ number_format($order->total_amount, 2) }}</td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
        </div>

        <!-- RIGHT COLUMN (Sticky Summary / Quick Actions) -->
        <aside class="grid sticky" style="gap:1rem">
          <div class="section">
            <div class="section-head"><div class="title">Summary</div></div>
            <div class="pad">
              <div class="grid" style="grid-template-columns: 140px 1fr; gap:.5rem 1rem">
                <div class="muted">Order #</div>  <div class="right">{{ $order->order_id }}</div>
                <div class="muted">User</div>      <div class="right">{{ $order->username }}</div>
                <div class="muted">Email</div>     <div class="right">{{ $order->email }}</div>
                <div class="muted">Payment</div>   <div class="right" style="text-transform:uppercase">{{ $order->payment_method }}</div>
                <div class="muted">Placed</div>    <div class="right">{{ \Carbon\Carbon::parse($order->order_date)->format('M j, Y g:i a') }}</div>
              </div>

              <hr style="border:none;border-top:1px solid var(--border);margin:.9rem 0">

              <div class="grid" style="grid-template-columns: 1fr 1fr; gap:.5rem">
                <div class="muted">Items Subtotal</div>
                <div class="right">${{ number_format(($order_items->sum(fn($i)=>$i->price*$i->quantity)), 2) }}</div>
                <div class="muted">Shipping</div>
                <div class="right">—</div>
                <div class="muted">Tax</div>
                <div class="right">—</div>
                <div class="bold">Total</div>
                <div class="right bold">${{ number_format($order->total_amount, 2) }}</div>
              </div>
            </div>
          </div>

          @php
            $nextMap = ['pending'=>'confirmed','confirmed'=>'shipped','shipped'=>'delivered'];
            $next = $nextMap[$order->status] ?? null;
          @endphp
          @if($next && $order->status !== 'cancelled')
            <form method="POST" action="{{ route('admin.orders.updateStatus') }}" class="section">
              @csrf
              <div class="section-head"><div class="title">Quick Action</div></div>
              <div class="pad" style="display:flex;align-items:center;justify-content:space-between;gap:.5rem">
                <div class="muted">Advance to <strong style="text-transform:capitalize">{{ $next }}</strong></div>
                <div>
                  <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                  <input type="hidden" name="new_status" value="{{ $next }}">
                  <button class="btn primary" type="submit"><i class="fa fa-forward"></i> Update</button>
                </div>
              </div>
            </form>
          @endif

          @if($order->status !== 'cancelled')
            <form method="POST" action="{{ route('admin.orders.updateStatus') }}" class="section">
              @csrf
              <div class="section-head"><div class="title">Cancel Order</div></div>
              <div class="pad" style="display:flex;justify-content:space-between;align-items:center;gap:.5rem">
                <span class="muted">Mark as <strong>cancelled</strong></span>
                <div>
                  <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                  <input type="hidden" name="new_status" value="cancelled">
                  <button class="btn red" type="submit"><i class="fa fa-ban"></i> Cancel</button>
                </div>
              </div>
            </form>
          @endif
        </aside>
      </div>
    </div>
  </main>

  <script>
    // Theme toggle (same as Orders/Reports)
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
  </script>
</body>
</html>
