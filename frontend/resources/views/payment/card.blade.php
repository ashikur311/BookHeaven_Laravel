@extends('layouts.app')

@section('title', 'Secure Payment | Book Heaven')

@section('content')
<div class="container">
    <div class="payment-container">
        <div class="payment-header">
            <h3><i class="far fa-credit-card"></i> Secure Payment</h3>
        </div>
        
        <div class="payment-body">
            {{-- ✅ Success Message --}}
            @if(session('success'))
                <div class="alert alert-success">
                    <h4 class="alert-heading">Payment Successful!</h4>
                    <p>{{ session('success') }}</p>
                    <p>You will be redirected shortly...</p>
                    <div class="progress mt-3">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                    </div>
                </div>
                <script>
                    setTimeout(() => {
                        window.location.href = "{{ session('redirect') ?? route('home') }}";
                    }, 3000);
                </script>
            @else
                <div class="payment-summary">
                    <h5><i class="fas fa-receipt"></i> Order Summary</h5>
                    <p><strong>Description:</strong> {{ $description }}</p>
                    <p><strong>Amount:</strong> ৳{{ number_format($amount, 2) }}</p>
                    <p><strong>Customer:</strong> {{ Auth::user()->username ?? Auth::user()->name ?? 'User' }}</p>
                </div>

                {{-- ✅ Error Message --}}
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- ✅ Payment Form --}}
                <form method="POST" action="{{ route('payment.card.pay') }}">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">
                    <input type="hidden" name="id" value="{{ $id }}">

                    <h5 class="mb-3"><i class="fas fa-credit-card"></i> Payment Method</h5>

                    @if($savedCards->count() > 0)
                        <div class="mb-4">
                            @foreach($savedCards as $card)
                                @php
                                    $last4 = substr($card->card_number, -4);
                                    $cardType = strtolower($card->card_type);
                                @endphp
                                <div class="saved-card-option">
                                    <input type="radio" name="saved_card_id" id="card_{{ $card->id }}"
                                           value="{{ $card->id }}" {{ $loop->first ? 'checked' : '' }}>
                                    <div class="saved-card-details">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="card-type">
                                                <i class="fab fa-cc-{{ $cardType }}"></i>
                                                {{ ucfirst($cardType) }}
                                            </span>
                                            <span class="card-number">•••• •••• •••• {{ $last4 }}</span>
                                            <span class="card-expiry ms-auto">Exp: {{ $card->expiry_date }}</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <small class="text-muted">{{ $card->card_name }}</small>
                                            <div class="ms-auto">
                                                <small>CVV:</small>
                                                <input type="password" name="saved_card_cvv"
                                                       class="form-control form-control-sm card-cvv-input"
                                                       placeholder="•••" maxlength="4"
                                                       {{ !$loop->first ? 'disabled' : '' }} required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="saved-card-option">
                                <input type="radio" name="saved_card_id" id="card_new" value="new">
                                <label for="card_new" class="ms-2 fw-bold">
                                    <i class="fas fa-plus-circle me-2"></i>Use a new card
                                </label>
                            </div>
                        </div>
                    @endif

                    <div id="newCardForm" class="{{ $savedCards->count() ? 'new-card-form' : '' }}">
                        <div class="card-icons">
                            <img src="{{ asset('assets/images/visa.png') }}" alt="Visa">
                            <img src="{{ asset('assets/images/mastercard.png') }}" alt="Mastercard">
                            <img src="{{ asset('assets/images/amex.png') }}" alt="Amex">
                            <img src="{{ asset('assets/images/discover.png') }}" alt="Discover">
                        </div>

                        <div class="mb-3">
                            <label for="card_name" class="form-label">Cardholder Name</label>
                            <input type="text" class="form-control" id="card_name" name="card_name"
                                   placeholder="Name on card" {{ $savedCards->count() ? 'disabled' : '' }} required>
                        </div>

                        <div class="mb-3">
                            <label for="card_number" class="form-label">Card Number</label>
                            <input type="text" class="form-control" id="card_number" name="card_number"
                                   placeholder="1234 5678 9012 3456" maxlength="19"
                                   {{ $savedCards->count() ? 'disabled' : '' }} required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="card_expiry" class="form-label">Expiry Date</label>
                                <input type="text" class="form-control" id="card_expiry" name="card_expiry"
                                       placeholder="MM/YY" {{ $savedCards->count() ? 'disabled' : '' }} required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="card_cvv" class="form-label">Security Code (CVV)</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="card_cvv" name="card_cvv"
                                           placeholder="•••" maxlength="4"
                                           {{ $savedCards->count() ? 'disabled' : '' }} required>
                                    <span class="input-group-text">
                                        <i class="fas fa-question-circle" title="3 or 4 digit code on back of card"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="save_card" id="save_card" value="1"
                                   {{ $savedCards->count() ? '' : 'checked' }}>
                            <label class="form-check-label" for="save_card">
                                <i class="fas fa-save me-1"></i> Save this card for future payments
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-pay">
                        <i class="fas fa-lock me-2"></i> Pay ৳{{ number_format($amount, 2) }}
                    </button>

                    <div class="security-note">
                        <i class="fas fa-shield-alt"></i> Your payment is secured with 256-bit SSL encryption
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

{{-- ======================= STYLES (Exact Copy from PHP) ======================= --}}
<style>
:root {
    --primary-color: #4361ee;
    --secondary-color: #3f37c9;
    --accent-color: #4895ef;
    --light-color: #f8f9fa;
    --dark-color: #212529;
    --success-color: #4cc9f0;
    --danger-color: #f72585;
}
body {
    background-color: #f5f7ff;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}
.payment-container {
    max-width: 600px;
    margin: 2rem auto;
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}
.payment-header {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    padding: 1.5rem;
    text-align: center;
}
.payment-header h3 i { margin-right: 10px; }
.payment-body { padding: 2rem; }
.payment-summary {
    background-color: var(--light-color);
    border-radius: 10px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border-left: 4px solid var(--accent-color);
}
.payment-summary h5 { color: var(--primary-color); font-weight: 600; }
.btn-pay {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    border: none;
    padding: 1rem;
    border-radius: 8px;
    font-weight: 600;
    width: 100%;
    margin-top: 1rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all .3s;
}
.btn-pay:hover {
    background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(67,97,238,.3);
}
.security-note {
    text-align: center;
    margin-top: 1.5rem;
    font-size: .85rem;
    color: #6c757d;
}
.card-icons img { height: 25px; margin-right: 10px; opacity: 0.8; }
.saved-card-option {
    display: flex; align-items: center; padding: 1rem;
    margin-bottom: 1rem; border-radius: 8px;
    background-color: var(--light-color);
    border: 1px solid #e0e0e0;
    transition: all 0.3s ease; cursor: pointer;
}
.saved-card-option:hover {
    border-color: var(--accent-color);
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}
.saved-card-option input[type="radio"] { margin-right: 1rem; accent-color: var(--primary-color); }
.card-type i { font-size: 1.5rem; color: var(--primary-color); margin-right: 0.5rem; }
.card-number { font-family: 'Courier New', monospace; }
.card-cvv-input { max-width: 100px; margin-left: 1rem; }
.new-card-form {
    margin-top: 1.5rem; padding: 1.5rem;
    background-color: var(--light-color);
    border-radius: 10px; border: 1px dashed var(--accent-color);
}
.processing { animation: pulse 1.5s infinite; }
@keyframes pulse {
    0% { transform: scale(1); } 50% { transform: scale(1.05); } 100% { transform: scale(1); }
}
</style>

{{-- ======================= SCRIPTS ======================= --}}
<script>
document.querySelectorAll('input[name="saved_card_id"]').forEach(radio=>{
    radio.addEventListener('change',()=>{
        const isNew = radio.value==='new';
        document.getElementById('newCardForm').classList.toggle('show',isNew);
        document.querySelectorAll('#newCardForm input').forEach(i=>{
            i.disabled=!isNew;
            i.required=isNew;
        });
        document.querySelectorAll('input[name="saved_card_cvv"]').forEach(i=>{
            i.disabled=true;i.required=false;i.value='';
        });
        if(!isNew){
            const cvv=radio.closest('.saved-card-option').querySelector('input[name="saved_card_cvv"]');
            if(cvv){cvv.disabled=false;cvv.required=true;}
        }
    });
});
document.getElementById('card_number')?.addEventListener('input',e=>{
    e.target.value=e.target.value.replace(/\D/g,'').replace(/(\d{4})(?=\d)/g,'$1 ').trim().substring(0,19);
});
document.getElementById('card_expiry')?.addEventListener('input',e=>{
    e.target.value=e.target.value.replace(/\D/g,'').replace(/(\d{2})(?=\d)/,'$1/').substring(0,5);
});
document.querySelector('form')?.addEventListener('submit',()=>{
    const btn=document.querySelector('.btn-pay');
    btn.classList.add('processing');
    btn.innerHTML='<i class="fas fa-spinner fa-spin me-2"></i> Processing...';
    btn.disabled=true;
});
</script>
@endsection
