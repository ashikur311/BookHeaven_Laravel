@extends('layouts.app')

@section('title', 'bKash Payment')

@section('content')
<div class="container py-4">
  <div class="card shadow-lg mx-auto" style="max-width:520px;">
    <div class="card-header text-white text-center" style="background:#E2136E;">
      <img src="{{ asset('assets/images/bkashlogo.png') }}" alt="bKash" width="90">
      <h5 class="mt-2 mb-0">bKash Payment</h5>
    </div>

    <div class="card-body">
      @if($type === 'book_order')
        <div class="mb-3">
          <div><strong>Order ID:</strong> #{{ $order->order_id }}</div>
          <div><strong>Amount:</strong> ৳{{ number_format($order->total_amount, 2) }}</div>
        </div>
      @else
        <div class="mb-3">
          <div><strong>Plan:</strong> {{ $plan->plan_name }}</div>
          <div><strong>Amount:</strong> ৳{{ number_format($plan->price, 2) }}</div>
        </div>
      @endif

      @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ route('payment.bkash.pay') }}">
        @csrf
        <input type="hidden" name="type" value="{{ $type }}">
        <input type="hidden" name="id" value="{{ $id }}">

        <div class="mb-3">
          <label class="form-label">bKash Number</label>
          <input type="text" name="bkash_number" class="form-control" value="01XXXXXXXXX" required>
        </div>

        <button type="button" id="sendOtpBtn" class="btn btn-outline-primary w-100 mb-3">
          Send OTP to {{ auth()->user()->email }}
        </button>

        <div class="mb-3">
          <label class="form-label">Enter OTP</label>
          <input type="text" name="otp_code" class="form-control" placeholder="6-digit OTP" required>
        </div>

        <button type="submit" class="btn w-100 text-white" style="background:#E2136E;">
          <i class="fas fa-check-circle me-1"></i> Confirm Payment
        </button>
      </form>
    </div>
  </div>
</div>

<script>
document.getElementById('sendOtpBtn').addEventListener('click', async function() {
  const btn = this;
  btn.disabled = true;
  const prev = btn.textContent;
  btn.textContent = 'Sending...';

  try {
    const res = await fetch("{{ route('payment.send.otp') }}", {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ email: "{{ auth()->user()->email }}" })
    });

    const data = await res.json();
    btn.textContent = data.message || 'OTP sent';
  } catch(e) {
    btn.textContent = 'Failed to send. Try again';
    btn.disabled = false;
  }
});
</script>
@endsection
