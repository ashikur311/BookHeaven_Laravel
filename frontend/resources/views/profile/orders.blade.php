@extends('layouts.app')

@section('title', 'My Orders | Book Heaven')

@section('content')
<main>
    <aside>
        <section class="user-info">
            <img src="{{ asset($user->user_profile ?? 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80') }}"
                 alt="{{ $user->username }}" class="user-avatar">
            <div>
                <div class="user-name">{{ $user->username }}</div>
                <small>Member since: {{ date('M Y', strtotime($user->create_time)) }}</small>
            </div>
        </section>

        <section>
            <nav>
                <ul>
                    <li><a href="{{ url('/profile') }}"><i class="fas fa-user"></i> Profile</a></li>
                    <li><a href="{{ url('/wishlist') }}"><i class="fas fa-heart"></i> Wish List</a></li>
                    <li><a href="{{ url('/profile/orders') }}" class="active"><i class="fas fa-shopping-bag"></i> My Orders</a></li>
                    <li><a href="{{ url('/profile/subscriptions') }}"><i class="fas fa-calendar-check"></i> My Subscription</a></li>
                    <li><a href="{{ url('/profile/settings') }}"><i class="fas fa-cog"></i> Setting</a></li>
                    <li><a href="{{ url('/logout') }}"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </section>
    </aside>

    <div class="orders_content">
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Orders</h3>
                <p>{{ $stats['total'] }}</p>
            </div>
            <div class="stat-card">
                <h3>Pending</h3>
                <p>{{ $stats['pending'] }}</p>
            </div>
            <div class="stat-card">
                <h3>Shipped</h3>
                <p>{{ $stats['shipped'] }}</p>
            </div>
            <div class="stat-card">
                <h3>Delivered</h3>
                <p>{{ $stats['delivered'] }}</p>
            </div>
        </div>

        <div class="orders-table">
            <h2>Order History</h2>

            @if($orders->isEmpty())
                <div class="no-orders">
                    <i class="fas fa-shopping-bag"></i>
                    <p>You haven’t placed any orders yet.</p>
                    <a href="{{ url('/') }}" class="btn btn-shop">Start Shopping</a>
                </div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Order Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>#ORD-{{ str_pad($order->order_id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ date('M j, Y', strtotime($order->order_date)) }}</td>
                                <td>৳{{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    <span class="status status-{{ strtolower($order->status) }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-view" onclick="openModal('{{ $order->order_id }}')">
                                        View Details
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</main>

<!-- Order Details Modal -->
<div id="orderModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Order Details - <span id="modalOrderId"></span></h3>
            <button class="modal-close" onclick="closeModal()">&times;</button>
        </div>
        <div class="order-info">
            <div><strong>Order Date:</strong> <span id="orderDate"></span></div><br>
            <div><strong>Status:</strong> <span id="orderStatus"></span></div><br>
            <div><strong>Payment Method:</strong> <span id="paymentMethod"></span></div><br>
            <div><strong>Shipping Address:</strong> <span id="shippingAddress"></span></div><br><br>
        </div>
        <table class="order-items">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody id="orderItems">
                <tr><td colspan="4" class="loading">Select an order to view details</td></tr>
            </tbody>
        </table>
        <div class="order-total">
            <strong>Total: <span id="orderTotal"></span></strong>
        </div>
        <div class="modal-actions">
            <button class="btn btn-pdf" onclick="downloadPDF()">Download PDF</button>
            <button class="btn btn-close" onclick="closeModal()">Close</button>
        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ asset('css/user_order.css') }}">

<script>
function openModal(orderId) {
    document.getElementById('orderItems').innerHTML = '<tr><td colspan="4" class="loading">Loading order details...</td></tr>';
    document.getElementById('modalOrderId').textContent = `#ORD-${orderId.toString().padStart(4, '0')}`;
    document.getElementById('orderModal').style.display = 'flex';

    fetch(`/orders/details/${orderId}`)
        .then(res => res.json())
        .then(data => {
            if (!data.success) throw new Error('Error fetching order');
            const order = data.order;
            const items = data.items;

            document.getElementById('orderDate').textContent = new Date(order.order_date).toLocaleDateString();
            document.getElementById('orderStatus').textContent = order.status;
            document.getElementById('paymentMethod').textContent = order.payment_method.toUpperCase();
            document.getElementById('shippingAddress').textContent = order.shipping_address;
            document.getElementById('orderTotal').textContent = `৳${parseFloat(order.total_amount).toFixed(2)}`;

            const tbody = document.getElementById('orderItems');
            tbody.innerHTML = '';
            if (!items.length) {
                tbody.innerHTML = '<tr><td colspan="4">No items found.</td></tr>';
                return;
            }
            items.forEach(item => {
                tbody.innerHTML += `
                    <tr>
                        <td>${item.title}</td>
                        <td>${item.quantity}</td>
                        <td>৳${parseFloat(item.price).toFixed(2)}</td>
                        <td>৳${(item.price * item.quantity).toFixed(2)}</td>
                    </tr>`;
            });
        })
        .catch(err => {
            document.getElementById('orderItems').innerHTML =
                `<tr><td colspan="4" class="error">Error: ${err.message}</td></tr>`;
        });
}

function closeModal() {
    document.getElementById('orderModal').style.display = 'none';
}

function downloadPDF() {
    alert('PDF generation coming soon!');
}

window.onclick = function(event) {
    const modal = document.getElementById('orderModal');
    if (event.target === modal) closeModal();
}
</script>
@endsection
