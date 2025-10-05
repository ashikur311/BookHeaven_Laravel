{{-- resources/views/admin/audiobooks.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard – Audiobooks</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

  <style>
    /* ========= THEME TOKENS (shared) ========= */
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
      display:grid; gap:.25rem; box-shadow:var(--shadow); font-weight:700;
    }
    .stat .k{ font-size:.85rem; color:var(--text-muted); font-weight:800 }
    .stat .v{ font-size:1.85rem }
    .stat.primary{ border-left:4px solid var(--primary) }
    .stat.success{ border-left:4px solid var(--success) }
    .stat.warning{ border-left:4px solid var(--warning) }
    .stat.danger{ border-left:4px solid var(--danger) }

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
    .cover{ width:60px; height:80px; object-fit:cover; border-radius:6px; border:1px solid var(--border) }

    .badge{
      display:inline-flex; align-items:center; gap:.35rem; padding:.25rem .55rem; border-radius:999px; font-size:.8rem; font-weight:800; border:1px solid transparent;
      text-transform:capitalize;
    }
    .b-visible { background:rgba(16,185,129,.12); color:#047857; border-color:rgba(16,185,129,.35) }
    .b-hidden  { background:rgba(107,114,128,.12); color:#374151; border-color:rgba(107,114,128,.35) }
    .b-pending { background:rgba(245,158,11,.12); color:#b45309; border-color:rgba(245,158,11,.35) }

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
  </style>
</head>
<body>
  <!-- HEADER -->
  <header class="admin_header">
    <div class="logo"><img src="{{ asset('assets/images/download.png') }}" alt="Logo"></div>
    <div class="admin_header_right">
      <h1>Audiobooks</h1>
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
          <li><a href="{{ route('admin.audiobooks') }}" class="active"><i class="fas fa-headphones"></i> Audiobooks</a></li>
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
      {{-- Alerts --}}
      @if(session('error'))   <div class="alert alert-error"><i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}</div> @endif
      @if(session('success')) <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> {{ session('success') }}</div> @endif
      @if(!empty($error_message)) <div class="alert alert-error"><i class="fa-solid fa-triangle-exclamation"></i> {{ $error_message }}</div> @endif

      <!-- STATS -->
      @php
        $total = $audiobooks->count();
        $visible = $audiobooks->where('status','visible')->count();
        $hidden  = $audiobooks->where('status','hidden')->count();
        $pending = $audiobooks->where('status','pending')->count();
      @endphp
      <div class="section">
        <div class="section-head">
          <div class="title">Audiobooks Overview</div>
          <span class="muted">Live snapshot</span>
        </div>
        <div class="pad">
          <div class="grid stats">
            <div class="stat primary"><div class="k">Total Audiobooks</div><div class="v">{{ $total }}</div></div>
            <div class="stat success"><div class="k">Visible</div><div class="v">{{ $visible }}</div></div>
            <div class="stat"><div class="k">Hidden</div><div class="v">{{ $hidden }}</div></div>
            <div class="stat warning"><div class="k">Pending</div><div class="v">{{ $pending }}</div></div>
          </div>
        </div>
      </div>

      {{-- EDIT FORM (kept as-is, just styled by cards) --}}
      @if($edit_mode && $audiobook_to_edit)
        <div class="section">
          <div class="section-head">
            <div class="title">Edit Audiobook: {{ $audiobook_to_edit->title }}</div>
            <a href="{{ route('admin.audiobooks') }}" class="btn ghost"><i class="fa fa-arrow-left"></i> Back</a>
          </div>
          <div class="pad">
            <form method="POST" action="{{ route('admin.audiobooks.update') }}" enctype="multipart/form-data">
              @csrf
              <input type="hidden" name="audiobook_id" value="{{ $audiobook_to_edit->audiobook_id }}">
              <input type="hidden" name="current_poster" value="{{ $audiobook_to_edit->poster_url }}">
              <input type="hidden" name="current_audio" value="{{ $audiobook_to_edit->audio_url }}">

              <div class="grid" style="grid-template-columns:1fr 1fr; gap:1rem">
                <div>
                  <label class="muted" for="title">Title</label>
                  <input id="title" name="title" type="text" value="{{ old('title', $audiobook_to_edit->title) }}" required class="control" style="width:100%">
                </div>
                <div>
                  <label class="muted" for="writer">Writer</label>
                  <input id="writer" name="writer" type="text" value="{{ old('writer', $audiobook_to_edit->writer) }}" required class="control" style="width:100%">
                </div>
                <div>
                  <label class="muted" for="genre">Genre</label>
                  <input id="genre" name="genre" type="text" value="{{ old('genre', $audiobook_to_edit->genre) }}" required class="control" style="width:100%">
                </div>
                <div>
                  <label class="muted" for="category">Category</label>
                  <input id="category" name="category" type="text" value="{{ old('category', $audiobook_to_edit->category) }}" required class="control" style="width:100%">
                </div>
                <div>
                  <label class="muted" for="language">Language</label>
                  <input id="language" name="language" type="text" value="{{ old('language', $audiobook_to_edit->language) }}" class="control" style="width:100%">
                </div>
                <div>
                  <label class="muted" for="duration">Duration (HH:MM:SS)</label>
                  <input id="duration" name="duration" type="time" step="1" value="{{ old('duration', $audiobook_to_edit->duration) }}" class="control" style="width:100%">
                </div>
                <div>
                  <label class="muted" for="status">Status</label>
                  <select id="status" name="status" required class="control" style="width:100%">
                    @foreach(['visible','hidden','pending'] as $st)
                      <option value="{{ $st }}" {{ old('status', $audiobook_to_edit->status) === $st ? 'selected' : '' }}>{{ ucfirst($st) }}</option>
                    @endforeach
                  </select>
                </div>
                <div></div>
                <div style="grid-column:1/-1">
                  <label class="muted" for="description">Description</label>
                  <textarea id="description" name="description" class="control" style="width:100%; min-height:120px">{{ old('description', $audiobook_to_edit->description) }}</textarea>
                </div>

                <div>
                  <div class="muted" style="margin-bottom:.4rem">Current Poster</div>
                  @if($audiobook_to_edit->poster_url)
                    <img src="{{ asset($audiobook_to_edit->poster_url) }}" alt="Current Poster" style="width:160px;height:auto;border:1px solid var(--border);border-radius:.5rem">
                  @else
                    <div class="muted">No poster uploaded</div>
                  @endif
                </div>
                <div>
                  <label class="muted" for="poster">Update Poster (optional)</label>
                  <input id="poster" name="poster" type="file" accept="image/*" class="control">
                  <small class="muted">JPG, JPEG, PNG, WEBP</small>
                </div>

                <div style="grid-column:1/-1">
                  <div class="muted" style="margin-bottom:.4rem">Current Audio</div>
                  @if($audiobook_to_edit->audio_url)
                    <audio controls style="width:100%;max-width:420px">
                      <source src="{{ asset($audiobook_to_edit->audio_url) }}">
                      Your browser does not support the audio element.
                    </audio>
                  @else
                    <div class="muted">No audio uploaded</div>
                  @endif
                </div>
                <div>
                  <label class="muted" for="audio">Update Audio (optional)</label>
                  <input id="audio" name="audio" type="file" accept="audio/*" class="control">
                  <small class="muted">MP3, M4A, WAV, OGG</small>
                </div>
              </div>

              <div style="display:flex; gap:.6rem; justify-content:flex-end; margin-top:1rem">
                <a href="{{ route('admin.audiobooks') }}" class="btn ghost"><i class="fa fa-times"></i> Cancel</a>
                <button type="submit" class="btn primary"><i class="fa fa-save"></i> Save Changes</button>
              </div>
            </form>
          </div>
        </div>
      @endif

      <!-- FILTERS -->
      <div class="section">
        <div class="section-head">
          <div class="title">Filter & Search</div>
          <span class="muted">Find audiobooks fast</span>
        </div>
        <div class="pad">
          <div class="toolbar">
            <div class="control">
              <i class="fa-solid fa-magnifying-glass"></i>
              <input id="searchInput" type="text" placeholder="Search title, writer, genre…">
            </div>
            <div class="control">
              <i class="fa-solid fa-filter"></i>
              <select id="statusFilter">
                <option value="all">All statuses</option>
                <option value="visible">Visible</option>
                <option value="hidden">Hidden</option>
                <option value="pending">Pending</option>
              </select>
            </div>
            <button class="btn ghost" id="resetFilters"><i class="fa-solid fa-rotate"></i> Reset</button>
          </div>
        </div>
      </div>

      <!-- LIST -->
      <div class="section">
        <div class="section-head">
          <div class="title">Audiobooks List</div>
          <span class="muted">Manage entries</span>
        </div>
        <div class="pad" style="overflow-x:auto">
          <table>
            <thead>
              <tr>
                <th style="width:80px">ID</th>
                <th style="width:90px">Poster</th>
                <th>Title</th>
                <th>Writer</th>
                <th>Genre</th>
                <th>Status</th>
                <th style="width:220px">Actions</th>
              </tr>
            </thead>
            <tbody id="abTableBody">
            @foreach($audiobooks as $ab)
              @php
                $poster = $ab->poster_url ? asset($ab->poster_url) : 'https://via.placeholder.com/60x80?text=No+Poster';
                $statusClass = [
                  'visible' => 'b-visible',
                  'hidden'  => 'b-hidden',
                  'pending' => 'b-pending',
                ][$ab->status] ?? 'b-hidden';
              @endphp
              <tr
                data-status="{{ $ab->status }}"
                data-search="{{ Str::lower($ab->title.' '.$ab->writer.' '.$ab->genre) }}"
              >
                <td>#{{ $ab->audiobook_id }}</td>
                <td><img src="{{ $poster }}" alt="{{ $ab->title }}" class="cover"></td>
                <td>{{ $ab->title }}</td>
                <td>{{ $ab->writer }}</td>
                <td>{{ $ab->genre }}</td>
                <td><span class="badge {{ $statusClass }}">{{ ucfirst($ab->status) }}</span></td>
                <td class="actions">
                  {{-- Edit via GET ?edit=ID --}}
                  <form method="GET" action="{{ route('admin.audiobooks') }}" style="display:inline">
                    <input type="hidden" name="edit" value="{{ $ab->audiobook_id }}">
                    <button type="submit" class="action edit"><i class="fa-regular fa-pen-to-square"></i> Edit</button>
                  </form>

                  {{-- Delete --}}
                  <form method="POST" action="{{ route('admin.audiobooks.delete') }}" style="display:inline" onsubmit="return confirm('Delete this audiobook?')">
                    @csrf
                    <input type="hidden" name="audiobook_id" value="{{ $ab->audiobook_id }}">
                    <button type="submit" class="action del"><i class="fa-regular fa-trash-can"></i> Delete</button>
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

    // Filters (client-side)
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const resetBtn = document.getElementById('resetFilters');

    function applyFilters(){
      const term = (searchInput?.value || '').toLowerCase().trim();
      const status = statusFilter?.value || 'all';

      document.querySelectorAll('#abTableBody tr').forEach(tr => {
        const hay = (tr.getAttribute('data-search') || '');
        const st  = tr.getAttribute('data-status') || '';
        const matchText = !term || hay.includes(term);
        const matchStatus = status === 'all' || st === status;
        tr.style.display = (matchText && matchStatus) ? '' : 'none';
      });
    }

    searchInput?.addEventListener('input', applyFilters);
    statusFilter?.addEventListener('change', applyFilters);
    resetBtn?.addEventListener('click', () => {
      if (searchInput) searchInput.value = '';
      if (statusFilter) statusFilter.value = 'all';
      applyFilters();
    });
  </script>
</body>
</html>
