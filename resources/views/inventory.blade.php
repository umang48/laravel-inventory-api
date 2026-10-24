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
                <div class="col-md-6">
                    <h4>Categories</h4>
                    <ul id="category-list" class="list-group mb-3"></ul>
                </div>
                <div class="col-md-6">
                    <h4>Products</h4>
                    <ul id="product-list" class="list-group mb-3"></ul>
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
                let products = response.data || response;
                products.forEach(function(product) {
                    $('#product-list').append(
                        `<li class="list-group-item d-flex justify-content-between align-items-center">
                            ${product.name} 
                            <span class="badge bg-success rounded-pill">Stock: ${product.stock_level}</span>
                        </li>`
                    );
                });
            });
        }
    });
</script>
</body>
</html>