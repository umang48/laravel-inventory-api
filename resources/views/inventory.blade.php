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
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Custom script goes here in the next step -->
    <script>
    $(document).ready(function() {
        // 1. Handle Login
        $('#login-btn').click(function() {
            $.ajax({
                url: '/api/login',
                method: 'POST',
                data: {
                    email: $('#email').val(),
                    password: $('#password').val()
                },
                success: function(response) {
                    // Store token and setup jQuery default headers
                    let token = response.token;
                    localStorage.setItem('api_token', token);
                    
                    $.ajaxSetup({
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/json'
                        }
                    });

                    $('#login-panel').hide();
                    $('#data-panel').show();
                    
                    // Fetch the data
                    fetchCategories();
                    fetchProducts();
                },
                error: function(xhr) {
                    alert('Login failed: ' + xhr.responseJSON.message);
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

        // 2. Fetch Categories
        function fetchCategories() {
            $.get('/api/categories', function(response) {
                $('#category-list').empty();
                // Assuming your CategoryResource returns a 'data' array
                let categories = response.data || response; 
                categories.forEach(function(category) {
                    $('#category-list').append(`<li class="list-group-item">${category.name}</li>`);
                });
            });
        }

        // 3. Fetch Products
        function fetchProducts() {
            $.get('/api/products', function(response) {
                $('#product-list').empty();
                $('#product-select').empty(); // Clear dropdown
                
                let products = response.data || response;
                
                products.forEach(function(product) {
                    // Populate List
                    $('#product-list').append(
                        `<li class="list-group-item d-flex justify-content-between align-items-center">
                            ${product.name} 
                            <span class="badge bg-success rounded-pill">Stock: ${product.stock_level}</span>
                        </li>`
                    );
                    
                    // Populate Dropdown
                    $('#product-select').append(
                        `<option value="${product.id}">${product.name} (Cur: ${product.stock_level})</option>`
                    );
                });
            });
        }
    });
</script>
</body>
</html>