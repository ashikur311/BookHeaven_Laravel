<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Writers</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    /* ----------- Same design tokens as REPORTS ----------- */
    :root{
      --bg:#f5f7fb;
      --text:#1f2937;
      --text-muted:#6b7280;
      --primary:#3b82f6;
      --danger:#ef4444;
      --card:#ffffff;
      --border:#e5e7eb;
      --table-header:#f3f4f6;
      --table-hover:#f9fafb;
      --chip:#eef2ff;
    }
    body.admin-dark-mode{
      --bg:#0f172a;
      --text:#e5e7eb;
      --text-muted:#9ca3af;
      --primary:#60a5fa;
      --danger:#f87171;
      --card:#111827;
      --border:#1f2937;
      --table-header:#0b1220;
      --table-hover:#0c1427;
      --chip:#1f2937;
    }

    *{box-sizing:border-box;margin:0;padding:0}
    body{
      font-family:ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans","Apple Color Emoji","Segoe UI Emoji";
      background:var(--bg);
      color:var(--text);
      line-height:1.5;
    }

    /* ----------- Header (same as REPORTS) ----------- */
    .admin_header{
      display:flex;justify-content:space-between;align-items:center;
      padding:1rem 1.5rem;background:#1f2937;color:#fff;
      position:sticky;top:0;z-index:30;border-bottom:1px solid rgba(255,255,255,.06);
    }
    .logo img{height:40px}
    .admin_header_right{display:flex;align-items:center;gap:.75rem}
    .admin_header_right h1{font-size:1.1rem;font-weight:600}
    .admin_theme_toggle{
      background:transparent;border:1px solid rgba(255,255,255,.2);color:#fff;
      padding:.5rem .65rem;border-radius:.5rem;cursor:pointer;
    }

    /* ----------- Shell / Sidebar (same grid + scroll) ----------- */
    .admin_main{display:grid;grid-template-columns:260px 1fr;min-height:calc(100vh - 64px)}
    .admin_sidebar{
      background:#111827;color:#cbd5e1;border-right:1px solid rgba(255,255,255,.06);
      position:sticky;top:64px;align-self:start;height:calc(100vh - 64px);overflow-y:auto;
    }
    .admin_sidebar_nav ul{list-style:none;padding:.75rem}
    .admin_sidebar_nav a,.admin_sidebar_nav button.linklike{
      display:flex;align-items:center;gap:.75rem;
      padding:.65rem .75rem;margin-bottom:.25rem;text-decoration:none;color:inherit;
      border-radius:.5rem;border:none;background:transparent;width:100%;text-align:left;cursor:pointer;
    }
    .admin_sidebar_nav a:hover,.admin_sidebar_nav button.linklike:hover{background:rgba(255,255,255,.06)}
    .admin_sidebar_nav a.active{background:rgba(59,130,246,.18);color:#fff}

    .admin_main_content{padding:1.5rem}

    /* ----------- Cards / Tables (neutral, same feel) ----------- */
    .stats-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1rem;margin-bottom:1.25rem}
    @media (max-width:1100px){.stats-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media (max-width:720px){.admin_main{grid-template-columns:1fr}.admin_sidebar{display:none}.stats-grid{grid-template-columns:1fr}}

    .stat-card{
      background:var(--card);border:1px solid var(--border);border-radius:12px;
      padding:1rem 1.25rem;box-shadow:0 1px 1px rgba(0,0,0,.02);
      display:grid;gap:.25rem;text-align:center;
    }
    .stat-card h3{font-size:.9rem;color:var(--text-muted)}
    .stat-value{font-size:1.75rem;font-weight:800;color:var(--text)}

    .section-title{margin:1.25rem 0 .75rem;font-size:1.1rem;font-weight:700}

    table{width:100%;border-collapse:collapse}
    th,td{padding:.75rem .9rem;border-bottom:1px solid var(--border);font-size:.95rem}
    thead th{background:var(--table-header);color:var(--text-muted);font-weight:700}
    tbody tr:hover{background:var(--table-hover)}

    .user-avatar{width:50px;height:50px;border-radius:50%;object-fit:cover}

    .action-btn{
      padding:.5rem 1rem;border:none;border-radius:.5rem;cursor:pointer;font-size:.9rem;
      transition:opacity .15s ease,transform .05s ease;
    }
    .action-btn:active{transform:scale(.98)}
    .edit-btn{background:var(--primary);color:#fff;margin-right:.5rem}
    .edit-btn:hover{opacity:.9}
    .delete-btn{background:var(--danger);color:#fff}
    .delete-btn:hover{opacity:.9}

    .card{
      background:var(--card);border:1px solid var(--border);border-radius:12px;
      padding:1rem 1.25rem;box-shadow:0 1px 1px rgba(0,0,0,.02);
      margin-bottom:1rem;
    }

    .form-grid{display:grid;gap:1rem}
    .form-group{display:grid;gap:.4rem}
    .form-group label{font-weight:700}
    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group textarea{
      width:100%;padding:.8rem;border:1px solid var(--border);border-radius:.5rem;background:var(--card);color:var(--text);
    }
    .form-group textarea{min-height:110px;resize:vertical}
    .file-upload{margin-top:.25rem}
    .file-upload-preview{margin-top:.5rem;max-width:150px;border:1px solid var(--border);padding:.5rem;border-radius:.5rem}

    .form-actions{display:flex;gap:.6rem;flex-wrap:wrap}
    .btn{padding:.7rem 1rem;border:none;border-radius:.5rem;cursor:pointer;font-size:1rem}
    .btn-primary{background:var(--primary);color:#fff}
    .btn-secondary{background:#9ca3af;color:#fff}

    .alert{padding:1rem;border-radius:.5rem;margin-bottom:1rem}
    .alert-error{background:#fee2e2;color:#991b1b;border:1px solid #fecaca}
    .alert-success{background:#dcfce7;color:#14532d;border:1px solid #bbf7d0}
  </style>
</head>
<body>
  <!-- HEADER (exact palette/structure as Reports) -->
  <header class="admin_header">
    <div class="logo"><img src="{{ asset('assets/images/download.png') }}" alt="Logo"></div>
    <div class="admin_header_right">
      <h1>Admin Writers</h1>
      <span style="font-size:.75rem;padding:.25rem .5rem;border-radius:999px;background:var(--chip);color:var(--text-muted);">
        Manage authors & profiles
      </span>
      <button class="admin_theme_toggle" id="themeToggle" aria-label="Toggle theme"><i class="fas fa-moon"></i></button>
    </div>
  </header>

  <main class="admin_main">
    <!-- SIDEBAR (same color + sticky + scroll as Reports) -->
    <aside class="admin_sidebar">
      <nav class="admin_sidebar_nav">
        <ul>
          <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
          <li><a href="{{ route('admin.add') }}"><i class="fas fa-plus-circle"></i> Add</a></li>
          <li><a href="{{ route('admin.users') }}"><i class="fas fa-users"></i> Users</a></li>
          <li><a href="{{ route('admin.partners') }}"><i class="fas fa-handshake"></i> Partners</a></li>
          <li><a href="{{ route('admin.writers') }}" class="active"><i class="fas fa-pen-fancy"></i> Writers</a></li>
          <li><a href="{{ route('admin.books') }}"><i class="fas fa-book"></i> Books</a></li>
          <li><a href="{{ route('admin.audiobooks') }}"><i class="fas fa-headphones"></i> Audio Books</a></li>
          <li><a href="{{ route('admin.partnerbooks') }}"><i class="fas fa-book-reader"></i> Partner Books</a></li>
          <li><a href="{{ route('admin.orders') }}"><i class="fas fa-shopping-cart"></i> Orders</a></li>
          <li><a href="{{ route('admin.subscription') }}"><i class="fas fa-star"></i> Subscription</a></li>
          <li><a href="{{ route('admin.events') }}"><i class="fas fa-calendar-alt"></i> Events</a></li>
          <li><a href="{{ route('admin.community') }}"><i class="fas fa-users"></i> Community</a></li>
          <li><a href="{{ route('admin.question') }}"><i class="fa-solid fa-question"></i> User Questions</a></li>
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

    <!-- MAIN CONTENT -->
    <div class="admin_main_content">
      {{-- Alerts --}}
      @if(session('error'))   <div class="alert alert-error">{{ session('error') }}</div> @endif
      @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
      @if(!empty($error_message)) <div class="alert alert-error">{{ $error_message }}</div> @endif

      <div class="card" style="display:flex;align-items:center;justify-content:space-between;gap:1rem">
        <div>
          <h2 style="margin:0;font-size:1.1rem">Writers Management</h2>
          <p style="margin:.25rem 0 0;color:var(--text-muted);font-size:.9rem">Create, edit and manage writers</p>
        </div>
      </div>

      <!-- Stats -->
      <div class="stats-grid">
        <div class="stat-card">
          <h3>Total Writers</h3>
          <div class="stat-value">{{ number_format($stats['total_writers']) }}</div>
        </div>
        <div class="stat-card">
          <h3>Most Prolific</h3>
          <div class="stat-value">{{ $stats['prolific_name'] }}</div>
        </div>
        <div class="stat-card">
          <h3>Total Books</h3>
          <div class="stat-value">{{ number_format($stats['total_books']) }}</div>
        </div>
      </div>

      @if($edit_mode && $writer_to_edit)
        <div class="card">
          <h3 style="margin-bottom:.75rem">Edit Writer: {{ $writer_to_edit->name }}</h3>
          <form method="POST" action="{{ route('admin.writers.update') }}" enctype="multipart/form-data" class="form-grid">
            @csrf
            <input type="hidden" name="writer_id" value="{{ $writer_to_edit->writer_id }}">
            <input type="hidden" name="current_image" value="{{ $writer_to_edit->image_url }}">

            <div class="form-group">
              <label for="name">Name</label>
              <input id="name" name="name" type="text" value="{{ old('name', $writer_to_edit->name) }}" required>
            </div>
            <div class="form-group">
              <label for="email">Email</label>
              <input id="email" name="email" type="email" value="{{ old('email', $writer_to_edit->email) }}">
            </div>
            <div class="form-group">
              <label for="bio">Biography</label>
              <textarea id="bio" name="bio">{{ old('bio', $writer_to_edit->bio) }}</textarea>
            </div>
            <div class="form-group">
              <label for="address">Address</label>
              <input id="address" name="address" type="text" value="{{ old('address', $writer_to_edit->address) }}">
            </div>

            <div class="form-group">
              <label>Current Image</label>
              @if($writer_to_edit->image_url)
                <div class="file-upload-preview">
                  <img src="{{ asset($writer_to_edit->image_url) }}" alt="Current Writer Image">
                </div>
              @else
                <p style="color:var(--text-muted)">No image uploaded</p>
              @endif
            </div>

            <div class="form-group">
              <label for="image">Update Image (optional)</label>
              <input id="image" name="image" type="file" class="file-upload" accept="image/*">
              <small style="color:var(--text-muted)">Allowed formats: JPG, JPEG, PNG, WEBP</small>
            </div>

            <div class="form-actions">
              <button type="submit" class="btn btn-primary">Save Changes</button>
              <a href="{{ route('admin.writers') }}" class="btn btn-secondary">Cancel</a>
            </div>
          </form>
        </div>
      @endif

      <h3 class="section-title">Writers List</h3>
      <div class="card" style="padding:0">
        <div style="overflow-x:auto;">
          <table>
            <thead>
              <tr>
                <th>ID</th><th>Photo</th><th>Name</th><th>Email</th><th>Books</th><th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($writers as $w)
                @php $img = $w->image_url ? asset($w->image_url) : 'https://via.placeholder.com/50?text=No+Image'; @endphp
                <tr>
                  <td>#{{ $w->writer_id }}</td>
                  <td><img src="{{ $img }}" alt="{{ $w->name }}" class="user-avatar"></td>
                  <td>{{ $w->name }}</td>
                  <td>{{ $w->email }}</td>
                  <td>{{ $w->book_count }}</td>
                  <td>
                    <form method="GET" action="{{ route('admin.writers') }}" style="display:inline;">
                      <input type="hidden" name="edit" value="{{ $w->writer_id }}">
                      <button type="submit" class="action-btn edit-btn">Edit</button>
                    </form>
                    <form method="POST" action="{{ route('admin.writers.delete') }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this writer?')">
                      @csrf
                      <input type="hidden" name="writer_id" value="{{ $w->writer_id }}">
                      <button type="submit" class="action-btn delete-btn">Delete</button>
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
    // Theme toggle — identical behavior to Reports
    const themeToggle = document.getElementById('themeToggle');
    const icon = themeToggle.querySelector('i');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (localStorage.getItem('admin-theme') === 'dark' || (!localStorage.getItem('admin-theme') && prefersDark)) {
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
  </script>
</body>
</html>
