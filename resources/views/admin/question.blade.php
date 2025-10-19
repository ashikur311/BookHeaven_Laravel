<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard — User Questions</title>
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

    /* ========= HEADER (identical to Reports) ========= */
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
    .chip{ font-size:.75rem; padding:.25rem .5rem; border-radius:999px; background:var(--chip); color:var(--text-muted); }

    /* ========= LAYOUT / SIDEBAR (sticky + scroll) ========= */
    .admin_main{display:grid; grid-template-columns:260px 1fr; min-height:calc(100vh - 64px)}
    .admin_sidebar{
      background:#111827; color:#cbd5e1; border-right:1px solid rgba(255,255,255,.06);
      position:sticky; top:64px; align-self:start; height:calc(100vh - 64px); overflow-y:auto;
    }
    .admin_sidebar_nav ul{list-style:none; padding:.75rem}
    .admin_sidebar_nav a, .admin_sidebar_nav button.linklike{
      display:flex; align-items:center; gap:.75rem;
      padding:.65rem .75rem; margin-bottom:.25rem; text-decoration:none; color:inherit;
      border-radius:.5rem; border:none; background:transparent; width:100%; text-align:left; cursor:pointer;
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
    .title{ font-weight:800; font-size:1.05rem }
    .muted{ color:var(--text-muted) }
    .pad{ padding:1.25rem }

    /* ========= TOOLBAR ========= */
    .toolbar{ display:flex; flex-wrap:wrap; gap:.6rem }
    .control{
      display:flex; align-items:center; gap:.5rem;
      background:var(--bg); border:1px solid var(--border); border-radius:.65rem; padding:.55rem .7rem;
    }
    .control input, .control select{
      border:none; outline:none; background:transparent; color:var(--text); font:inherit;
    }
    .btn{ padding:.55rem .9rem; border:none; border-radius:.55rem; cursor:pointer; font-weight:700 }
    .btn.primary{ background:var(--primary); color:#fff }
    .btn.ghost{ background:transparent; border:1px solid var(--border); color:var(--text) }

    /* ========= QUESTION CARDS ========= */
    .qcard{ background:var(--card); border:1px solid var(--border); border-radius:12px; box-shadow:var(--shadow); overflow:hidden }
    .qhead{ padding:1rem 1.25rem; background:var(--table-header); border-bottom:1px solid var(--border) }
      .qmeta{ display:flex; justify-content:space-between; gap:1rem; font-size:.92rem; color:var(--text-muted) }
      .quser{ display:flex; align-items:center; gap:.6rem; font-weight:700; color:var(--primary) }
      .uimg{ width:30px; height:30px; border-radius:999px; object-fit:cover }
      .book{ color:var(--success); font-weight:800 }
    .qbody{ padding:1rem 1.25rem }
      .qtext{ font-size:1.05rem; line-height:1.6 }

    .asection{ padding:1rem 1.25rem; background:var(--table-hover); border-top:1px solid var(--border) }
    .adisp{ background:var(--card); border:1px solid var(--border); border-radius:.8rem; padding:1rem; display:grid; gap:.5rem }
    .ameta{ display:flex; justify-content:space-between; gap:1rem; font-size:.9rem; color:var(--text-muted) }
    .aname{ font-weight:800; color:#a78bfa } /* violet-ish like earlier */
    .atext{ line-height:1.6 }

    .afrm{ margin-top:.75rem }
    .afrm textarea{
      width:100%; min-height:140px; resize:vertical; background:var(--bg);
      border:1px solid var(--border); border-radius:.65rem; padding:.75rem; color:var(--text);
      font:inherit;
    }
    .aactions{ display:flex; justify-content:flex-end; gap:.5rem; margin-top:.6rem }
    .btn.danger{ background:var(--danger); color:#fff }
    .btn.secondary{ background:transparent; border:1px solid var(--border); color:var(--text) }

    /* ========= ALERTS / EMPTY ========= */
    .alert{ padding:.9rem 1rem; border-radius:.6rem; margin:1rem 0; border:1px solid }
    .alert-success{ background:#dcfce7; color:#14532d; border-color:#bbf7d0 }
    .alert-error{ background:#fee2e2; color:#7f1d1d; border-color:#fecaca }
    .empty{ text-align:center; padding:2rem; color:var(--text-muted) }

    /* ========= GRID HELPERS ========= */
    .grid{ display:grid; gap:1rem }
    .list{ display:grid; gap:1rem }

    @media (max-width:900px){
      .admin_main{ grid-template-columns:1fr }
      .admin_sidebar{ display:none }
    }
  </style>
</head>
<body>
<header class="admin_header">
  <div class="logo"><img src="{{ asset('assets/images/download.png') }}" alt="Logo"></div>
  <div class="admin_header_right">
    <h1>User Questions</h1>
    <span class="chip">Moderation</span>
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
        <li><a href="{{ route('admin.subscription') }}"><i class="fas fa-star"></i> Subscription</a></li>
        <li><a href="{{ route('admin.events') }}"><i class="fas fa-calendar-alt"></i> Events</a></li>
        <li><a href="{{ route('admin.community') }}"><i class="fas fa-users"></i> Community</a></li>
        <li><a href="{{ route('admin.question') }}" class="active"><i class="fa-solid fa-question"></i> User Questions</a></li>
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

    {{-- Alerts --}}
    @if (session('error_message'))
      <div class="alert alert-error">{{ session('error_message') }}</div>
    @endif
    @if (session('success_message'))
      <div class="alert alert-success">{{ session('success_message') }}</div>
    @endif

    {{-- Toolbar --}}
    <div class="section">
      <div class="section-head">
        <div class="title">Filter & Search</div>
        <span class="muted">Quickly find questions that need attention</span>
      </div>
      <div class="pad">
        <div class="toolbar">
          <div class="control">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input id="searchInput" type="text" placeholder="Search by text, user, or book…">
          </div>
          <div class="control">
            <i class="fa-solid fa-filter"></i>
            <select id="statusFilter">
              <option value="all">All</option>
              <option value="answered">Answered</option>
              <option value="unanswered">Unanswered</option>
            </select>
          </div>
          <button class="btn ghost" id="clearFilters"><i class="fa-solid fa-rotate"></i> Reset</button>
        </div>
      </div>
    </div>

    {{-- List --}}
    <div class="grid">
      @php
        $isEmpty = empty($questions) || (is_object($questions) && method_exists($questions,'isEmpty') && $questions->isEmpty());
      @endphp

      @if ($isEmpty)
        <div class="section">
          <div class="pad empty">
            <i class="fas fa-question-circle" style="font-size:3rem; opacity:.5;"></i>
            <h3 style="margin-top:.5rem;">No questions found</h3>
            <p>There are currently no user questions to display.</p>
          </div>
        </div>
      @else
        <div class="list" id="questionsList">
          @foreach ($questions as $q)
            @php
              $qid            = is_array($q) ? $q['question_id']     : $q->question_id;
              $qtext          = is_array($q) ? $q['question_text']   : $q->question_text;
              $qcreated       = is_array($q) ? $q['created_at']      : $q->created_at;
              $username       = is_array($q) ? $q['username']        : $q->username;
              $user_id        = is_array($q) ? $q['user_id']         : $q->user_id;
              $book_title     = is_array($q) ? $q['book_title']      : $q->book_title;
              $answer_id      = is_array($q) ? ($q['answer_id'] ?? null)      : ($q->answer_id ?? null);
              $answer_text    = is_array($q) ? ($q['answer_text'] ?? '')      : ($q->answer_text ?? '');
              $answer_created = is_array($q) ? ($q['answer_created'] ?? null) : ($q->answer_created ?? null);
              $admin_name     = is_array($q) ? ($q['admin_name'] ?? 'Admin')  : ($q->admin_name ?? 'Admin');
              try { $askedOn = \Carbon\Carbon::parse($qcreated)->format('M j, Y \a\t g:i a'); }
              catch (\Throwable $e) { $askedOn = e($qcreated); }
              $status = !empty($answer_id) ? 'answered' : 'unanswered';
            @endphp

            <article class="qcard"
                     data-status="{{ $status }}"
                     data-search="{{ Str::lower($qtext.' '.$username.' '.$book_title) }}">
              <header class="qhead">
                <div class="qmeta">
                  <div class="quser">
                    <img class="uimg"
                         src="{{ asset('assets/user_image/'.$user_id.'.jpg') }}"
                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($username) }}&background=random'"
                         alt="{{ $username }}">
                    <span>{{ $username }}</span>
                  </div>
                  <div class="muted">Asked on {{ $askedOn }}</div>
                </div>
                <h3 style="margin-top:.6rem;font-size:1.05rem;">About:
                  <span class="book">{{ $book_title }}</span>
                </h3>
              </header>

              <div class="qbody">
                <div class="qtext">{!! nl2br(e($qtext)) !!}</div>
              </div>

              <div class="asection">
                @if (!empty($answer_id))
                  <div class="adisp" id="answer-card-{{ $answer_id }}">
                    <div class="ameta">
                      <div class="aname"><i class="fas fa-user-shield"></i> Answered by {{ $admin_name }}</div>
                      <div class="muted">
                        @php
                          try { $adt = \Carbon\Carbon::parse($answer_created)->format('M j, Y \a\t g:i a'); }
                          catch (\Throwable $e) { $adt = e($answer_created); }
                        @endphp
                        {{ $adt }}
                      </div>
                    </div>

                    <div class="atext" id="answer-text-{{ $answer_id }}">
                      {!! nl2br(e($answer_text)) !!}
                    </div>

                    {{-- Inline edit form --}}
                    <form method="POST"
                          action="{{ route('admin.question.answer.update', $answer_id) }}"
                          class="afrm"
                          id="answer-edit-form-{{ $answer_id }}"
                          style="display:none;">
                      @csrf
                      @method('PUT')
                      <textarea name="answer_text" required>{{ $answer_text }}</textarea>
                      <div class="aactions">
                        <button type="button" class="btn secondary" onclick="toggleEdit({{ $answer_id }}, false)">Cancel</button>
                        <button type="submit" class="btn primary"><i class="fas fa-save"></i> Save</button>
                      </div>
                    </form>

                    <div class="aactions" id="answer-actions-{{ $answer_id }}">
                      <button class="btn ghost" onclick="toggleEdit({{ $answer_id }}, true)">
                        <i class="fas fa-edit"></i> Edit
                      </button>
                      <form method="POST"
                            action="{{ route('admin.question.answer.destroy', $answer_id) }}"
                            onsubmit="return confirm('Delete this answer?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn danger">
                          <i class="fas fa-trash"></i> Delete
                        </button>
                      </form>
                    </div>
                  </div>
                @else
                  <h4 style="margin-bottom:.6rem;"><i class="fas fa-reply"></i> Write an answer</h4>
                  <form method="POST" action="{{ route('admin.question.answer.store') }}" class="afrm">
                    @csrf
                    <input type="hidden" name="question_id" value="{{ $qid }}">
                    <textarea name="answer_text" placeholder="Type your answer here…" required></textarea>
                    <div class="aactions">
                      <button type="submit" class="btn primary">
                        <i class="fas fa-paper-plane"></i> Submit Answer
                      </button>
                    </div>
                  </form>
                @endif
              </div>
            </article>

          @endforeach
        </div>
      @endif
    </div>
  </div>
</main>

<script>
  // Theme toggle (consistent with Reports)
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

  // Inline edit toggle
  function toggleEdit(id, show) {
    const form = document.getElementById('answer-edit-form-' + id);
    const text = document.getElementById('answer-text-' + id);
    const actions = document.getElementById('answer-actions-' + id);
    if (!form || !text || !actions) return;

    if (show) {
      form.style.display = 'block';
      text.style.display = 'none';
      actions.style.display = 'none';
    } else {
      form.style.display = 'none';
      text.style.display = 'block';
      actions.style.display = 'flex';
    }
  }

  // Client-side filter/search (no backend roundtrip)
  const list = document.getElementById('questionsList');
  const searchInput = document.getElementById('searchInput');
  const statusFilter = document.getElementById('statusFilter');
  const clearFilters = document.getElementById('clearFilters');

  function applyFilters(){
    if (!list) return;
    const term = (searchInput?.value || '').toLowerCase().trim();
    const status = statusFilter?.value || 'all';
    const cards = list.querySelectorAll('.qcard');

    cards.forEach(card => {
      const haystack = (card.getAttribute('data-search') || '').toLowerCase();
      const s = card.getAttribute('data-status') || 'unanswered';
      const matchText = !term || haystack.includes(term);
      const matchStatus = status === 'all' || s === status;
      card.style.display = (matchText && matchStatus) ? '' : 'none';
    });
  }

  searchInput?.addEventListener('input', applyFilters);
  statusFilter?.addEventListener('change', applyFilters);
  clearFilters?.addEventListener('click', () => {
    if (searchInput) searchInput.value = '';
    if (statusFilter) statusFilter.value = 'all';
    applyFilters();
  });
</script>
</body>
</html>
