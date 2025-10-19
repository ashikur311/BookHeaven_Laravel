@extends('layouts.app')

@section('title', 'Account Settings')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/user_setting.css') }}">

<main class="settings-page">

    {{-- Sidebar --}}
    <aside>
        <section class="user-info">
            <img src="{{ asset($user->user_profile ?? 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=800&q=80') }}"
                alt="{{ $user->username }}" class="user-avatar">
            <div>
                <div class="user-name">{{ $user->username }}</div>
                <small>Member since: {{ \Carbon\Carbon::parse($user->created_at)->format('M Y') }}</small>
            </div>
        </section>

        <nav>
            <ul>
                <li><a href="{{ route('profile') }}"><i class="fas fa-user"></i> Profile</a></li>
                <li><a href="{{ route('wishlist') }}"><i class="fas fa-heart"></i> Wish List</a></li>
                <li><a href="{{ route('orders') }}"><i class="fas fa-shopping-bag"></i> My Orders</a></li>
                <li><a href="{{ route('subscription') }}"><i class="fas fa-calendar-check"></i> My Subscription</a></li>
                <li><a href="{{ route('settings') }}" class="active"><i class="fas fa-cog"></i> Setting</a></li>
                <li><a href="{{ route('logout') }}"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </aside>

    {{-- Main Settings Area --}}
    <div class="settings_content">
        <div class="settings-container">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @elseif(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            {{-- Tabs --}}
            <div class="settings-tabs">
                <button class="tab-btn active" onclick="openTab(event, 'profile')">Profile Settings</button>
                <button class="tab-btn" onclick="openTab(event, 'password')">Password</button>
                <button class="tab-btn" onclick="openTab(event, 'billing')">Billing Address</button>
                <button class="tab-btn" onclick="openTab(event, 'payment')">Payment Methods</button>
                <button class="tab-btn" onclick="openTab(event, 'verification')">Two-Step Verification</button>
            </div>

            {{-- Profile Tab --}}
            <div id="profile" class="tab-content active">
                <div class="section-header">
                    <h2>Profile Settings</h2>
                </div>

                <form method="POST" action="{{ route('settings.updateProfile') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="profile-image-upload">
                        <img src="{{ asset($user->user_profile ?? 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&w=800&q=80') }}"
                             alt="{{ $user->username }}" class="current-profile-image" id="profileImagePreview">

                        <div class="image-upload-controls">
                            <div class="file-input-wrapper">
                                <button type="button" class="btn btn-primary">Upload New Photo</button>
                                <input type="file" id="profileImage" name="profile_image" accept="image/*" onchange="previewImage(this)">
                            </div>

                            @if(!empty($user->user_profile) && !str_contains($user->user_profile, 'https://'))
                                <button type="submit" formaction="{{ route('settings.removeProfileImage') }}" class="btn btn-outline">Remove Photo</button>
                            @endif
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="{{ $user->phone }}">
                        </div>
                        <div class="form-group">
                            <label>Date of Birth</label>
                            <input type="date" name="birthday" class="form-control" value="{{ $user->birthday }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" class="form-control">{{ $user->address }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>

            {{-- Password Tab --}}
            <div id="password" class="tab-content">
                <div class="section-header">
                    <h2>Change Password</h2>
                </div>
                <form method="POST" action="{{ route('settings.changePassword') }}">
                    @csrf
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="new_password_confirmation" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </form>
            </div>

            {{-- Billing Tab --}}
            <div id="billing" class="tab-content">
                <div class="section-header">
                    <h2>Billing Address</h2>
                </div>

                <form method="POST" action="{{ route('settings.updateBilling') }}">
                    @csrf
                    <div class="form-group">
                        <label>Street Address</label>
                        <input type="text" name="street_address" class="form-control" value="{{ $billing->street_address ?? '' }}" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city" class="form-control" value="{{ $billing->city ?? '' }}" required>
                        </div>
                        <div class="form-group">
                            <label>Division</label>
                            <select name="division" class="form-control" required>
                                <option value="">Select Division</option>
                                @foreach($divisions as $division)
                                    <option value="{{ $division }}" {{ ($billing->division ?? '') === $division ? 'selected' : '' }}>
                                        {{ $division }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>ZIP Code</label>
                            <input type="text" name="zip_code" class="form-control" value="{{ $billing->zip_code ?? '' }}" required>
                        </div>
                        <div class="form-group">
                            <label>Country</label>
                            <input type="text" value="Bangladesh" readonly class="form-control">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Address</button>
                </form>
            </div>

            {{-- Payment Methods Tab --}}
            <div id="payment" class="tab-content">
                <div class="section-header">
                    <h2>Payment Methods</h2>
                </div>

                @forelse($payments as $method)
                    <div class="payment-method">
                        <i class="fab fa-cc-{{ strtolower($method->card_type) }} payment-method-icon"></i>
                        <div class="payment-method-details">
                            <h4>{{ ucfirst($method->card_type) }} ending in {{ substr($method->card_number, -4) }}</h4>
                            <p>Expires {{ $method->expiry_date }}</p>
                        </div>
                        <form method="POST" action="{{ url('/settings/delete-payment/'.$method->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-outline">Remove</button>
                        </form>
                    </div>
                @empty
                    <p>No payment methods saved yet.</p>
                @endforelse

                <form method="POST" action="{{ route('settings.addPayment') }}" class="mt-4">
                    @csrf
                    <div class="form-row">
                        <div class="form-group">
                            <label>Card Type</label>
                            <select name="card_type" class="form-control" required>
                                <option value="visa">Visa</option>
                                <option value="mastercard">MasterCard</option>
                                <option value="amex">American Express</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Card Name</label>
                            <input type="text" name="card_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Card Number</label>
                            <input type="text" name="card_number" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Expiry Date (MM/YY)</label>
                            <input type="text" name="expiry_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>CVV</label>
                            <input type="text" name="cvv" class="form-control" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Payment</button>
                </form>
            </div>

            {{-- Two-Step Verification Tab --}}
            <div id="verification" class="tab-content">
                <div class="section-header">
                    <h2>Two-Step Verification</h2>
                </div>
                <div class="verification-status">
                    <h3>Status:</h3>
                    <span class="verification-badge {{ $user->two_step_verification ? 'badge-success' : 'badge-warning' }}">
                        {{ $user->two_step_verification ? 'Enabled' : 'Not Enabled' }}
                    </span>
                </div>
                <form method="POST" action="{{ route('settings.updateVerification') }}">
                    @csrf
                    <div class="form-check">
                        <input type="checkbox" name="two_step_verification" id="two_step_verification"
                               {{ $user->two_step_verification ? 'checked' : '' }}>
                        <label for="two_step_verification">Enable Two-Step Verification</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </form>
            </div>
        </div>
    </div>
</main>

<script>
function openTab(evt, tabName) {
    document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById(tabName).classList.add('active');
    evt.currentTarget.classList.add('active');
}

function previewImage(input) {
    const preview = document.getElementById('profileImagePreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => preview.src = e.target.result;
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
