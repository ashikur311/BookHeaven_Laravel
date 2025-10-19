<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Management - Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    /* ========= SAME THEME TOKENS AS REPORTS ========= */
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
      --shadow: 0 1px 1px rgba(0,0,0,.02);
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
      font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
      background: var(--bg);
      color: var(--text);
      line-height: 1.5;
    }

    /* ========= HEADER (identical palette to Reports) ========= */
    .admin_header {
      display: flex; justify-content: space-between; align-items: center;
      padding: 1rem 1.5rem; background: #1f2937; color: #fff;
      position: sticky; top: 0; z-index: 30; border-bottom: 1px solid rgba(255,255,255,.06);
    }
    .logo img { height: 40px; }
    .admin_header_right { display: flex; align-items: center; gap: .75rem; }
    .admin_header_right h1 { font-size: 1.1rem; font-weight: 600; }
    .admin_theme_toggle {
      background: transparent; border: 1px solid rgba(255,255,255,.2); color: #fff;
      padding: .5rem .65rem; border-radius: .5rem; cursor: pointer;
    }

    /* ========= LAYOUT / SIDEBAR (sticky + scroll same as Reports) ========= */
    .admin_main { display: grid; grid-template-columns: 260px 1fr; min-height: calc(100vh - 64px); }
    .admin_sidebar {
      background: #111827; color: #cbd5e1; border-right: 1px solid rgba(255,255,255,.06);
      position: sticky; top: 64px; align-self: start; height: calc(100vh - 64px); overflow-y: auto;
    }
    .admin_sidebar_nav ul { list-style: none; padding: .75rem; }
    .admin_sidebar_nav a, .admin_sidebar_nav button.linklike {
      display: flex; align-items: center; gap: .75rem;
      padding: .65rem .75rem; margin-bottom: .25rem; text-decoration: none; color: inherit;
      border-radius: .5rem; border: none; background: transparent; width: 100%; text-align: left; cursor: pointer;
    }
    .admin_sidebar_nav a:hover, .admin_sidebar_nav button.linklike:hover { background: rgba(255,255,255,.06); }
    .admin_sidebar_nav a.active { background: rgba(59,130,246,.18); color: #fff; }
    .admin_main_content { padding: 1.5rem; }

    /* ========= STATS ========= */
    .grid { display: grid; gap: 1rem; }
    .grid.stats { grid-template-columns: repeat(4,minmax(0,1fr)); }
    @media (max-width: 1100px) { .grid.stats { grid-template-columns: repeat(2,minmax(0,1fr)); } }
    @media (max-width: 900px) { .admin_main { grid-template-columns: 1fr; } .admin_sidebar { display: none; } }

    .stat-card {
      background: var(--card); border: 1px solid var(--border); border-radius: 12px;
      padding: 1rem 1.25rem; box-shadow: var(--shadow); text-align: center; display: grid; gap: .25rem;
    }
    .stat-card h3 { margin: 0; font-size: .9rem; color: var(--text-muted); }
    .stat-value { font-size: 1.75rem; font-weight: 800; color: var(--text); }

    /* ========= TOOLBAR ========= */
    .toolbar {
      background: var(--card); border: 1px solid var(--border); border-radius: 12px;
      padding: .9rem 1rem; margin: 1rem 0; display: flex; gap: .75rem; align-items: center; flex-wrap: wrap;
      box-shadow: var(--shadow);
    }
    .toolbar .title { font-weight: 700; margin-right: auto; }
    .input {
      display: inline-flex; align-items: center; gap: .5rem;
      background: var(--bg); border: 1px solid var(--border); border-radius: .6rem; padding: .55rem .75rem;
    }
    .input input {
      border: none; outline: none; background: transparent; color: var(--text); min-width: 200px;
    }
    .btn {
      padding: .6rem .9rem; border: none; border-radius: .55rem; cursor: pointer; font-weight: 600;
    }
    .btn.primary { background: var(--primary); color: #fff; }
    .btn.ghost   { background: transparent; border: 1px solid var(--border); color: var(--text); }

    /* ========= TABLE ========= */
    .table-card {
      background: var(--card); border: 1px solid var(--border); border-radius: 12px; box-shadow: var(--shadow);
      overflow: clip;
    }
    .table-wrap { width: 100%; overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; }
    thead th {
      position: sticky; top: 0; z-index: 1; background: var(--table-header);
      color: var(--text-muted); font-weight: 700; text-align: left; padding: .75rem .9rem; border-bottom: 1px solid var(--border);
    }
    tbody td { padding: .75rem .9rem; border-bottom: 1px solid var(--border); }
    tbody tr:hover { background: var(--table-hover); }
    .user-avatar { width: 42px; height: 42px; border-radius: 100%; object-fit: cover; box-shadow: 0 0 0 1px var(--border); }

    .action-btns { display: flex; gap: .4rem; flex-wrap: wrap; }
    .action-btn {
      padding: .45rem .8rem; border: 1px solid var(--border); background: transparent; color: var(--text);
      border-radius: .5rem; cursor: pointer; font-size: .9rem;
    }
    .view-btn   { border-color: var(--primary); color: var(--primary); }
    .delete-btn { border-color: var(--danger);  color: var(--danger);  }

    /* ========= MODALS ========= */
    .admin_modal {
      display: none; position: fixed; inset: 0; z-index: 50; align-items: center; justify-content: center;
      background: rgba(15,23,42,.45); backdrop-filter: blur(2px);
    }
    .admin_modal_content {
      width: min(680px, 92vw); background: var(--card); color: var(--text);
      border: 1px solid var(--border); border-radius: 14px; padding: 1.25rem; box-shadow: 0 10px 30px rgba(0,0,0,.25);
      animation: pop .12s ease-out;
    }
    @keyframes pop { from { transform: scale(.98); opacity: .9; } to { transform: scale(1); opacity: 1; } }
    .admin_modal_close { float: right; cursor: pointer; font-size: 1.25rem; color: var(--text-muted); }
    .admin_modal_close:hover { color: var(--text); }

    .user-details { display: grid; grid-template-columns: 96px 1fr; gap: 1rem; margin-top: .5rem; }
    .user-details-avatar { width: 96px; height: 96px; border-radius: 50%; object-fit: cover; box-shadow: 0 0 0 2px var(--border); }
    .user-details-info { display: grid; gap: .5rem; }
    .user-details-row { display: grid; grid-template-columns: 140px 1fr; gap: .75rem; }
    .user-details-label { color: var(--text-muted); font-weight: 600; }

    .confirmation-modal { width: min(520px, 92vw); }
    .confirmation-buttons { display: flex; gap: .5rem; justify-content: flex-end; margin-top: 1rem; }
    .confirm-btn { background: var(--danger); color: #fff; border: none; }
    .cancel-btn  { background: transparent; color: var(--text); border: 1px solid var(--border); }

    /* ========= ALERTS ========= */
    .alert { padding: .9rem 1rem; border-radius: .6rem; margin: 1rem 0; border: 1px solid; }
    .alert-success { background: #dcfce7; color: #14532d; border-color: #bbf7d0; }
    .alert-error   { background: #fee2e2; color: #7f1d1d; border-color: #fecaca; }

    /* ========= RESPONSIVE ========= */
    @media (max-width: 520px) {
      .user-details { grid-template-columns: 1fr; }
      .user-details-avatar { justify-self: center; }
      .user-details-row { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
  <!-- HEADER -->
  <header>
    <nav class="admin_header">
      <div class="logo">
        <img src="{{ asset('assets/images/download.png') }}" alt="Logo">
      </div>
      <div class="admin_header_right">
        <h1>Admin Dashboard</h1>
        <span class="chip" style="background:var(--chip);color:var(--text-muted);padding:.25rem .5rem;border-radius:999px;font-size:.75rem;">
          Users
        </span>
        <button class="admin_theme_toggle" id="themeToggle" aria-label="Toggle theme">
          <i class="fas fa-moon"></i>
        </button>
      </div>
    </nav>
  </header>

  <main class="admin_main">
    <!-- SIDEBAR (same as Reports) -->
    <aside class="admin_sidebar">
      <nav class="admin_sidebar_nav">
        <ul>
          <li><a href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
          <li><a href="{{ route('admin.add') }}"><i class="fas fa-plus-circle"></i> Add</a></li>
          <li><a href="{{ route('admin.users') }}" class="active"><i class="fas fa-users"></i> Users</a></li>
          <li><a href="{{ route('admin.partners') }}"><i class="fas fa-handshake"></i> Partners</a></li>
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
      <h2 style="margin-bottom:.6rem">User Statistics</h2>
      <div class="grid stats">
        <div class="stat-card">
          <h3>Total Users</h3>
          <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
        </div>
        <div class="stat-card">
          <h3>New This Month</h3>
          <div class="stat-value">{{ number_format($stats['new_this_month']) }}</div>
        </div>
        <div class="stat-card">
          <h3>Active Users</h3>
          <div class="stat-value">{{ number_format($stats['active_users']) }}</div>
        </div>
        <div class="stat-card">
          <h3>Inactive Users</h3>
          <div class="stat-value">{{ number_format($stats['inactive_users']) }}</div>
        </div>
      </div>

      @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
      @if(session('error'))   <div class="alert alert-error">{{ session('error') }}</div>   @endif

      <!-- Toolbar -->
      <div class="toolbar">
        <div class="title">User Management</div>
        <div class="input">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" id="userSearch" placeholder="Search username or email...">
        </div>
        <button class="btn ghost" id="clearSearch"><i class="fa-solid fa-rotate-left"></i> Reset</button>
      </div>

      <!-- Table -->
      <div class="table-card">
        <div class="table-wrap">
          <table class="admin_table" id="usersTable">
            <thead>
              <tr>
                <th>User ID</th>
                <th>User Image</th>
                <th>Username</th>
                <th>Email</th>
                <th>Joined Date</th>
                <th style="min-width:200px">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($users as $u)
                @php $avatar = $u->user_profile ?: 'assets/images/default-avatar.jpg'; @endphp
                <tr
                  data-phone="{{ $u->phone ?? 'N/A' }}"
                  data-address="{{ $u->address ?? 'N/A' }}"
                >
                  <td>#{{ $u->user_id }}</td>
                  <td>
                    <img src="{{ asset($avatar) }}" alt="{{ e($u->username) }}" class="user-avatar">
                  </td>
                  <td class="col-username">{{ $u->username }}</td>
                  <td class="col-email">{{ $u->email }}</td>
                  <td>{{ \Carbon\Carbon::parse($u->create_time)->format('Y-m-d') }}</td>
                  <td>
                    <div class="action-btns">
                      <button class="action-btn view-btn" onclick="showUserDetails({{ $u->user_id }})">
                        <i class="fa-regular fa-eye"></i> View
                      </button>

                      <form id="form-delete-{{ $u->user_id }}" action="{{ route('admin.users.delete') }}" method="POST" style="display:none;">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $u->user_id }}">
                      </form>
                      <button class="action-btn delete-btn"
                        onclick="confirmDelete({{ $u->user_id }}, '{{ addslashes($u->username) }}')">
                        <i class="fa-regular fa-trash-can"></i> Delete
                      </button>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>

      <!-- User Details Modal -->
      <div id="userDetailsModal" class="admin_modal" aria-modal="true" role="dialog">
        <div class="admin_modal_content">
          <span class="admin_modal_close" onclick="closeModal()">&times;</span>
          <h2 style="margin-bottom:.5rem">User Details</h2>
          <div class="user-details">
            <img id="modalUserImage" src="" alt="User" class="user-details-avatar">
            <div class="user-details-info">
              <div class="user-details-row">
                <span class="user-details-label">User ID:</span>
                <span class="user-details-value" id="modalUserId"></span>
              </div>
              <div class="user-details-row">
                <span class="user-details-label">Username:</span>
                <span class="user-details-value" id="modalUsername"></span>
              </div>
              <div class="user-details-row">
                <span class="user-details-label">Email:</span>
                <span class="user-details-value" id="modalEmail"></span>
              </div>
              <div class="user-details-row">
                <span class="user-details-label">Phone:</span>
                <span class="user-details-value" id="modalPhone"></span>
              </div>
              <div class="user-details-row">
                <span class="user-details-label">Joined Date:</span>
                <span class="user-details-value" id="modalJoinedDate"></span>
              </div>
              <div class="user-details-row">
                <span class="user-details-label">Address:</span>
                <span class="user-details-value" id="modalAddress"></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Delete Confirmation Modal -->
      <div id="deleteConfirmationModal" class="admin_modal" aria-modal="true" role="dialog">
        <div class="admin_modal_content confirmation-modal">
          <span class="admin_modal_close" onclick="closeModal()">&times;</span>
          <h2>Confirm Deletion</h2>
          <p id="confirmationMessage" style="margin:.4rem 0 1rem">Are you sure you want to delete this user?</p>
          <div class="confirmation-buttons">
            <button class="btn confirm-btn" id="confirmDeleteBtn"><i class="fa-solid fa-trash"></i> Confirm Delete</button>
            <button class="btn cancel-btn" onclick="closeModal()">Cancel</button>
          </div>
        </div>
      </div>

    </div>
  </main>

  <script>
    // ===== Theme toggle (same behavior as Reports) =====
    const themeToggle = document.getElementById('themeToggle');
    const icon = themeToggle.querySelector('i');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    const savedTheme = localStorage.getItem('admin-theme');
    if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
      document.body.classList.add('admin-dark-mode'); icon.classList.replace('fa-moon','fa-sun');
    }
    themeToggle.addEventListener('click', () => {
      document.body.classList.toggle('admin-dark-mode');
      if (document.body.classList.contains('admin-dark-mode')) { localStorage.setItem('admin-theme','dark'); icon.classList.replace('fa-moon','fa-sun'); }
      else { localStorage.setItem('admin-theme','light'); icon.classList.replace('fa-sun','fa-moon'); }
    });

    // ===== Client-side search (username/email) =====
    const qInput = document.getElementById('userSearch');
    const clearBtn = document.getElementById('clearSearch');
    const rows = Array.from(document.querySelectorAll('#usersTable tbody tr'));

    function filterUsers() {
      const q = (qInput.value || '').toLowerCase().trim();
      rows.forEach(r => {
        const user = r.querySelector('.col-username')?.textContent.toLowerCase() || '';
        const mail = r.querySelector('.col-email')?.textContent.toLowerCase() || '';
        r.style.display = (user.includes(q) || mail.includes(q)) ? '' : 'none';
      });
    }
    qInput.addEventListener('input', filterUsers);
    clearBtn.addEventListener('click', () => { qInput.value=''; filterUsers(); qInput.focus(); });

    // ===== View modal =====
    function showUserDetails(userId) {
      const all = document.querySelectorAll('#usersTable tbody tr');
      let userRow = null;
      all.forEach(row => { if (row.cells[0]?.textContent === `#${userId}`) userRow = row; });
      if (!userRow) return;

      const data = {
        id: userId,
        username: userRow.cells[2].textContent,
        email: userRow.cells[3].textContent,
        joinedDate: userRow.cells[4].textContent,
        image: userRow.cells[1].querySelector('img').src,
        phone: userRow.dataset.phone || 'N/A',
        address: userRow.dataset.address || 'N/A'
      };

      document.getElementById('modalUserId').textContent = '#' + data.id;
      document.getElementById('modalUsername').textContent = data.username;
      document.getElementById('modalEmail').textContent = data.email;
      document.getElementById('modalJoinedDate').textContent = data.joinedDate;
      document.getElementById('modalUserImage').src = data.image;
      document.getElementById('modalPhone').textContent = data.phone;
      document.getElementById('modalAddress').textContent = data.address;

      document.getElementById('userDetailsModal').style.display = 'flex';
    }

    // ===== Delete modal =====
    let pendingDeleteId = null;
    function confirmDelete(userId, username) {
      pendingDeleteId = userId;
      document.getElementById('confirmationMessage').textContent =
        `Are you sure you want to delete user ${username} (ID: ${userId})? This action cannot be undone.`;
      document.getElementById('deleteConfirmationModal').style.display = 'flex';
    }
    document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
      if (pendingDeleteId !== null) {
        const form = document.getElementById(`form-delete-${pendingDeleteId}`);
        if (form) form.submit();
      }
    });

    // ===== Close modals =====
    function closeModal() {
      document.getElementById('userDetailsModal').style.display = 'none';
      document.getElementById('deleteConfirmationModal').style.display = 'none';
      pendingDeleteId = null;
    }
    window.onclick = function (e) {
      const modals = document.querySelectorAll('.admin_modal');
      modals.forEach(m => { if (e.target === m) m.style.display = 'none'; });
    }
  </script>
</body>
</html>
