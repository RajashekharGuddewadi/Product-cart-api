axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;

// Base URL for API
const apiUrl = "/api/admin/products";

// Reset Form
document.getElementById('resetBtn').addEventListener('click', () => {
    resetForm();
});

// Fetch Products
function fetchProducts() {
    axios.get(apiUrl)
        .then(res => {
            let tbody = '';
            res.data.data.data.forEach(product => {
                tbody += `<tr>
                    <td>${product.id}</td>
                    <td>${product.name}</td>
                    <td>${product.sku}</td>
                    <td>${product.price}</td>
                    <td>${product.stock}</td>
                    <td>${product.is_active ? 'Yes' : 'No'}</td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="editProduct(${product.id})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteProduct(${product.id})">Delete</button>
                        <button class="btn btn-sm btn-warning" onclick="toggleProduct(${product.id})">Toggle</button>
                    </td>
                </tr>`;
            });
            document.querySelector('#productTable tbody').innerHTML = tbody;
        });
}

// Save / Update Product
document.getElementById('productForm').addEventListener('submit', function(e) {
    e.preventDefault();

    let id = document.getElementById('product_id').value;
    let method = id ? 'put' : 'post';
    let url = id ? `${apiUrl}/${id}` : apiUrl;

    axios[method](url, {
        name: document.getElementById('name').value,
        sku: document.getElementById('sku').value,
        price: document.getElementById('price').value,
        stock: document.getElementById('stock').value,
        is_active: document.getElementById('is_active').checked ? 1 : 0
    }).then(res => {
        alert(res.data.message);
        resetForm();
        fetchProducts();
    }).catch(err => {
        if (err.response && err.response.data.errors) {
            let errors = err.response.data.errors;
            document.getElementById('error-name').innerText = errors.name ? errors.name[0] : '';
            document.getElementById('error-sku').innerText = errors.sku ? errors.sku[0] : '';
            document.getElementById('error-price').innerText = errors.price ? errors.price[0] : '';
            document.getElementById('error-stock').innerText = errors.stock ? errors.stock[0] : '';
        }
    });
});

// Edit Product
function editProduct(id) {
    axios.get(`${apiUrl}/${id}`)
        .then(res => {
            let product = res.data.data;
            document.getElementById('product_id').value = product.id;
            document.getElementById('name').value = product.name;
            document.getElementById('sku').value = product.sku;
            document.getElementById('price').value = product.price;
            document.getElementById('stock').value = product.stock;
            document.getElementById('is_active').checked = product.is_active;
        });
}

// Delete Product
function deleteProduct(id) {
    if (!confirm('Are you sure?')) return;
    axios.delete(`${apiUrl}/${id}`).then(res => {
        alert(res.data.message);
        fetchProducts();
    });
}

// Toggle Product
function toggleProduct(id) {
    axios.post(`${apiUrl}/${id}/toggle`).then(res => {
        alert(res.data.message);
        fetchProducts();
    });
}

// Reset Form
function resetForm() {
    document.getElementById('product_id').value = '';
    document.getElementById('productForm').reset();
    document.getElementById('error-name').innerText = '';
    document.getElementById('error-sku').innerText = '';
    document.getElementById('error-price').innerText = '';
    document.getElementById('error-stock').innerText = '';
}

// Initial Load
fetchProducts();
