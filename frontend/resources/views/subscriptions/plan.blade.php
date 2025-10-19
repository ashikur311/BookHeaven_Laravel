@extends('layouts.app')

@section('title', 'Subscription Plans')

@section('content')
<main class="subscription-page">
    <section class="current-plan">
        <h2><i class="fas fa-id-card"></i> Your Current Plans</h2>

        @if($currentPlans->isNotEmpty())
            <table>
                <thead>
                    <tr>
                        <th>Plan Name</th>
                        <th>Price</th>
                        <th>Valid Until</th>
                        <th>Books Available</th>
                        <th>Audio Books</th>
                        <th>Books Left</th>
                        <th>Audio Books Left</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($currentPlans as $plan)
                        <tr>
                            <td>{{ $plan->plan_name }}</td>
                            <td>${{ number_format($plan->price, 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($plan->end_date)->format('M d, Y') }}</td>
                            <td>{{ $plan->book_quantity }}</td>
                            <td>{{ $plan->audiobook_quantity }}</td>
                            <td>{{ $plan->available_rent_book ?? 0 }}</td>
                            <td>{{ $plan->available_audio ?? 0 }}</td>
                            <td class="{{ $plan->subscription_status === 'active' ? 'status-active' : 'status-expired' }}">
                                {{ ucfirst($plan->subscription_status) }}
                            </td>
                            <td>
                                @if($plan->subscription_status === 'active')
                                    <button class="action-btn"
                                        onclick="window.location.href='{{ url('/book_add_to_subscription?plan_type=' . urlencode(strtolower($plan->plan_name)) . '&sub_id=' . $plan->user_subscription_id) }}'">
                                        <i class="fas fa-plus"></i> Add Book
                                    </button>
                                @else
                                    <button class="action-btn warning"
                                        onclick="openModal(
                                            '{{ addslashes($plan->plan_name) }}',
                                            '{{ $plan->price }}',
                                            '{{ $plan->book_quantity }}',
                                            '{{ $plan->audiobook_quantity }}',
                                            '{{ $plan->validity_days }}',
                                            '{{ $plan->plan_id }}'
                                        )">
                                        <i class="fas fa-sync-alt"></i> Renew
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="no-plan">You don't have any active subscription plans. Please subscribe to access books and audio books.</p>
        @endif
    </section>

    <section class="available-plans">
        <h2><i class="fas fa-crown"></i> Available Plans</h2>

        <div class="plans-grid">
            @foreach($plans as $plan)
                @php
                    $hasActive = false;
                    $hasExpired = false;
                    foreach ($currentPlans as $current) {
                        if ($current->plan_id == $plan->plan_id) {
                            if ($current->subscription_status === 'active') $hasActive = true;
                            else $hasExpired = true;
                        }
                    }
                @endphp

                <div class="plan-card {{ $plan->plan_id == 2 ? 'popular' : '' }}">
                    @if($plan->plan_id == 2)
                        <div class="popular-badge">Popular</div>
                    @endif

                    <h3 class="plan-name">{{ $plan->plan_name }}</h3>
                    <p class="plan-price">
                        ${{ number_format($plan->price, 2) }}
                        <span>/{{ $plan->validity_days > 30 ? 'year' : 'month' }}</span>
                    </p>

                    <div class="plan-features">
                        <div class="plan-feature"><i class="fas fa-check-circle"></i> {{ $plan->book_quantity }} Books</div>
                        <div class="plan-feature"><i class="fas fa-check-circle"></i> {{ $plan->audiobook_quantity }} Audio Books</div>
                        <div class="plan-feature"><i class="fas fa-check-circle"></i> {{ $plan->validity_days }} Days Validity</div>
                        <div class="plan-feature"><i class="fas fa-check-circle"></i> 
                            {{ $plan->plan_id == 1 ? 'Standard' : 'Priority' }} Support
                        </div>
                    </div>

                    @if($hasActive)
                        <button class="subscribe-btn success" disabled>
                            <i class="fas fa-check"></i> Subscribed
                        </button>
                    @elseif($hasExpired)
                        <button class="subscribe-btn warning"
                            onclick="openModal(
                                '{{ addslashes($plan->plan_name) }}',
                                '{{ $plan->price }}',
                                '{{ $plan->book_quantity }}',
                                '{{ $plan->audiobook_quantity }}',
                                '{{ $plan->validity_days }}',
                                '{{ $plan->plan_id }}'
                            )">
                            <i class="fas fa-sync-alt"></i> Renew
                        </button>
                    @else
                        <button class="subscribe-btn"
                            onclick="openModal(
                                '{{ addslashes($plan->plan_name) }}',
                                '{{ $plan->price }}',
                                '{{ $plan->book_quantity }}',
                                '{{ $plan->audiobook_quantity }}',
                                '{{ $plan->validity_days }}',
                                '{{ $plan->plan_id }}'
                            )">
                            <i class="fas fa-crown"></i> Subscribe
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
    </section>
</main>
<link rel="stylesheet" href="{{ asset('css/subscriptions_plan.css') }}">

@include('subscriptions.modal')

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    let selectedPlanId = null;
    let selectedPaymentMethod = null;
    const currentPlans = @json($currentPlans);

    // Check if user already has subscription
    function checkExistingSubscription(planId) {
        const existing = currentPlans.find(plan => plan.plan_id == planId);
        if (existing) return { exists: true, status: existing.subscription_status };
        return { exists: false };
    }

    // Open modal
    window.openModal = function (planName, price, books, audioBooks, validity, planId) {
        const subCheck = checkExistingSubscription(planId);
        if (subCheck.exists && subCheck.status === 'active') {
            alert('You already have an active subscription for this plan.');
            return;
        }

        document.getElementById('modalPlanName').textContent = planName;
        document.getElementById('modalPlanPrice').textContent = parseFloat(price).toFixed(2);
        document.getElementById('modalBookQuantity').textContent = books;
        document.getElementById('modalAudioBookQuantity').textContent = audioBooks;
        document.getElementById('modalValidityDays').textContent = validity;

        selectedPlanId = planId;
        selectedPaymentMethod = null;

        const modal = document.getElementById('subscriptionModal');
        modal.style.display = 'flex';
        document.getElementById('confirmSubscription').disabled = true;
        document.getElementById('paymentDetails').style.display = 'none';

        const confirmBtn = document.getElementById('confirmSubscription');
        if (subCheck.exists && subCheck.status === 'expired') {
            confirmBtn.textContent = 'Renew Subscription';
            confirmBtn.classList.add('warning');
        } else {
            confirmBtn.textContent = 'Confirm Subscription';
            confirmBtn.classList.remove('warning');
        }

        document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('selected'));
    }

    // Close modal
    window.closeModal = function () {
        document.getElementById('subscriptionModal').style.display = 'none';
        selectedPlanId = null;
        selectedPaymentMethod = null;
    }

    // Payment method select
    document.querySelectorAll('.payment-option').forEach(option => {
        option.addEventListener('click', function () {
            document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            selectedPaymentMethod = this.dataset.method;
            document.getElementById('confirmSubscription').disabled = false;

            const details = document.getElementById('paymentDetails');
            if (selectedPaymentMethod === 'bkash') {
                details.innerHTML = `
                    <div style="background: var(--even-row-bg); padding: 1rem; border-radius: 6px;">
                        <p><strong>bKash Merchant:</strong> 01777895XXX<br>
                        Amount: $${document.getElementById('modalPlanPrice').textContent}</p>
                    </div>`;
            } else {
                details.innerHTML = `
                    <div style="background: var(--even-row-bg); padding: 1rem; border-radius: 6px;">
                        <p>You will be redirected to our secure payment gateway.</p>
                    </div>`;
            }
            details.style.display = 'block';
        });
    });

    // Confirm subscription
    document.getElementById('confirmSubscription').addEventListener('click', function () {
        if (selectedPlanId && selectedPaymentMethod) {
            window.location.href = `/payment/${selectedPaymentMethod}?type=subscription&id=${selectedPlanId}`;
        }
    });

    // Click outside modal
    window.onclick = function (event) {
        const modal = document.getElementById('subscriptionModal');
        if (event.target === modal) closeModal();
    }
});
</script>
@endsection
