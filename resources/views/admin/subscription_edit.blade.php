<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard — Edit Subscription</title>
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

    /* ========= PAGE HEAD / BREADCRUMB ========= */
    .page-head{
      display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:1rem;
    }
    .crumbs{display:flex; align-items:center; gap:.5rem; color:var(--text-muted); font-size:.92rem}
    .crumbs a{ color:inherit; text-decoration:none }
    .crumbs i{ opacity:.7 }
    .chip{ font-size:.75rem; padding:.25rem .5rem; border-radius:999px; background:var(--chip); color:var(--text-muted); }

    /* ========= CARD / FORM ========= */
    .card{
      background:var(--card); border:1px solid var(--border); border-radius:12px; box-shadow:var(--shadow);
    }
    .card-head{
      padding:1rem 1.25rem; border-bottom:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; gap:1rem;
    }
    .card-body{ padding:1.25rem }
    .title{ font-weight:800; font-size:1.05rem }
    .muted{ color:var(--text-muted) }

    .grid{ display:grid; gap:1rem }
    .grid.two{ grid-template-columns:repeat(2,minmax(0,1fr)) }
    @media (max-width:900px){ .admin_main{ grid-template-columns:1fr } .admin_sidebar{ display:none } .grid.two{ grid-template-columns:1fr } }

    .field{ display:grid; gap:.35rem }
    .label{ font-size:.9rem; font-weight:700 }
    .hint{ font-size:.8rem; color:var(--text-muted) }

    .control{
      display:flex; align-items:center; gap:.5rem;
      background:var(--bg); border:1px solid var(--border); border-radius:.65rem; padding:.6rem .75rem;
    }
    .control input, .control select, .control textarea{
      width:100%; border:none; outline:none; background:transparent; color:var(--text); font:inherit;
    }
    .adorn{ opacity:.75; font-weight:700; min-width:1.25rem; text-align:center }

    .actions{ display:flex; gap:.6rem; justify-content:flex-end; padding:1rem 1.25rem; border-top:1px solid var(--border) }
    .btn{ padding:.6rem 1rem; border:none; border-radius:.55rem; cursor:pointer; font-weight:700 }
    .btn.primary{ background:var(--primary); color:#fff }
    .btn.ghost{ background:transparent; border:1px solid var(--border); color:var(--text) }

    /* ========= ALERTS ========= */
    .alert{ padding:.9rem 1rem; border-radius:.6rem; margin:1rem 0; border:1px solid }
    .alert-success{ background:#dcfce7; color:#14532d; border-color:#bbf7d0 }
    .alert-error{ background:#fee2e2; color:#7f1d1d; border-color:#fecaca }

    /* ========= SMALL UTIL ========= */
    .sr{ font-variant-numeric:tabular-nums }
  </style>
</head>
<body>
<header class="admin_header">
  <div class="logo"><img src="{{ asset('assets/images/download.png') }}" alt="Logo"></div>
  <div class="admin_header_right">
    <h1>Admin Dashboard</h1>
    <span class="chip">Edit Subscription</span>
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

    <div class="page-head">
      <div class="crumbs">
        <a href="{{ route('admin.subscription') }}"><i class="fa-solid fa-arrow-left"></i> Plans</a>
        <i class="fa-solid fa-angle-right"></i>
        <span>Edit #{{ $plan->plan_id }}</span>
      </div>
      <span class="muted">Last updated: {{ \Carbon\Carbon::parse($plan->updated_at ?? now())->format('Y-m-d H:i') }}</span>
    </div>

    @if(session('error'))   <div class="alert alert-error">{{ session('error') }}</div> @endif
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if ($errors->any())
      <div class="alert alert-error">
        <ul style="padding-left:1rem;list-style:disc;">
          @foreach ($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="card" role="region" aria-labelledby="editTitle">
      <div class="card-head">
        <div id="editTitle" class="title">Edit Subscription Plan</div>
        <a href="{{ route('admin.subscription') }}" class="btn ghost"><i class="fa-solid fa-list"></i> Back to Plans</a>
      </div>

      <form method="POST" action="{{ route('admin.subscription.update', $plan->plan_id) }}">
        @csrf
        @method('PUT')

        <div class="card-body grid">
          <div class="field">
            <label for="plan_name" class="label">Plan Name</label>
            <div class="control">
              <input type="text" id="plan_name" name="plan_name"
                     value="{{ old('plan_name', $plan->plan_name) }}" required
                     placeholder="e.g., Premium, Gold, Basic">
            </div>
            <span class="hint">Choose a short, clear name users can recognize.</span>
          </div>

          <div class="grid two">
            <div class="field">
              <label for="price" class="label">Price</label>
              <div class="control">
                <span class="adorn">$</span>
                <input type="number" step="0.01" min="0" id="price" name="price"
                       value="{{ old('price', $plan->price) }}" required
                       aria-describedby="priceHint">
              </div>
              <span id="priceHint" class="hint">Displayed as <span id="pricePreview" class="sr">${{ number_format($plan->price, 2) }}</span></span>
            </div>

            <div class="field">
              <label for="validity_days" class="label">Validity</label>
              <div class="control">
                <input type="number" min="1" id="validity_days" name="validity_days"
                       value="{{ old('validity_days', $plan->validity_days) }}" required>
                <span class="adorn">days</span>
              </div>
              <span class="hint">How long the plan remains active.</span>
            </div>
          </div>

          <div class="grid two">
            <div class="field">
              <label for="book_quantity" class="label">Book Quantity</label>
              <div class="control">
                <input type="number" min="0" id="book_quantity" name="book_quantity"
                       value="{{ old('book_quantity', $plan->book_quantity) }}" required>
              </div>
              <span class="hint">Number of eBooks included in the plan.</span>
            </div>

            <div class="field">
              <label for="audiobook_quantity" class="label">Audiobook Quantity</label>
              <div class="control">
                <input type="number" min="0" id="audiobook_quantity" name="audiobook_quantity"
                       value="{{ old('audiobook_quantity', $plan->audiobook_quantity) }}" required>
              </div>
              <span class="hint">Number of audiobooks included in the plan.</span>
            </div>
          </div>

          <div class="field">
            <label for="plan_description" class="label">Description</label>
            <div class="control" style="padding:.25rem">
              <textarea id="plan_description" name="plan_description" rows="4"
                        placeholder="Briefly describe what this plan offers...">{{ old('plan_description', $plan->description) }}</textarea>
            </div>
            <span class="hint">Visible on the plan detail page.</span>
          </div>

          <div class="field">
            <label for="status" class="label">Status</label>
            <div class="control">
              <select id="status" name="status" required>
                <option value="active"   {{ old('status', $plan->status) === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status', $plan->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
              </select>
            </div>
            <span class="hint">Inactive plans are hidden from new purchases.</span>
          </div>
        </div>

        <div class="actions">
          <a href="{{ route('admin.subscription') }}" class="btn ghost">Cancel</a>
          <button type="submit" class="btn primary"><i class="fa-solid fa-floppy-disk"></i> Update Plan</button>
        </div>
      </form>
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

  // ===== Live price preview =====
  const priceInput = document.getElementById('price');
  const pricePreview = document.getElementById('pricePreview');
  function formatUSD(v){
    const n = Number(v || 0);
    return '$' + n.toLocaleString(undefined, {minimumFractionDigits:2, maximumFractionDigits:2});
  }
  if (priceInput && pricePreview){
    const updatePreview = () => pricePreview.textContent = formatUSD(priceInput.value);
    priceInput.addEventListener('input', updatePreview);
    updatePreview();
  }
</script>
</body>
</html>
