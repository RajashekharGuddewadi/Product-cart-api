@extends('customer.layouts.app')

@section('content')
<div class="row mb-4">
    <div class="col-md-6">
        <h2><i class="bi bi-grid"></i> Our Products</h2>
    </div>
    <div class="col-md-6">
        <div class="input-group">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" id="searchInput" class="form-control" placeholder="Search products...">
            <button class="btn btn-outline-secondary" type="button" id="searchBtn">
                Search
            </button>
        </div>
    </div>
</div>

<div id="productsContainer" class="row">
    @foreach ($products as $product)
        <div class="col-md-3">
            <div class="card product-card">
                <div class="product-image d-flex align-items-center justify-content-center">
                    <i class="bi bi-box-seam text-muted" style="font-size: 4rem;"></i>
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="text-muted small mb-1">SKU: <code>{{ $product->sku }}</code></p>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="price-tag">₹{{ number_format($product->price, 2) }}</span>
                        <span class="badge {{ $product->stock > 10 ? 'bg-success' : ($product->stock > 0 ? 'bg-warning' : 'bg-danger') }}">
                            {{ $product->stock }} in stock
                        </span>
                    </div>
                    <button class="btn btn-primary btn-add-cart" 
                            onclick="addToCartFromProductList({{ $product->id }})"
                            @if ($product->stock < 1) disabled @endif>
                        <i class="bi bi-cart-plus"></i> Add to Cart
                    </button>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if ($products->hasPages())
    <div class="pagination-container">
        {{ $products->links() }}
    </div>
@endif
@endsection

@section('scripts')
<script>
    // Add to Cart from product list
    async function addToCartFromProductList(productId) {
        try {
            await addToCart(productId, 1);
        } catch (error) {
            // Error handled in addToCart function
        }
    }

    // Search functionality
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');

    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                performSearch(e.target.value);
            }, 500);
        });
    }

    if (searchBtn) {
        searchBtn.addEventListener('click', function() {
            const query = searchInput.value;
            performSearch(query);
        });
    }

    async function performSearch(query) {
        try {
            const response = await axios.get('/api/products', {
                params: { search: query }
            });

            const products = response.data.data;
            renderProducts(products);

        } catch (error) {
            showToast('Failed to search products', 'error');
        }
    }

    function renderProducts(products) {
        const container = document.getElementById('productsContainer');
        
        if (!products || products.length === 0) {
            container.innerHTML = `
                <div class="col-12 text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                    <h4 class="mt-3 text-muted">No products found</h4>
                    <p class="text-muted">Try a different search term</p>
                </div>
            `;
            return;
        }

        container.innerHTML = products.map(product => `
            <div class="col-md-3">
                <div class="card product-card">
                    <div class="product-image d-flex align-items-center justify-content-center">
                        <i class="bi bi-box-seam text-muted" style="font-size: 4rem;"></i>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">${product.name}</h5>
                        <p class="text-muted small mb-1">SKU: <code>${product.sku}</code></p>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="price-tag">₹${parseFloat(product.price).toFixed(2)}</span>
                            <span class="badge ${product.stock > 10 ? 'bg-success' : (product.stock > 0 ? 'bg-warning' : 'bg-danger')}">
                                ${product.stock} in stock
                            </span>
                        </div>
                        <button class="btn btn-primary btn-add-cart" 
                                onclick="addToCartFromProductList(${product.id})"
                                ${product.stock < 1 ? 'disabled' : ''}>
                            <i class="bi bi-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        `).join('');
    }

    // Pagination for search results
    document.addEventListener('DOMContentLoaded', () => {
        // Add click handlers for pagination links
        document.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const href = link.getAttribute('href');
                if (href && href.includes('page=')) {
                    const page = href.match(/page=(\d+)/)[1];
                    const query = searchInput ? searchInput.value : '';
                    performSearchWithPage(query, page);
                }
            });
        });
    });

    async function performSearchWithPage(query, page) {
        try {
            const response = await axios.get('/api/products', {
                params: { search: query, page: page }
            });

            const products = response.data.data;
            renderProducts(products);

            // Update URL without reload
            const url = new URL(window.location.href);
            if (query) {
                url.searchParams.set('search', query);
            } else {
                url.searchParams.delete('search');
            }
            if (page > 1) {
                url.searchParams.set('page', page);
            }
            window.history.pushState({}, '', url);

        } catch (error) {
            showToast('Failed to load page', 'error');
        }
    }
</script>
@endsection
