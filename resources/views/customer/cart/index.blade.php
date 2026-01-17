@extends('customer.layouts.app')

@section('content')
<div class="row">
    <div class="col-md-8">
        <h2><i class="bi bi-cart"></i> Your Shopping Cart</h2>
        
        <div id="cartItemsContainer" class="card mt-3">
            <div class="card-body">
                <div id="cartItems">
                    <!-- Cart items will be loaded here -->
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-receipt-cutoff"></i> Order Summary</h5>
            </div>
            <div class="card-body">
                <div id="orderSummary">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <strong id="subtotal">₹0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Shipping:</span>
                        <span class="text-muted">Free</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong id="total" class="text-primary" style="font-size: 1.5rem;">₹0.00</strong>
                    </div>
                    <button id="checkoutBtn" class="btn btn-success btn-lg w-100" onclick="checkout()">
                        <i class="bi bi-credit-card"></i> Checkout
                    </button>
                    <div class="text-center mt-3">
                        <a href="{{ route('customer.products') }}" class="text-muted">
                            <i class="bi bi-arrow-left"></i> Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Load cart on page load
    document.addEventListener('DOMContentLoaded', () => {
        loadCart();
    });

    async function loadCart() {
        try {
            const response = await axios.get('/api/cart');
            const data = response.data.data;
            
            renderCart(data);
            updateCartBadge();
            
        } catch (error) {
            showToast('Failed to load cart', 'error');
        }
    }

    function renderCart(data) {
        const itemsContainer = document.getElementById('cartItems');
        const subtotalEl = document.getElementById('subtotal');
        const totalEl = document.getElementById('total');
        const checkoutBtn = document.getElementById('checkoutBtn');

        const items = data.items || [];
        const total = data.total || 0;

        // Update totals
        subtotalEl.textContent = `₹${total.toFixed(2)}`;
        totalEl.textContent = `₹${total.toFixed(2)}`;

        // Enable/disable checkout
        checkoutBtn.disabled = items.length === 0;

        if (items.length === 0) {
            itemsContainer.innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-cart-x text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-muted">Your cart is empty</h4>
                    <p class="text-muted">Add some products to get started</p>
                    <a href="{{ route('customer.products') }}" class="btn btn-primary mt-2">
                        <i class="bi bi-shop"></i> Browse Products
                    </a>
                </div>
            `;
            return;
        }

        itemsContainer.innerHTML = items.map(item => `
            <div class="cart-item" data-product-id="${item.product_id}">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <strong>${item.product_name}</strong>
                        <div class="text-muted small">SKU: <code>${item.sku}</code></div>
                        <div class="text-muted small">Price: ₹${item.price_at_time.toFixed(2)}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="input-group" style="max-width: 150px;">
                            <button class="btn btn-outline-secondary qty-btn" 
                                    onclick="updateQuantity(${item.product_id}, ${item.quantity - 1})" 
                                    ${item.quantity <= 1 ? 'disabled' : ''}>
                                <i class="bi bi-dash"></i>
                            </button>
                            <input type="number" class="form-control text-center" 
                                   value="${item.quantity}" 
                                   id="qty_${item.product_id}"
                                   onchange="updateQuantity(${item.product_id}, this.value)">
                            <button class="btn btn-outline-secondary qty-btn" 
                                    onclick="updateQuantity(${item.product_id}, ${item.quantity + 1})">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3 text-end">
                        <strong>₹${item.subtotal.toFixed(2)}</strong>
                    </div>
                    <div class="col-md-1 text-end">
                        <button class="btn btn-outline-danger btn-sm" 
                                onclick="deleteCartItem(${item.product_id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
    }

    // Update quantity
    async function updateQuantity(productId, quantity) {
        quantity = parseInt(quantity);
        
        if (quantity < 1) {
            if (confirm('Remove this item from cart?')) {
                return deleteCartItem(productId);
            }
            return;
        }

        try {
            const response = await axios.patch(`/api/cart/items/${productId}`, { quantity });
            const data = response.data.data;
            
            renderCart(data);
            showToast(response.data.message, 'success');
            
        } catch (error) {
            const message = error.response?.data?.message || 'Failed to update quantity';
            showToast(message, 'error');
            
            // Reload cart to get correct state
            loadCart();
        }
    }

    // Delete cart item
    async function deleteCartItem(productId) {
        if (!confirm('Are you sure you want to remove this item?')) {
            return;
        }

        try {
            const response = await axios.delete(`/api/cart/items/${productId}`);
            const data = response.data.data;
            
            renderCart(data);
            showToast(response.data.message, 'success');
            updateCartBadge();
            
        } catch (error) {
            showToast('Failed to remove item', 'error');
        }
    }

    // Checkout
    async function checkout() {
        try {
            const response = await axios.post('/api/cart/checkout');
            
            showToast(response.data.message, 'success');
            
            // Clear cart display
            document.getElementById('cartItems').innerHTML = `
                <div class="text-center py-5">
                    <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-success">Order Placed Successfully!</h4>
                    <p class="text-muted">Thank you for shopping with us.</p>
                    <a href="{{ route('customer.products') }}" class="btn btn-primary mt-2">
                        <i class="bi bi-shop"></i> Continue Shopping
                    </a>
                </div>
            `;
            
            document.getElementById('subtotal').textContent = '₹0.00';
            document.getElementById('total').textContent = '₹0.00';
            document.getElementById('checkoutBtn').disabled = true;
            
            updateCartBadge();
            
        } catch (error) {
            const message = error.response?.data?.message || 'Checkout failed';
            showToast(message, 'error');
        }
    }
</script>
@endsection
