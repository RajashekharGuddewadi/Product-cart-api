<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - Shop</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        .navbar-brand { font-weight: 700; }
        .product-card { 
            transition: transform 0.2s, box-shadow 0.2s;
            margin-bottom: 20px;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        .product-image {
            height: 200px;
            object-fit: cover;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        .price-tag {
            font-size: 1.25rem;
            font-weight: 700;
            color: #dc3545;
        }
        .toast-container { 
            position: fixed; 
            top: 20px; 
            right: 20px; 
            z-index: 9999; 
        }
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.7rem;
            min-width: 20px;
            text-align: center;
        }
        .btn-add-cart { width: 100%; }
        .badge-status { padding: 5px 10px; }
        .error-message { color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem; }
        .cart-item { border-bottom: 1px solid #dee2e6; padding: 15px 0; }
        .cart-item:last-child { border-bottom: none; }
        .qty-btn { width: 35px; height: 35px; padding: 0; }
        .pagination { justify-content: center; margin-top: 30px; }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('customer.products') }}">
                <i class="bi bi-shop"></i> ShopHub
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() == 'customer.products' ? 'active' : '' }}" 
                           href="{{ route('customer.products') }}">
                            <i class="bi bi-grid"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ Route::currentRouteName() == 'customer.cart' ? 'active' : '' }}" 
                           href="{{ route('customer.cart') }}" style="position: relative;">
                            <i class="bi bi-cart"></i> Cart
                            <span class="cart-badge" id="cartBadge" style="display: none;">0</span>
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item">
                            <span class="nav-link">
                                <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                            </span>
                        </li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-light btn-sm">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Login</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>
    <!-- Main Content -->
    <div class="container mt-4">
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <strong><i class="bi bi-exclamation-triangle-fill"></i> Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Footer -->
    <footer class="bg-dark text-white mt-5 py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; {{ date('Y') }} ShopHub. All rights reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <!-- Common Customer JavaScript -->
    <script>
        // Toast Notification
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            if (!container) return;
            
            const toast = document.createElement('div');
            const bgClass = type === 'success' ? 'bg-success' : (type === 'warning' ? 'bg-warning' : 'bg-danger');
            const icon = type === 'success' ? 'check-circle' : (type === 'warning' ? 'exclamation-triangle' : 'exclamation-triangle');
            
            toast.className = `toast align-items-center text-white ${bgClass} border-0`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-${icon}"></i> ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            
            container.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast, { delay: 4000 });
            bsToast.show();
            
            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        }

        // Update Cart Badge
        async function updateCartBadge() {
            try {
                const response = await axios.get('/api/cart');
                const data = response.data.data;
                const badge = document.getElementById('cartBadge');
                
                if (badge) {
                    const count = data?.item_count || 0;
                    badge.textContent = count;
                    badge.style.display = count > 0 ? 'block' : 'none';
                }
            } catch (error) {
                // Silent fail for badge update
            }
        }

        // Add to Cart
        async function addToCart(productId, quantity = 1) {
            try {
                const response = await axios.post('/api/cart/items', {
                    product_id: productId,
                    quantity: quantity
                });
                
                showToast(response.data.message, 'success');
                updateCartBadge();
                
                return response.data;
            } catch (error) {
                const message = error.response?.data?.message || 'Failed to add to cart';
                showToast(message, 'error');
                throw error;
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            updateCartBadge();
        });
    </script>
    
    @yield('scripts')
</body>
</html>

