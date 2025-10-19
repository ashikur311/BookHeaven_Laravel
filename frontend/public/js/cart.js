       // Theme synchronization with header
        function syncTheme() {
            const darkMode = localStorage.getItem('darkMode');
            if (darkMode === 'enabled') {
                document.body.classList.add('dark-mode');
            } else {
                document.body.classList.remove('dark-mode');
            }
        }

        // Call syncTheme when page loads and when theme changes in header
        document.addEventListener('DOMContentLoaded', syncTheme);

        // Listen for storage events to sync theme when changed in other tabs/windows
        window.addEventListener('storage', syncTheme);

        // Payment method selection
        document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
            radio.addEventListener('change', function () {
                if (this.value === 'cod') {
                    document.getElementById('codDetails').style.display = 'block';
                    document.getElementById('onlineDetails').style.display = 'none';
                    document.getElementById('checkoutBtn').innerHTML = '<i class="fas fa-lock"></i> Confirm Order';
                } else {
                    document.getElementById('codDetails').style.display = 'none';
                    document.getElementById('onlineDetails').style.display = 'block';
                    document.getElementById('checkoutBtn').innerHTML = '<i class="fas fa-lock"></i> Proceed to Payment';
                }
            });
        });

        // Quantity Controls
        document.querySelectorAll('.quantity-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const input = this.parentElement.querySelector('.quantity-input');
                let value = parseInt(input.value);
                const cartItem = this.closest('.cart-item');
                const cartId = cartItem.dataset.cartId;
                const bookId = cartItem.dataset.bookId;

                if (this.classList.contains('minus') && value > 1) {
                    value = value - 1;
                    input.value = value;
                    updateCartItem(cartId, bookId, value);
                } else if (this.classList.contains('plus')) {
                    value = value + 1;
                    input.value = value;
                    updateCartItem(cartId, bookId, value);
                }
            });
        });

        // Update cart item quantity via AJAX
        function updateCartItem(cartId, bookId, quantity) {
            $.ajax({
                url: window.location.href,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'update',
                    cart_id: cartId,
                    book_id: bookId,
                    quantity: quantity
                },
                success: function (data) {
                    if (data.success) {
                        // Update summary totals
                        updateSummaryTotals(data.totals);
                    } else {
                        alert('Error updating cart: ' + data.message);
                    }
                },
                error: function () {
                    alert('Error communicating with server');
                }
            });
        }

        // Update summary totals
        function updateSummaryTotals(totals) {
            document.getElementById('summarySubtotal').textContent = `৳${totals.subtotal.toFixed(2)}`;
            document.getElementById('summaryDelivery').textContent = `৳${totals.delivery.toFixed(2)}`;
            document.getElementById('summaryTotal').textContent = `৳${totals.total.toFixed(2)}`;

            // Update item count in subtotal label
            const itemText = totals.item_count === 1 ? 'item' : 'items';
            document.querySelector('.summary-row:nth-child(1) span:first-child').textContent =
                `Subtotal (${totals.item_count} ${itemText})`;
        }

        // Remove Item
        document.querySelectorAll('.remove-item').forEach(item => {
            item.addEventListener('click', function () {
                const cartId = this.dataset.cartId;

                if (confirm('Are you sure you want to remove this item from your cart?')) {
                    $.ajax({
                        url: window.location.href,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action: 'remove',
                            cart_id: cartId
                        },
                        success: function (data) {
                            if (data.success) {
                                // Redirect to cart.php after successful removal
                                window.location.href = 'cart.php';
                            } else {
                                alert('Error removing item: ' + data.message);
                            }
                        },
                        error: function () {
                            alert('Error communicating with server');
                        }
                    });
                }
            });
        });

        // Move to Wishlist
        document.querySelectorAll('.wishlist-item').forEach(item => {
            item.addEventListener('click', function () {
                const cartId = this.dataset.cartId;
                const bookId = this.dataset.bookId;

                if (confirm('Move this item to your wishlist?')) {
                    $.ajax({
                        url: window.location.href,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action: 'move_to_wishlist',
                            cart_id: cartId,
                            book_id: bookId
                        },
                        success: function (data) {
                            if (data.success) {
                                // Remove item from DOM
                                document.querySelector(`.cart-item[data-cart-id="${cartId}"]`).remove();

                                // Update summary totals
                                updateSummaryTotals(data.totals);

                                // Show success message
                                alert(data.message);

                                // If no items left, show empty cart message
                                if (document.querySelectorAll('.cart-item').length === 0) {
                                    document.getElementById('cartItemsContainer').innerHTML = `
                                        <div class="empty-cart">
                                            <i class="fas fa-shopping-cart"></i>
                                            <h2>Your Cart is Empty</h2>
                                            <p>Looks like you haven't added any items to your cart yet.</p>
                                            <button class="btn btn-primary" style="margin-top: 20px;" onclick="window.location.href='/BookHeaven2.0/index.php'">
                                                <i class="fas fa-book"></i> Browse Books
                                            </button>
                                        </div>
                                    `;
                                    document.querySelector('.cart-summary').style.display = 'none';
                                }
                            } else {
                                alert('Error: ' + data.message);
                            }
                        },
                        error: function () {
                            alert('Error communicating with server');
                        }
                    });
                }
            });
        });

        // Clear Cart
        document.getElementById('clearCartBtn')?.addEventListener('click', function () {
            if (confirm('Are you sure you want to clear your cart?')) {
                $.ajax({
                    url: window.location.href,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'clear'
                    },
                    success: function (data) {
                        if (data.success) {
                            // Clear cart items
                            document.getElementById('cartItemsContainer').innerHTML = `
                                <div class="empty-cart">
                                    <i class="fas fa-shopping-cart"></i>
                                    <h2>Your Cart is Empty</h2>
                                    <p>Looks like you haven't added any items to your cart yet.</p>
                                    <button class="btn btn-primary" style="margin-top: 20px;" onclick="window.location.href='/BookHeaven2.0/index.php'">
                                        <i class="fas fa-book"></i> Browse Books
                                    </button>
                                </div>
                            `;
                            document.querySelector('.cart-summary').style.display = 'none';
                        } else {
                            alert('Error clearing cart: ' + data.message);
                        }
                    },
                    error: function () {
                        alert('Error communicating with server');
                    }
                });
            }
        });

        // Checkout Button - Show Confirmation Modal
        document.getElementById('checkoutBtn')?.addEventListener('click', function () {
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            const shippingAddress = document.getElementById('shippingAddress').value;

            if (!shippingAddress.trim()) {
                alert('Please enter your shipping address');
                return;
            }

            // Get all cart items
            const cartItems = document.querySelectorAll('.cart-item');

            // Clear previous items in modal
            document.getElementById('orderItemsList').innerHTML = '';

            // Add each item to the modal
            cartItems.forEach(item => {
                const title = item.querySelector('.cart-item-title').textContent;
                const price = item.querySelector('.cart-item-price').textContent;
                const quantity = item.querySelector('.quantity-input').value;

                const itemElement = document.createElement('div');
                itemElement.className = 'order-item';
                itemElement.innerHTML = `
                    <span class="order-item-name">${title}</span>
                    <span class="order-item-qty">${quantity}x</span>
                    <span class="order-item-price">${price}</span>
                `;
                document.getElementById('orderItemsList').appendChild(itemElement);
            });

            // Update totals in modal
            document.getElementById('modalSubtotal').textContent = document.getElementById('summarySubtotal').textContent;
            document.getElementById('modalDelivery').textContent = document.getElementById('summaryDelivery').textContent;
            document.getElementById('modalTotal').textContent = document.getElementById('summaryTotal').textContent;

            // Update payment method in modal
            const paymentText = paymentMethod === 'cod' ? 'Cash on Delivery' : 'Online Payment';
            document.getElementById('modalPaymentMethod').textContent = paymentText;

            // Update address in modal
            document.getElementById('modalShippingAddress').textContent = shippingAddress;

            // Show/hide online payment section based on payment method
            const onlinePaymentSection = document.getElementById('onlinePaymentSection');
            if (paymentMethod === 'online') {
                onlinePaymentSection.style.display = 'block';
                document.getElementById('confirmOrderBtn').innerHTML = '<i class="fas fa-check-circle"></i> Confirm Order';
            } else {
                onlinePaymentSection.style.display = 'none';
                document.getElementById('confirmOrderBtn').innerHTML = '<i class="fas fa-check-circle"></i> Confirm Order';
            }

            // Show the modal
            document.getElementById('confirmationModal').style.display = 'flex';
        });

        // Payment option selection (for online payment)
        document.querySelectorAll('.payment-option').forEach(option => {
            option.addEventListener('click', function () {
                // Remove active class from all options
                document.querySelectorAll('.payment-option').forEach(opt => {
                    opt.classList.remove('active');
                });

                // Add active class to selected option
                this.classList.add('active');

                // Store the selected payment method
                this.dataset.selected = 'true';
            });
        });

        // Close Modal
        document.querySelector('.close-modal').addEventListener('click', function () {
            document.getElementById('confirmationModal').style.display = 'none';
        });

        document.querySelector('.modal-btn-cancel').addEventListener('click', function () {
            document.getElementById('confirmationModal').style.display = 'none';
        });

        // Click outside modal to close
        window.addEventListener('click', function (event) {
            if (event.target === document.getElementById('confirmationModal')) {
                document.getElementById('confirmationModal').style.display = 'none';
            }
        });

        // Confirm Order Button in Modal
        document.getElementById('confirmOrderBtn').addEventListener('click', function () {
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            const shippingAddress = document.getElementById('shippingAddress').value;

            // For online payment, check if a payment option is selected
            let paymentOption = null;
            if (paymentMethod === 'online') {
                const selectedOption = document.querySelector('.payment-option[data-selected="true"]');
                if (!selectedOption) {
                    alert('Please select a payment method');
                    return;
                }
                paymentOption = selectedOption.dataset.method;
            }

            // Hide the modal
            document.getElementById('confirmationModal').style.display = 'none';

            // Proceed with the order placement
            $.ajax({
                url: window.location.href,
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'place_order',
                    payment_method: paymentMethod,
                    shipping_address: shippingAddress
                },
                success: function (data) {
                    if (data.success) {
                        // Clear cart display
                        document.getElementById('cartItemsContainer').innerHTML = `
                            <div class="empty-cart">
                                <i class="fas fa-shopping-cart"></i>
                                <h2>Order Placed Successfully!</h2>
                                <p>Your order ID is: ${data.order_id}</p>
                                <button class="btn btn-primary" style="margin-top: 20px;" onclick="window.location.href='/BookHeaven2.0/index.php'">
                                    <i class="fas fa-book"></i> Continue Shopping
                                </button>
                            </div>
                        `;
                        document.querySelector('.cart-summary').style.display = 'none';

                        // For online payment, redirect to payment processor
                        if (paymentMethod === 'online') {
                            if (paymentOption === 'bkash') {
                                window.location.href = '/BookHeaven2.0/php/process_bkash_payment.php?type=book_order&id=' + data.order_id;
                            } else if (paymentOption === 'card') {
                                window.location.href = '/BookHeaven2.0/php/process_card_payment.php?type=book_order&id=' + data.order_id;
                            }
                        } else {
                            // For COD, just show success message
                            alert('Order placed successfully! Order ID: ' + data.order_id);
                        }
                    } else {
                        alert('Error placing order: ' + data.message);
                    }
                },
                error: function () {
                    alert('Error communicating with server');
                }
            });
        });
        // Payment option selection (for online payment)
        document.querySelectorAll('.payment-option').forEach(option => {
            option.addEventListener('click', function () {
                // Remove active class from all options
                document.querySelectorAll('.payment-option').forEach(opt => {
                    opt.classList.remove('active');
                });

                // Hide all payment details
                document.querySelectorAll('.payment-details').forEach(detail => {
                    detail.classList.remove('active');
                });

                // Add active class to selected option
                this.classList.add('active');

                // Show corresponding payment details
                const method = this.dataset.method;
                document.getElementById(method + 'Details').classList.add('active');

                // Store the selected payment method
                this.dataset.selected = 'true';
            });
        });