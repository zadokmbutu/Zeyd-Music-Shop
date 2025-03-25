<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    header("Location: index.php");
    exit();
}

// Get product details
$sql = "SELECT product_id, name, description, price, image_url FROM products WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product["name"]); ?> - E-commerce Store</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .product-image {
            max-height: 500px;
            object-fit: contain;
        }
        .quantity-selector {
            max-width: 100px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">E-commerce Store</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a href="cart.php" class="btn btn-outline-light me-2">
                        <i class="fas fa-shopping-cart"></i> Cart
                        <span class="badge bg-danger" id="cart-count">0</span>
                    </a>
                    <a href="login.php" class="btn btn-outline-light">
                        <i class="fas fa-user"></i> Login
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active">Product Details</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-6 mb-4">
                <?php
                $image = !empty($product["image_url"]) ? $product["image_url"] : "https://via.placeholder.com/600x600?text=No+Image";
                echo '<img src="' . htmlspecialchars($image) . '" class="img-fluid rounded product-image" alt="' . htmlspecialchars($product["name"]) . '">';
                ?>
                
                <div class="mt-3 row">
                    <?php
                    // Thumbnail images (placeholder for multiple product images)
                    for($i = 0; $i < 4; $i++) {
                        echo '<div class="col-3 mb-2">
                            <img src="' . htmlspecialchars($image) . '" class="img-fluid rounded border" alt="Thumbnail">
                        </div>';
                    }
                    ?>
                </div>
            </div>
            
            <div class="col-md-6">
                <h1 class="mb-3"><?php echo htmlspecialchars($product["name"]); ?></h1>
                
                <div class="mb-3">
                    <span class="fs-4 fw-bold text-primary">$<?php echo number_format($product["price"], 2); ?></span>
                    <?php if ($product["price"] > 50) { ?>
                        <span class="badge bg-success ms-2">Free Shipping</span>
                    <?php } ?>
                </div>
                
                <div class="mb-3">
                    <div class="text-warning mb-2">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                        <span class="text-muted ms-2">(24 reviews)</span>
                    </div>
                </div>
                
                <div class="mb-4">
                    <h5>Description</h5>
                    <p><?php echo nl2br(htmlspecialchars($product["description"])); ?></p>
                </div>
                
                <div class="mb-4">
                    <h5>Quantity</h5>
                    <div class="input-group quantity-selector mb-3">
                        <button class="btn btn-outline-secondary" type="button" id="decrease-quantity">-</button>
                        <input type="number" class="form-control text-center" id="quantity" value="1" min="1" max="10">
                        <button class="btn btn-outline-secondary" type="button" id="increase-quantity">+</button>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <button class="btn btn-primary btn-lg" id="add-to-cart" data-product-id="<?php echo $product["product_id"]; ?>">
                        <i class="fas fa-cart-plus me-2"></i>Add to Cart
                    </button>
                    <button class="btn btn-outline-secondary">
                        <i class="fas fa-heart me-2"></i>Add to Wishlist
                    </button>
                </div>
                
                <div class="mt-4">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-truck text-muted me-3 fs-5"></i>
                        <div>
                            <strong>Free Shipping</strong>
                            <p class="mb-0 text-muted">For orders over $50</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-undo text-muted me-3 fs-5"></i>
                        <div>
                            <strong>30 Days Return</strong>
                            <p class="mb-0 text-muted">30 days money back guarantee</p>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <i class="fas fa-shield-alt text-muted me-3 fs-5"></i>
                        <div>
                            <strong>Secure Payment</strong>
                            <p class="mb-0 text-muted">Your payment is secure with us</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Product Details Tabs -->
        <div class="row mt-5">
            <div class="col-12">
                <ul class="nav nav-tabs" id="productTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab">Details</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">Reviews</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping" type="button" role="tab">Shipping</button>
                    </li>
                </ul>
                <div class="tab-content p-4 border border-top-0 rounded-bottom" id="productTabsContent">
                    <div class="tab-pane fade show active" id="details" role="tabpanel">
                        <h4>Product Specifications</h4>
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <th scope="row" style="width: 30%;">Product ID</th>
                                    <td><?php echo $product["product_id"]; ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Product Name</th>
                                    <td><?php echo htmlspecialchars($product["name"]); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Price</th>
                                    <td>$<?php echo number_format($product["price"], 2); ?></td>
                                </tr>
                                <tr>
                                    <th scope="row">Availability</th>
                                    <td><span class="badge bg-success">In Stock</span></td>
                                </tr>
                                <tr>
                                    <th scope="row">Category</th>
                                    <td>Electronics</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="reviews" role="tabpanel">
                        <h4 class="mb-4">Customer Reviews</h4>
                        
                        <!-- Sample Review -->
                        <div class="mb-4 pb-4 border-bottom">
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <img src="https://via.placeholder.com/50" class="rounded-circle" alt="User">
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-0">John Doe</h5>
                                    <div class="text-warning">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <p class="text-muted">Posted on Jan 10, 2023</p>
                                </div>
                            </div>
                            <p>Great product! Exactly as described and arrived quickly. Would definitely recommend.</p>
                        </div>
                        
                        <!-- Sample Review -->
                        <div class="mb-4">
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0">
                                    <img src="https://via.placeholder.com/50" class="rounded-circle" alt="User">
                                </div>
                                <div class="ms-3">
                                    <h5 class="mb-0">Jane Smith</h5>
                                    <div class="text-warning">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="far fa-star"></i>
                                    </div>
                                    <p class="text-muted">Posted on Dec 5, 2022</p>
                                </div>
                            </div>
                            <p>Good quality product but shipping took longer than expected. Otherwise satisfied with my purchase.</p>
                        </div>
                        
                        <button class="btn btn-primary">Write a Review</button>
                    </div>
                    <div class="tab-pane fade" id="shipping" role="tabpanel">
                        <h4>Shipping Information</h4>
                        <p>We offer the following shipping options:</p>
                        <ul>
                            <li>Standard Shipping: 5-7 business days</li>
                            <li>Express Shipping: 2-3 business days</li>
                            <li>Next Day Delivery: Order before 2pm for next business day delivery</li>
                        </ul>
                        <p>Free shipping on all orders over $50.</p>
                        <p>International shipping available to select countries.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Related Products -->
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">Related Products</h3>
            </div>
            
            <?php
            // Get related products (simple implementation - just get 4 random products)
            $sql = "SELECT product_id, name, price, image_url FROM products WHERE product_id != ? ORDER BY RAND() LIMIT 4";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $related_result = $stmt->get_result();
            
            if ($related_result->num_rows > 0) {
                while($row = $related_result->fetch_assoc()) {
                    $image = !empty($row["image_url"]) ? $row["image_url"] : "https://via.placeholder.com/300x200?text=No+Image";
                    
                    echo '<div class="col-6 col-md-3 mb-4">
                        <div class="card h-100">
                            <img src="' . htmlspecialchars($image) . '" class="card-img-top" alt="' . htmlspecialchars($row["name"]) . '" style="height: 150px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title">' . htmlspecialchars($row["name"]) . '</h5>
                                <p class="card-text fw-bold text-primary">$' . number_format($row["price"], 2) . '</p>
                                <a href="product.php?id=' . $row["product_id"] . '" class="btn btn-outline-primary btn-sm">View Details</a>
                            </div>
                        </div>
                    </div>';
                }
            }
            ?>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>E-commerce Store</h5>
                    <p>Your one-stop shop for all your needs.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-white">Home</a></li>
                        <li><a href="#" class="text-white">Products</a></li>
                        <li><a href="#" class="text-white">About Us</a></li>
                        <li><a href="#" class="text-white">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Us</h5>
                    <address>
                        <p><i class="fas fa-map-marker-alt"></i> 123 E-Commerce St, City</p>
                        <p><i class="fas fa-phone"></i> +1 (555) 123-4567</p>
                        <p><i class="fas fa-envelope"></i> info@ecommerce.com</p>
                    </address>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; <?php echo date("Y"); ?> E-commerce Store. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Product Page Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Quantity selector
            const quantityInput = document.getElementById('quantity');
            const decreaseBtn = document.getElementById('decrease-quantity');
            const increaseBtn = document.getElementById('increase-quantity');
            
            decreaseBtn.addEventListener('click', function() {
                let value = parseInt(quantityInput.value);
                if (value > 1) {
                    quantityInput.value = value - 1;
                }
            });
            
            increaseBtn.addEventListener('click', function() {
                let value = parseInt(quantityInput.value);
                if (value < 10) {
                    quantityInput.value = value + 1;
                }
            });
            
            // Add to cart
            const addToCartBtn = document.getElementById('add-to-cart');
            
            addToCartBtn.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                const quantity = parseInt(quantityInput.value);
                addToCart(productId, quantity);
            });
            
            // Update cart count based on localStorage
            updateCartCount();
        });
        
        function addToCart(productId, quantity = 1) {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            
            // Check if product already in cart
            const existingProductIndex = cart.findIndex(item => item.id === productId);
            
            if (existingProductIndex >= 0) {
                cart[existingProductIndex].quantity += quantity;
            } else {
                cart.push({
                    id: productId,
                    quantity: quantity
                });
            }
            
            // Save to localStorage
            localStorage.setItem('cart', JSON.stringify(cart));
            
            // Update cart count
            updateCartCount();
            
            // Show alert
            alert('Product added to cart!');
        }
        
        function updateCartCount() {
            const cart = JSON.parse(localStorage.getItem('cart')) || [];
            const count = cart.reduce((total, item) => total + item.quantity, 0);
            document.getElementById('cart-count').textContent = count;
        }
    </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>