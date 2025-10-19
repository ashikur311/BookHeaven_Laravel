@extends('layouts.app')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card p-4 shadow-sm border-0 rounded-4">
        <h4 class="text-center mb-3">Two-Step Verification</h4>
        <p class="text-muted text-center mb-4">
          A 6-digit OTP was sent to <strong>{{ session('2fa_email') }}</strong>.
        </p>

        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        @if(session('info'))
          <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        <form method="POST" action="{{ route('verify.2fa.post') }}">
          @csrf
          <div class="mb-3">
            <input type="text" name="otp_code" class="form-control text-center fs-4"
                   maxlength="6" placeholder="Enter 6-digit OTP" required>
          </div>
          <button class="btn btn-primary w-100">Verify & Continue</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
