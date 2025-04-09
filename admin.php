<?php
// Start session
session_start();

// Ensure the user is an admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "ecommerce");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$product_id = $name = $description = $price = $stock = $image = "";
$action = "add";
$message = "";
$message_type = "";

// Handle DELETE action
if (isset($_GET['delete'])) {
    $product_id = intval($_GET['delete']);
    
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
    $product_id = intval($_GET['edit']);
    $action = "edit";
    
    $edit_query = "SELECT * FROM products WHERE id = ?";
    $edit_stmt = $conn->prepare($edit_query);
    $edit_stmt->bind_param("i", $product_id);
    $edit_stmt->execute();
    $result = $edit_stmt->get_result();
    
    if ($result->num_rows == 1) {
        $product = $result->fetch_assoc();
        $name = htmlspecialchars($product['name']);
        $description = htmlspecialchars($product['description']);
        $price = $product['price'];
        $stock = $product['stock'];
        $image = htmlspecialchars($product['image_url']);
    } else {
        $message = "Product not found!";
        $message_type = "danger";
    }
}

// Handle form submission (ADD or UPDATE)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $image = trim($_POST['image']);
    
    if (empty($name) || empty($description) || $price <= 0) {
        $message = "Please fill all required fields!";
        $message_type = "danger";
    } else {
        if ($_POST['action'] == 'add') {
            $add_query = "INSERT INTO products (name, description, price, stock, image_url) VALUES (?, ?, ?, ?, ?)";
            $add_stmt = $conn->prepare($add_query);
            $add_stmt->bind_param("ssdis", $name, $description, $price, $stock, $image);
            
            if ($add_stmt->execute()) {
                $message = "Product added successfully!";
                $message_type = "success";
            } else {
                $message = "Error adding product: " . $conn->error;
                $message_type = "danger";
            }
        } else {
            $product_id = intval($_POST['product_id']);
            $update_query = "UPDATE products SET name = ?, description = ?, price = ?, stock = ?, image_url = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("ssdisi", $name, $description, $price, $stock, $image, $product_id);
            
            if ($update_stmt->execute()) {
                $message = "Product updated successfully!";
                $message_type = "success";
            } else {
                $message = "Error updating product: " . $conn->error;
                $message_type = "danger";
            }
        }
    }
}

// Fetch all products
$products_query = "SELECT * FROM products ORDER BY id DESC";
$products_result = $conn->query($products_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Product Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script>
        function confirmDelete(productId, productName) {
            if (confirm(`Are you sure you want to delete "${decodeURIComponent(productName)}"?`)) {
                window.location.href = 'admin.php?delete=' + productId;
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h1><?php echo ($action == 'add') ? 'Add Product' : 'Edit Product'; ?></h1>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="action" value="<?php echo $action; ?>">
            <?php if ($action == 'edit'): ?>
                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <?php endif; ?>

            <label>Product Name*</label>
            <input type="text" name="name" value="<?php echo $name; ?>" required class="form-control">

            <label>Price ($)*</label>
            <input type="number" step="0.01" name="price" value="<?php echo $price; ?>" required class="form-control">

            <label>Stock</label>
            <input type="number" name="stock" value="<?php echo $stock; ?>" class="form-control">

            <label>Description*</label>
            <textarea name="description" required class="form-control"><?php echo $description; ?></textarea>

            <label>Image URL</label>
            <input type="text" name="image" value="<?php echo $image; ?>" class="form-control">

            <button type="submit" class="btn btn-primary mt-3"><?php echo ($action == 'add') ? 'Add Product' : 'Update Product'; ?></button>
        </form>

        <h2 class="mt-5">Product List</h2>
        <table class="table">
            <thead><tr><th>ID</th><th>Name</th><th>Price</th><th>Stock</th><th>Actions</th></tr></thead>
            <tbody>
                <?php while ($product = $products_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td>$<?php echo number_format($product['price'], 2); ?></td>
                        <td><?php echo $product['stock']; ?></td>
                        <td>
                            <a href="admin.php?edit=<?php echo $product['id']; ?>" class="btn btn-info btn-sm">Edit</a>
                            <a href="#" onclick="confirmDelete(<?php echo $product['id']; ?>, '<?php echo urlencode($product['name']); ?>')" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
