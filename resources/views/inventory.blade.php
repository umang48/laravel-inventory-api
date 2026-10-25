<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="mb-4">API Dashboard</h2>
        
        <!-- Login Panel -->
        <div id="login-panel" class="card mb-4">
            <div class="card-body">
                <h5>Admin Login</h5>
                <input type="email" id="email" class="form-control mb-2" value="admin@test.com">
                <input type="password" id="password" class="form-control mb-2" value="password123">
                <button id="login-btn" class="btn btn-primary">Login & Get Token</button>
            </div>
        </div>

        <!-- User Profile & Logout Bar -->
        <div id="user-navbar" class="card mb-4 bg-white shadow-sm" style="display: none;">
            <div class="card-body d-flex justify-content-between align-items-center py-2">
                <h5 class="mb-0 text-secondary">Logged in as: <strong id="profile-name" class="text-dark"></strong></h5>
                <button id="logout-btn" class="btn btn-outline-danger btn-sm">Logout</button>
            </div>
        </div>

        <!-- Data Panel (Hidden until logged in) -->
        <div id="data-panel" style="display: none;">
            <div class="row">
                <div class="col-md-4">
                    <h4>Categories</h4>
                    <ul id="category-list" class="list-group mb-3"></ul>
                </div>
                <div class="col-md-4">
                    <h4>Products</h4>
                    <ul id="product-list" class="list-group mb-3"></ul>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-dark text-white">Update Stock</div>
                        <div class="card-body">
                            <form id="stock-form">
                                <div class="mb-2">
                                    <label>Product</label>
                                    <select id="product-select" class="form-control" required></select>
                                </div>
                                <div class="mb-2">
                                    <label>Action</label>
                                    <select id="stock-action" class="form-control">
                                        <option value="add">Add Stock</option>
                                        <option value="subtract">Subtract Stock</option>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label>Quantity</label>
                                    <input type="number" id="stock-quantity" class="form-control" min="1" required>
                                </div>
                                <div class="mb-3">
                                    <label>Notes</label>
                                    <input type="text" id="stock-notes" class="form-control" placeholder="e.g. New shipment">
                                </div>
                                <button type="submit" class="btn btn-success w-100">Submit Transaction</button>
                            </form>
                            <div id="stock-alert" class="mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- New Row: Creation Forms -->
<div class="row mb-4">
    <!-- Create Category Form -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">Add Category</div>
            <div class="card-body">
                <form id="category-form">
                    <input type="text" id="cat-name" class="form-control mb-2" placeholder="Category Name (e.g. Mobiles)" required>
                    <input type="text" id="cat-slug" class="form-control mb-2" placeholder="Slug (e.g. mobiles)" required>
                    <button type="submit" class="btn btn-primary w-100">Create Category</button>
                </form>
                <div id="cat-alert" class="mt-2"></div>
            </div>
        </div>
    </div>

    <!-- Create Product Form -->
    <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">Add Product</div>
                <div class="card-body">
                    <form id="create-product-form">
                        <!-- We will populate this dropdown dynamically -->
                        <select id="prod-category" class="form-control mb-2" required>
                            <option value="">Select Category...</option>
                        </select>
                        <input type="text" id="prod-name" class="form-control mb-2" placeholder="Product Name (e.g. iPhone 15)" required>
                        <input type="text" id="prod-sku" class="form-control mb-2" placeholder="SKU (e.g. IPH-15)" required>
                        <div class="input-group mb-2">
                            <span class="input-group-text">₹</span>
                            <input type="number" id="prod-price" class="form-control" placeholder="Price" min="0" step="0.01" required>
                        </div>
                        <input type="number" id="prod-stock" class="form-control mb-2" placeholder="Initial Stock" min="0" required>
                        <button type="submit" class="btn btn-success w-100">Create Product</button>
                    </form>
                    <div id="prod-alert" class="mt-2"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Your existing Category/Product lists and Stock Update form stay below this -->




    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Custom script goes here in the next step -->
    <script>
    $(document).ready(function() {

        // 1. AUTO-LOGIN CHECK
        let savedToken = localStorage.getItem('api_token');
        if (savedToken) {
            activateDashboard(savedToken);
        }

        // Helper function to set headers and load UI
        function activateDashboard(token) {
        $.ajaxSetup({
            headers: {
                'Authorization': 'Bearer ' + token,
                'Accept': 'application/json'
            }
        });
        
        // Fetch User Profile
        $.get('/api/profile', function(user) {
            $('#profile-name').text(user.name);
            $('#user-navbar').show();
            $('#login-panel').hide();
            $('#data-panel').show();
            
            fetchCategories();
            fetchProducts();
        }).fail(function() {
            // Token is invalid/expired
            localStorage.removeItem('api_token');
            location.reload();
        });
    }


        // 1. Handle Login
        // 2. Handle Login Button
        $('#login-btn').click(function() {
            $.ajax({
                url: '/api/login',
                method: 'POST',
                data: {
                    email: $('#email').val(),
                    password: $('#password').val()
                },
                success: function(response) {
                    localStorage.setItem('api_token', response.token);
                    activateDashboard(response.token);
                },
                error: function(xhr) {
                    alert('Login failed: ' + xhr.responseJSON.message);
                }
            });
        });

        $('#logout-btn').click(function() {
            $.ajax({
                url: '/api/logout',
                method: 'POST',
                success: function() {
                    localStorage.removeItem('api_token');
                    location.reload(); // Refresh the page to reset the UI
                },
                error: function() {
                    alert('Error logging out.');
                }
            });
        });

        

        // 2. Fetch Categories
        // 3. Fetch Categories
        function fetchCategories() {
            $.get('/api/categories', function(response) {
                $('#category-list').empty();
                $('#prod-category').empty().append('<option value="">Select Category...</option>');
                
                let categories = response.data || response; 
                categories.forEach(function(category) {
                    $('#category-list').append(
                        `<li class="list-group-item d-flex justify-content-between align-items-center">
                            ${category.name}
                            <button class="btn btn-sm btn-danger delete-category-btn" data-id="${category.id}">Delete</button>
                        </li>`
                    );
                    $('#prod-category').append(`<option value="${category.id}">${category.name}</option>`);
                });
            }).fail(function(xhr) {
                if(xhr.status === 401) {
                    localStorage.removeItem('api_token');
                    location.reload();
                }
            });
        }

        // 3. Fetch Products
        function fetchProducts() {
            $.get('/api/products', function(response) {
                $('#product-list').empty();
                $('#product-select').empty();
                
                let products = response.data || response;
                products.forEach(function(product) {
                    $('#product-list').append(
                        `<li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                ${product.name} 
                                <span class="badge bg-success rounded-pill ms-2">Stock: ${product.stock_level}</span>
                            </div>
                            <button class="btn btn-sm btn-danger delete-product-btn" data-id="${product.id}">Delete</button>
                        </li>`
                    );
                    $('#product-select').append(`<option value="${product.id}">${product.name}</option>`);
                });
            });
        }


        // Handle Category Creation
        $('#category-form').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: '/api/categories',
                method: 'POST',
                data: {
                    name: $('#cat-name').val(),
                    slug: $('#cat-slug').val()
                },
                success: function() {
                    $('#cat-alert').html('<div class="alert alert-success">Category created!</div>');
                    $('#category-form')[0].reset();
                    fetchCategories();
                },
                error: function(xhr) {
                    $('#cat-alert').html(`<div class="alert alert-danger">${xhr.responseJSON.message}</div>`);
                }
            });
        });

        // 4. Handle Stock Transaction
        $('#stock-form').submit(function(e) {
            e.preventDefault(); // Prevent standard HTML form submission
            
            let productId = $('#product-select').val();
            let payload = {
                type: $('#stock-action').val(),
                quantity: $('#stock-quantity').val(),
                notes: $('#stock-notes').val()
            };

            $.ajax({
                url: `/api/products/${productId}/stock`,
                method: 'POST',
                data: payload,
                success: function(response) {
                    $('#stock-alert').html(`<div class="alert alert-success">${response.message}. New stock: ${response.current_stock}</div>`);
                    $('#stock-form')[0].reset(); // Clear the form fields
                    fetchProducts(); // Refresh the product list immediately
                },
                error: function(xhr) {
                    let errorMsg = xhr.responseJSON.message || 'An error occurred';
                    $('#stock-alert').html(`<div class="alert alert-danger">${errorMsg}</div>`);
                }
            });
        });

        // Handle Product Creation
        $('#create-product-form').submit(function(e) {
            e.preventDefault();
            $.ajax({
                url: '/api/products',
                method: 'POST',
                data: {
                    category_id: $('#prod-category').val(),
                    sku: $('#prod-sku').val(),
                    name: $('#prod-name').val(),
                    price: $('#prod-price').val(),
                    stock_level: $('#prod-stock').val()
                },
                success: function(response) {
                    $('#prod-alert').html('<div class="alert alert-success">Product created successfully!</div>');
                    $('#create-product-form')[0].reset();
                    fetchProducts(); // Instantly refresh the product list and stock dropdown
                },
                error: function(xhr) {
                    let errorMsg = xhr.responseJSON.message || 'Validation failed';
                    $('#prod-alert').html(`<div class="alert alert-danger">${errorMsg}</div>`);
                }
            });
        });



        // Handle Product Deletion
        $('#product-list').on('click', '.delete-product-btn', function() {
            let productId = $(this).data('id');
            
            // Confirm before destructive action
            if (confirm('Are you sure you want to delete this product? This cannot be undone.')) {
                $.ajax({
                    url: `/api/products/${productId}`,
                    method: 'DELETE',
                    success: function() {
                        $('#prod-alert').html('<div class="alert alert-success">Product deleted successfully!</div>');
                        fetchProducts(); // Refresh the list instantly
                    },
                    error: function(xhr) {
                        let errorMsg = xhr.responseJSON.message || 'Error deleting product';
                        $('#prod-alert').html(`<div class="alert alert-danger">${errorMsg}</div>`);
                    }
                });
            }
        });


        // Handle Category Deletion
        $('#category-list').on('click', '.delete-category-btn', function() {
            let categoryId = $(this).data('id');
            
            // Emphasize the cascade effect in the warning
            if (confirm('WARNING: Deleting this category will automatically delete ALL products inside it. Do you want to proceed?')) {
                $.ajax({
                    url: `/api/categories/${categoryId}`,
                    method: 'DELETE',
                    success: function() {
                        $('#cat-alert').html('<div class="alert alert-success">Category and related products deleted!</div>');
                        fetchCategories(); // Refresh the categories list
                        fetchProducts();   // Refresh products to clear deleted items from UI
                    },
                    error: function(xhr) {
                        let errorMsg = xhr.responseJSON.message || 'Error deleting category';
                        $('#cat-alert').html(`<div class="alert alert-danger">${errorMsg}</div>`);
                    }
                });
            }
        });




    });
</script>
</body>
</html>