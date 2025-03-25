<?php
// Start the session
session_start();

// Check if user is logged in and is an admin
// Note: You'll need to add an "is_admin" column to your users table
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    // Not logged in or not an admin, redirect to login
    header('Location: login.php');
    exit();
}

// Database connection
$servername = "localhost";
$username = "root"; // Change to your database username
$password = ""; // Change to your database password
$dbname = "ecommerce"; // Change to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$product_id = $name = $description = $price = $stock = $image = "";
$action = "add"; // Default action
$message = "";
$message_type = "";

// Handle DELETE action
if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];
    
    // Delete the product
    $delete_query = "DELETE FROM products WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("i", $product_id);
    
    if ($delete_stmt->execute()) {
        $message = "Product deleted successfully!";
        $message_type = "success";
    } else {
        $message = "Error deleting product: " . $conn->error;
        $message_type = "danger";
    }
}

// Handle EDIT action - Load product data
if (isset($_GET['edit'])) {
    $product_id = $_GET['edit'];
    $action = "edit";
    
    // Get product data
    $edit_query = "SELECT * FROM products WHERE id = ?";
    $edit_stmt = $conn->prepare($edit_query);
    $edit_stmt->bind_param("i", $product_id);
    $edit_stmt->execute();
    $result = $edit_stmt->get_result();
    
    if ($result->num_rows == 1) {
        $product = $result->fetch_assoc();
        $name = $product['name'];
        $description = $product['description'];
        $price = $product['price'];
        $stock = $product['stock'];
        $image = $product['image'];
    } else {
        $message = "Product not found!";
        $message_type = "danger";
    }
}

// Handle form submission (ADD or UPDATE)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $image = trim($_POST['image']);
    
    // Validate input
    if (empty($name) || empty($description) || $price <= 0) {
        $message = "Please fill all required fields with valid values!";
        $message_type = "danger";
    } else {
        // Check if it's an add or edit operation
        if ($_POST['action'] == 'add') {
            // INSERT new product
            $add_query = "INSERT INTO products (name, description, price, stock, image) VALUES (?, ?, ?, ?, ?)";
            $add_stmt = $conn->prepare($add_query);
            $add_stmt->bind_param("ssdis", $name, $description, $price, $stock, $image);
            
            if ($add_stmt->execute()) {
                $message = "Product added successfully!";
                $message_type = "success";
                // Reset form
                $name = $description = $price = $stock = $image = "";
            } else {
                $message = "Error adding product: " . $conn->error;
                $message_type = "danger";
            }
        } else {
            // UPDATE existing product
            $product_id = $_POST['product_id'];
            $update_query = "UPDATE products SET name = ?, description = ?, price = ?, stock = ?, image = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("ssdisi", $name, $description, $price, $stock, $image, $product_id);
            
            if ($update_stmt->execute()) {
                $message = "Product updated successfully!";
                $message_type = "success";
                // Reset to add mode
                $action = "add";
                $name = $description = $price = $stock = $image = "";
            } else {
                $message = "Error updating product: " . $conn->error;
                $message_type = "danger";
            }
        }
    }
}

// Fetch all products for display
$products_query = "SELECT * FROM products ORDER BY id DESC";
$products_result = $conn->query($products_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Product Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .product-form {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .product-image {
            max-width: 80px;
            max-height: 80px;
            object-fit: cover;
        }
        .action-buttons .btn {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
            <a class="navbar-brand" href="admin.php">Admin Panel</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="admin.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_orders.php">Orders</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_users.php">Users</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Visit Store</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><?php echo $action == 'add' ? 'Add New Product' : 'Edit Product'; ?></h1>
            <?php if ($action == 'edit'): ?>
                <a href="admin.php" class="btn btn-secondary">Cancel Edit</a>
            <?php endif; ?>
        </div>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <!-- Product Form -->
        <div class="product-form">
            <form method="post" action="admin.php">
                <input type="hidden" name="action" value="<?php echo $action; ?>">
                <?php if ($action == 'edit'): ?>
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                <?php endif; ?>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="name">Product Name*</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="price">Price ($)*</label>
                        <input type="number" step="0.01" min="0.01" class="form-control" id="price" name="price" value="<?php echo $price; ?>" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="stock">Stock Quantity</label>
                        <input type="number" min="0" class="form-control" id="stock" name="stock" value="<?php echo $stock; ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description*</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($description); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Image URL</label>
                    <input type="text" class="form-control" id="image" name="image" value="<?php echo htmlspecialchars($image); ?>">
                    <small class="form-text text-muted">Enter the URL to the product image. For a production site, you would implement file uploading.</small>
                </div>

                <button type="submit" class="btn btn-primary">
                    <?php echo $action == 'add' ? 'Add Product' : 'Update Product'; ?>
                </button>
            </form>
        </div>

        <!-- Products Table -->
        <h2 class="mb-3">Product List</h2>
        
        <?php if ($products_result->num_rows == 0): ?>
            <div class="alert alert-info">No products found. Add your first product using the form above.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($product = $products_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td>
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                                    <?php else: ?>
                                        <div class="text-muted">No image</div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td>$<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo $product['stock']; ?></td>
                                <td class="action-buttons">
                                    <a href="admin.php?edit=<?php echo $product['id']; ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="#" class="btn btn-sm btn-danger" onclick="confirmDelete(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function confirmDelete(productId, productName) {
            if (confirm('Are you sure you want to delete "' + productName + '"? This action cannot be undone.')) {
                window.location.href = 'admin.php?delete=' + productId;
            }
        }
    </script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>