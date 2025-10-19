@extends('layouts.app')

@section('title', 'Your Shopping Cart | Book Heaven')

@section('content')
<link rel="stylesheet" href="{{ asset('css/cart.css') }}">

<style>
/* modal look like your original */
.modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:1000;justify-content:center;align-items:center}
.modal.open{display:flex}
.modal-content{background:#fff;border-radius:12px;padding:25px;width:95%;max-width:550px;box-shadow:0 10px 25px rgba(0,0,0,.2)}
.modal-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px}
.modal-btn{border:none;border-radius:6px;padding:10px 18px;cursor:pointer;font-weight:600}
.modal-btn-cancel{background:#e74c3c;color:#fff}
.modal-btn-confirm{background:#3498db;color:#fff}
.payment-option{border:1px solid #ddd;border-radius:8px;padding:10px;margin:6px 0;display:flex;gap:10px;align-items:center;cursor:pointer}
.payment-option.active{border:2px solid #3498db;background:#f1f8ff}
.order-item-row{display:flex;justify-content:space-between;padding:4px 0;border-bottom:1px dotted #ddd}
</style>

<div class="container">
  <div class="cart-header">
    <h1><i class="fas fa-shopping-cart"></i> Your Shopping Cart</h1>
    <div class="cart-actions">
      <a href="{{ route('home') }}" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Continue Shopping</a>
      @if($totals['item_count'] > 0)
        <button class="btn btn-danger" id="clearCartBtn"><i class="fas fa-trash"></i> Clear Cart</button>
      @endif
    </div>
  </div>

  <div class="cart-layout">

    {{-- Items --}}
    <div class="cart-items" id="cartItemsContainer">
      @forelse($cartItems as $item)
      <div class="cart-item"
           data-cart-id="{{ $item->id }}"
           data-book-id="{{ $item->book_id }}"
           data-price="{{ $item->price }}">
        <img src="{{ asset($item->cover_image_url) }}" alt="{{ $item->title }}" class="cart-item-image">
        <div class="cart-item-details">
          <h3 class="cart-item-title">{{ $item->title }}</h3>
          <p class="cart-item-author">By {{ $item->writers ?? 'Unknown' }}</p>
          <div class="cart-item-price">৳<span class="price-ea">{{ number_format($item->price, 2) }}</span></div>

          <div class="cart-item-actions">
            <div class="quantity-control">
              <button class="quantity-btn minus"><i class="fas fa-minus"></i></button>
              <input type="number" value="{{ $item->quantity }}" min="1" class="quantity-input">
              <button class="quantity-btn plus"><i class="fas fa-plus"></i></button>
            </div>
            <button class="wishlist-item" data-cart-id="{{ $item->id }}" data-book-id="{{ $item->book_id }}">
              <i class="fas fa-heart"></i> Move to Wishlist
            </button>
            <button class="remove-item" data-cart-id="{{ $item->id }}">
              <i class="fas fa-trash"></i> Remove
            </button>
          </div>
        </div>
      </div>
      @empty
      <div class="empty-cart">
        <i class="fas fa-shopping-cart"></i>
        <h2>Your Cart is Empty</h2>
      </div>
      @endforelse
    </div>

    {{-- Summary --}}
    @if($totals['item_count'] > 0)
    <div class="cart-summary">
      <h2>Order Summary</h2>
      <div><span>Subtotal (<span id="summaryCount">{{ $totals['item_count'] }}</span> items):</span>
           <span id="summarySubtotal">৳{{ number_format($totals['subtotal'],2) }}</span></div>
      <div><span>Delivery:</span> <span id="summaryDelivery">৳{{ number_format($totals['delivery'],2) }}</span></div>
      <div><strong>Total:</strong> <strong id="summaryTotal">৳{{ number_format($totals['total'],2) }}</strong></div>

      <div class="payment-method">
        <h3>Payment Method</h3>
        <label><input type="radio" name="payment_method" value="cod" checked> Cash on Delivery</label>
        <label><input type="radio" name="payment_method" value="online"> Online Payment</label>
      </div>

      <div class="address-input">
        <h3>Shipping Address</h3>
        <textarea id="shippingAddress" rows="3">{{ $userAddress }}</textarea>
      </div>

      <button class="checkout-btn" id="checkoutBtn"><i class="fas fa-lock"></i> Proceed to Payment</button>
    </div>
    @endif
  </div>
</div>

{{-- Modal --}}
<div class="modal" id="confirmationModal">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Confirm Your Order</h3>
      <button class="close-modal">&times;</button>
    </div>
    <div class="modal-body">
      <div class="order-items" id="orderItemsList"></div>
      <div class="order-totals" style="margin-top:8px;">
        <div><strong>Subtotal:</strong> <span id="modalSubtotal">৳0.00</span></div>
        <div><strong>Delivery:</strong> <span id="modalDelivery">৳0.00</span></div>
        <div><strong>Total:</strong> <span id="modalTotal">৳0.00</span></div>
      </div>
      <hr>
      <div><strong>Payment Method:</strong> <span id="modalPaymentMethod"></span></div>
      <div><strong>Address:</strong> <p id="modalShippingAddress"></p></div>

      <div id="onlinePaymentSection" style="display:none; margin-top:8px;">
        <h4>Select Online Method</h4>
        <div class="payment-options">
          <div class="payment-option" data-method="bkash"><i class="fas fa-mobile-alt" style="color:#e2136e;"></i> bKash</div>
          <div class="payment-option" data-method="card"><i class="far fa-credit-card" style="color:#0061a8;"></i> Card</div>
        </div>
        <div class="payment-details" id="bkashDetails" style="display:none;">You'll be redirected to bKash payment page</div>
        <div class="payment-details" id="cardDetails" style="display:none;">You'll be redirected to secure card payment</div>
      </div>
    </div>
    <div class="modal-actions" style="text-align:right;margin-top:10px;">
      <button class="modal-btn modal-btn-cancel">Cancel</button>
      <button class="modal-btn modal-btn-confirm" id="confirmOrderBtn">Confirm Order</button>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function(){
  const csrf = '{{ csrf_token() }}';
  const post = (action, data={}) => $.post('{{ route('cart.action') }}', { _token: csrf, action, ...data });

  /* ---------- helpers ---------- */
  const money = n => '৳' + Number(n).toFixed(2);
  function applyTotals(t){
    $('#summaryCount').text(t.item_count);
    $('#summarySubtotal').text(money(t.subtotal));
    $('#summaryDelivery').text(money(t.delivery));
    $('#summaryTotal').text(money(t.total));
    $('#modalSubtotal').text(money(t.subtotal));
    $('#modalDelivery').text(money(t.delivery));
    $('#modalTotal').text(money(t.total));
  }
  function getQty($row){ return Math.max(1, parseInt($row.find('.quantity-input').val()) || 1); }
  function setQty($row,q){ $row.find('.quantity-input').val(q); }

  /* ---------- qty +/- live update ---------- */
  $('.plus').on('click', function(){
    const $row = $(this).closest('.cart-item');
    const id   = $row.data('cart-id');
    const q    = getQty($row) + 1;
    setQty($row, q);
    post('update', { cart_id: id, quantity: q }).done(r => r.success && applyTotals(r.totals));
  });

  $('.minus').on('click', function(){
    const $row = $(this).closest('.cart-item');
    const id   = $row.data('cart-id');
    const q    = Math.max(1, getQty($row) - 1);
    setQty($row, q);
    post('update', { cart_id: id, quantity: q }).done(r => r.success && applyTotals(r.totals));
  });

  $('.quantity-input').on('change', function(){
    const $row = $(this).closest('.cart-item');
    const id   = $row.data('cart-id');
    const q    = getQty($row);
    setQty($row, q);
    post('update', { cart_id: id, quantity: q }).done(r => r.success && applyTotals(r.totals));
  });

  /* ---------- remove / clear / wishlist ---------- */
  $('.remove-item').click(function(){
    const $row = $(this).closest('.cart-item');
    post('remove', { cart_id: $(this).data('cart-id') }).done(r=>{
      if(r.success){ $row.remove(); applyTotals(r.totals); }
    });
  });

  $('#clearCartBtn').click(function(){
    if(!confirm('Clear all items?')) return;
    post('clear').done(()=>location.reload());
  });

  $('.wishlist-item').click(function(){
    const $row = $(this).closest('.cart-item');
    post('move_to_wishlist', { cart_id: $(this).data('cart-id'), book_id: $(this).data('book-id') })
      .done(r=>{ if(r.success){ $row.remove(); applyTotals(r.totals); alert('Moved to wishlist'); }});
  });

  /* ---------- proceed to payment (modal) ---------- */
  $('#checkoutBtn').click(function(){
    // build order items summary (like original)
    let rows='';
    $('.cart-item').each(function(){
      const t = $(this).find('.cart-item-title').text().trim();
      const q = getQty($(this));
      const p = parseFloat($(this).data('price'));
      rows += `<div class="order-item-row"><span>${t}</span><span>x${q}</span><span>${money(p*q)}</span></div>`;
    });
    $('#orderItemsList').html(rows || '<em>No items</em>');

    // set labels
    const pm = $('input[name="payment_method"]:checked').val();
    $('#modalPaymentMethod').text(pm==='online' ? 'Online Payment' : 'Cash on Delivery');
    $('#modalShippingAddress').text($('#shippingAddress').val().trim() || 'No address provided');

    // mirror current totals
    $('#modalSubtotal').text($('#summarySubtotal').text());
    $('#modalDelivery').text($('#summaryDelivery').text());
    $('#modalTotal').text($('#summaryTotal').text());

    // show/hide online options
    if(pm==='online'){ $('#onlinePaymentSection').show(); } else { $('#onlinePaymentSection').hide(); }
    $('#bkashDetails,#cardDetails').hide();
    $('.payment-option').removeClass('active'); // reset
    $('#confirmationModal').addClass('open');
  });

  $('.close-modal, .modal-btn-cancel').click(()=>$('#confirmationModal').removeClass('open'));

  $('.payment-option').click(function(){
    $('.payment-option').removeClass('active');
    $(this).addClass('active');
    const m = $(this).data('method');
    $('#bkashDetails,#cardDetails').hide();
    if(m==='bkash') $('#bkashDetails').show();
    if(m==='card')  $('#cardDetails').show();
  });

  /* ---------- confirm order -> place_order + redirect ---------- */
  $('#confirmOrderBtn').click(function(){
    const pm = $('input[name="payment_method"]:checked').val();
    const addr = $('#shippingAddress').val().trim();
    const onlineMethod = $('.payment-option.active').data('method');

    post('place_order', { payment_method: pm, shipping_address: addr })
      .done(r=>{
        if(!r.success){ alert(r.message || 'Failed'); return; }

        // redirect logic (unchanged from your original)
        if(pm === 'online'){
          const method = onlineMethod || 'bkash'; // default if not clicked
          const url = (method === 'bkash')
            ? "{{ url('/payment/bkash') }}" + "?type=book_order&id=" + r.order_id
            : "{{ url('/payment/card') }}"  + "?type=book_order&id=" + r.order_id;
          window.location.href = url;
        } else {
          window.location.href = "{{ route('orders.index') }}";
        }
      })
      .fail(()=>alert('Server error'));
  });
});
</script>
@endsection
