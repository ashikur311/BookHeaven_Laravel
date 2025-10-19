@extends('layouts.app')

@section('title', 'User Profile | Book Heaven')

@section('content')
<main>
    @include('profile.sidebar')

    <div class="user_profile_content">
        {{-- Stats section --}}
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Books Purchased</h3>
                <p>{{ $totalBooks }}</p>
            </div>
            <div class="stat-card">
                <h3>Active Subscriptions</h3>
                <p>{{ $activeSubs }}</p>
            </div>
            <div class="stat-card">
                <h3>Partner Status</h3>
                <p>{{ $partnerStatus }}</p>
            </div>
            <div class="stat-card">
                <h3>Total Spent</h3>
                <p>${{ number_format($totalSpent, 2) }}</p>
            </div>
        </div>

        {{-- Chart --}}
        <div class="chart-container">
            <canvas id="purchaseChart"></canvas>
        </div>

        {{-- Profile info --}}
        <div class="profile-info">
            <h2>Profile Information</h2>
            <div class="info-grid">
                <div>
                    <div class="info-item">
                        <label>Username</label>
                        <p>{{ $user->username }}</p>
                    </div>
                    <div class="info-item">
                        <label>Email</label>
                        <p>{{ $user->email }}</p>
                    </div>
                    <div class="info-item">
                        <label>Phone</label>
                        <p>{{ $user->phone ?? 'Not specified' }}</p>
                    </div>
                </div>

                <div>
                    <div class="info-item">
                        <label>Date of Birth</label>
                        <p>{{ $user->birthday ? \Carbon\Carbon::parse($user->birthday)->format('F j, Y') : 'Not specified' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Address</label>
                        <p>{{ $user->address ?? 'Not specified' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Member Since</label>
                        <p>{{ \Carbon\Carbon::parse($user->create_time)->format('F Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<link rel="stylesheet" href="{{ asset('css/user_profile.css') }}">

<script>
document.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('purchaseChart').getContext('2d');
    const purchaseChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Books Purchased',
                data: [
                    @for ($i = 1; $i <= 12; $i++)
                        {{ $monthlyData[$i] }},
                    @endfor
                ],
                backgroundColor: 'rgba(87, 171, 210, 0.2)',
                borderColor: 'rgba(87, 171, 210, 1)',
                borderWidth: 2,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--text-color')
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--text-color')
                    },
                    grid: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--accent-color')
                    }
                },
                x: {
                    ticks: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--text-color')
                    },
                    grid: {
                        color: getComputedStyle(document.documentElement).getPropertyValue('--accent-color')
                    }
                }
            }
        }
    });
});
</script>
@endsection
