<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .card { margin-bottom: 20px; }
        .toast-container { position: fixed; top: 20px; right: 20px; z-index: 9999; }
        .badge-status { padding: 6px 12px; }
        .btn-action { margin-right: 5px; }
        .error-message { color: #dc3545; font-size: 0.875rem; margin-top: 0.25rem; }
        .pagination { justify-content: center; margin-top: 20px; }
        .table-responsive { overflow-x: auto; }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-shop"></i> Admin Panel
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">
                            <i class="bi bi-box-seam"></i> Products
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="nav-link">
                            <i class="bi bi-person-circle"></i> {{ Auth::user()->name }}
                        </span>
                    </li>
                    <li class="nav-item">
                        <form method="POST" action="{{ route('admin.logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <h2 class="mb-4"><i class="bi bi-box-seam"></i> Product Management</h2>

        <!-- Add / Update Form -->
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Add / Update Product</h5>
            </div>
            <div class="card-body">
                <form id="productForm" class="needs-validation" novalidate>
                    <input type="hidden" id="product_id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Product Name *</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                            <div class="error-message" id="error-name"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="sku" class="form-label">SKU *</label>
                            <input type="text" id="sku" name="sku" class="form-control" required>
                            <div class="error-message" id="error-sku"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="price" class="form-label">Price (₹) *</label>
                            <input type="number" id="price" name="price" class="form-control" step="0.01" min="1" required>
                            <div class="error-message" id="error-price"></div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="stock" class="form-label">Stock *</label>
                            <input type="number" id="stock" name="stock" class="form-control" min="0" required>
                            <div class="error-message" id="error-stock"></div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="form-check mt-4 pt-3">
                                <input type="checkbox" id="is_active" name="is_active" class="form-check-input" checked>
                                <label class="form-check-label" for="is_active">
                                    <strong>Active Status</strong>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Product
                        </button>
                        <button type="button" class="btn btn-secondary" id="resetBtn">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Search & Product List -->
        <div class="card">
            <div class="card-header bg-secondary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-list"></i> Product List</h5>
                    <div class="input-group" style="max-width: 300px;">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="searchInput" class="form-control" placeholder="Search by name or SKU...">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>SKU</th>
                                <th>Price (₹)</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="productTableBody">
                            <!-- AJAX will populate -->
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div id="pagination" class="pagination"></div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <script>
        // Toast Notification
        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            const bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
            const icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';
            
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

        // Clear form errors
        function clearErrors() {
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        }

        // Show form error
        function showError(field, message) {
            const errorEl = document.getElementById(`error-${field}`);
            const inputEl = document.getElementById(field);
            
            if (errorEl) errorEl.textContent = message;
            if (inputEl) inputEl.classList.add('is-invalid');
        }

        // Load products
        async function loadProducts(page = 1, search = '') {
            try {
                const response = await axios.get('/api/admin/products', {
                    params: { page, search }
                });

                const products = response.data.data.data || response.data.data;
                renderProducts(products);
                renderPagination(response.data.data);
            } catch (error) {
                showToast(error.response?.data?.message || 'Failed to load products', 'error');
            }
        }

        // Render products table
        function renderProducts(products) {
            const tbody = document.getElementById('productTableBody');
            
            if (!products || products.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox"></i> No products found
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = products.map(product => `
                <tr>
                    <td>${product.id}</td>
                    <td><strong>${product.name}</strong></td>
                    <td><code>${product.sku}</code></td>
                    <td>₹${parseFloat(product.price).toFixed(2)}</td>
                    <td><span class="badge ${product.stock > 10 ? 'bg-success' : (product.stock > 0 ? 'bg-warning' : 'bg-danger')}">${product.stock}</span></td>
                    <td>
                        <button class="btn btn-sm btn-action ${product.is_active ? 'btn-success' : 'btn-danger'}" 
                                onclick="toggleStatus(${product.id}, ${product.is_active})">
                            ${product.is_active ? '<i class="bi bi-check-circle"></i> Active' : '<i class="bi bi-x-circle"></i> Inactive'}
                        </button>
                    </td>
                    <td>${new Date(product.created_at).toLocaleDateString()}</td>
                    <td>
                        <button class="btn btn-sm btn-primary btn-action" onclick="editProduct(${product.id})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-action" onclick="deleteProduct(${product.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Render pagination
        function renderPagination(data) {
            const pagination = document.getElementById('pagination');
            const links = data.links || [];
            
            if (!links || links.length === 0) {
                pagination.innerHTML = '';
                return;
            }

            const currentPage = data.current_page || data.meta?.current_page || 1;
            const lastPage = data.last_page || data.meta?.last_page || 1;

            let html = '<nav><ul class="pagination">';

            // Previous
            html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="goToPage(${currentPage - 1})">Previous</a>
            </li>`;

            // Page numbers
            for (let i = 1; i <= lastPage; i++) {
                if (i === 1 || i === lastPage || (i >= currentPage - 1 && i <= currentPage + 1)) {
                    html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="goToPage(${i})">${i}</a>
                    </li>`;
                } else if (i === currentPage - 2 || i === currentPage + 2) {
                    html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
                }
            }

            // Next
            html += `<li class="page-item ${currentPage === lastPage ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="goToPage(${currentPage + 1})">Next</a>
            </li>`;

            html += '</ul></nav>';
            pagination.innerHTML = html;
        }

        function goToPage(page) {
            if (page < 1) return;
            const search = document.getElementById('searchInput').value;
            loadProducts(page, search);
        }

        // Edit product
        async function editProduct(id) {
            try {
                const response = await axios.get(`/api/admin/products/${id}`);
                const product = response.data.data;
                
                document.getElementById('product_id').value = product.id;
                document.getElementById('name').value = product.name;
                document.getElementById('sku').value = product.sku;
                document.getElementById('price').value = product.price;
                document.getElementById('stock').value = product.stock;
                document.getElementById('is_active').checked = product.is_active;
                
                clearErrors();
                showToast('Product loaded for editing', 'success');
            } catch (error) {
                showToast(error.response?.data?.message || 'Failed to load product', 'error');
            }
        }

        // Toggle status
        async function toggleStatus(id, currentStatus) {
            if (!confirm(`Are you sure you want to ${currentStatus ? 'deactivate' : 'activate'} this product?`)) {
                return;
            }

            try {
                const response = await axios.post(`/api/admin/products/${id}/toggle`);
                showToast(response.data.message, 'success');
                const search = document.getElementById('searchInput').value;
                loadProducts(1, search);
            } catch (error) {
                showToast(error.response?.data?.message || 'Failed to update status', 'error');
            }
        }

        // Delete product
        async function deleteProduct(id) {
            if (!confirm('Are you sure you want to delete this product? This action cannot be undone.')) {
                return;
            }

            try {
                const response = await axios.delete(`/api/admin/products/${id}`);
                showToast(response.data.message, 'success');
                const search = document.getElementById('searchInput').value;
                loadProducts(1, search);
            } catch (error) {
                showToast(error.response?.data?.message || 'Failed to delete product', 'error');
            }
        }

        // Form submission
        document.getElementById('productForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            clearErrors();

            const productId = document.getElementById('product_id').value;
            const formData = {
                name: document.getElementById('name').value,
                sku: document.getElementById('sku').value,
                price: document.getElementById('price').value,
                stock: document.getElementById('stock').value,
                is_active: document.getElementById('is_active').checked ? 1 : 0,
            };

            try {
                let response;
                if (productId) {
                    // Update
                    response = await axios.put(`/api/admin/products/${productId}`, formData);
                } else {
                    // Create
                    response = await axios.post('/api/admin/products', formData);
                }

                showToast(response.data.message, 'success');
                
                // Reset form
                this.reset();
                document.getElementById('product_id').value = '';
                clearErrors();
                
                // Reload products
                const search = document.getElementById('searchInput').value;
                loadProducts(1, search);

            } catch (error) {
                const errors = error.response?.data?.errors;
                
                if (errors) {
                    for (const [field, messages] of Object.entries(errors)) {
                        showError(field, Array.isArray(messages) ? messages[0] : messages);
                    }
                } else {
                    showToast(error.response?.data?.message || 'Failed to save product', 'error');
                }
            }
        });

        // Reset form
        document.getElementById('resetBtn').addEventListener('click', function() {
            document.getElementById('productForm').reset();
            document.getElementById('product_id').value = '';
            clearErrors();
        });

        // Search with debounce
        let searchTimeout;
        document.getElementById('searchInput').addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                loadProducts(1, e.target.value);
            }, 500);
        });

        // Initial load
        document.addEventListener('DOMContentLoaded', () => {
            loadProducts(1);
        });
    </script>
</body>
</html>
