@extends('layouts.app')
@section('title', 'Partner Dashboard')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/partner_dashboard.css') }}">

<main>
    <aside>
        <div class="nav-logo">
            {{ $partner->username }}
            <div style="font-size: 0.8rem; margin-top: 5px;">
                Partner since {{ \Carbon\Carbon::parse($partner->joined_at)->format('M Y') }}
            </div>
        </div>
        <nav>
            <ul>
                <li><a href="#" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="{{ route('partner.addBook') }}"><i class="fas fa-book"></i> Add Book</a></li>

            </ul>
        </nav>
    </aside>

    <div class="main-content">
        <div class="dashboard-header">
            <h1 class="dashboard-title">Partner Dashboard</h1>
        </div>

        @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
        @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

        <div class="stats-container">
            <div class="stat-card"><div class="stat-title">Total Books</div><div class="stat-value">{{ $totalBooks }}</div></div>
            <div class="stat-card"><div class="stat-title">In Rent</div><div class="stat-value">{{ $inRent }}</div></div>
            <div class="stat-card"><div class="stat-title">Return Requests</div><div class="stat-value">{{ $returnRequests }}</div></div>
            <div class="stat-card"><div class="stat-title">Total Income</div><div class="stat-value">${{ number_format($totalIncome,2) }}</div></div>
        </div>

        <h2 class="section-title">Active Books</h2>
        <table>
            <thead><tr><th>Title</th><th>Writer</th><th>Added</th><th>Revenue</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($activeBooks as $book)
                    <tr>
                        <td>{{ $book->title }}</td>
                        <td>{{ $book->writer }}</td>
                        <td>{{ \Carbon\Carbon::parse($book->added_at)->format('Y-m-d') }}</td>
                        <td>${{ number_format($book->revenue ?? 0, 2) }}</td>
                        <td><span class="status status-{{ str_replace(' ', '-', strtolower($book->status)) }}">{{ ucfirst($book->status) }}</span></td>
                        <td>
                            <form method="POST" action="{{ route('partner.applyReturn') }}">
                                @csrf
                                <input type="hidden" name="book_id" value="{{ $book->id }}">
                                <button type="submit" class="btn btn-primary btn-sm">Apply Return</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">No active books found</td></tr>
                @endforelse
            </tbody>
        </table>

        <h2 class="section-title">Return Book Requests</h2>
        <table>
            <thead><tr><th>Title</th><th>Writer</th><th>Added</th><th>Revenue</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($returnBooks as $book)
                    <tr>
                        <td>{{ $book->title }}</td>
                        <td>{{ $book->writer }}</td>
                        <td>{{ \Carbon\Carbon::parse($book->added_at)->format('Y-m-d') }}</td>
                        <td>${{ number_format($book->revenue ?? 0, 2) }}</td>
                        <td><span class="status status-return">{{ ucfirst(str_replace('_', ' ', $book->status)) }}</span></td>
                        <td>
                            <form method="POST" action="{{ route('partner.deleteReturn') }}">
                                @csrf
                                <input type="hidden" name="book_id" value="{{ $book->id }}">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">No return requests found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</main>
@endsection
