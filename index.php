<?php include 'header.php' ;?>
<?php include 'navbar.php' ;?>
<?php include 'database.php' ;?>
<?php include 'slideshow.php' ;?>
<body>
    <!-- Navigation -->
   
    <!-- Main Content -->
    <div class="container py-5">
       
        
       <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $image = !empty($row["image_url"]) ? $row["image_url"] : "https://via.placeholder.com/300x200?text=No+Image";
                    
                    echo '<div class="col">
                        <div class="card h-100">
                            <img src="' . htmlspecialchars($image) . '" class="card-img-top" alt="' . htmlspecialchars($row["name"]) . '">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">' . htmlspecialchars($row["name"]) . '</h5>
                                <p class="card-text">' . htmlspecialchars(substr($row["description"], 0, 100)) . '</p>
                                <div class="mt-auto">
                                    <p class="fs-5 fw-bold text-primary">$' . number_format($row["price"], 2) . '</p>
                                    <button class="btn btn-primary add-to-cart" data-product-id="' . $row["product_id"] . '">
                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                    </button>
                                    <a href="product.php?id=' . $row["product_id"] . '" class="btn btn-outline-secondary">
                                        <i class="fas fa-eye"></i> Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>';
                }
            } else {
                echo "<div class='col-12'><p class='text-center'>No products found</p></div>";
            }
            ?>
        </div>
    </div>

    <!-- Footer -->


    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Cart Functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add to cart buttons
            const addToCartButtons = document.querySelectorAll('.add-to-cart');
            
            addToCartButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    addToCart(productId);
                });
            });
            
            // Update cart count based on localStorage
            updateCartCount();
        });
        
        function addToCart(productId) {
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            
            // Check if product already in cart
            const existingProduct = cart.find(item => item.id === productId);
            
            if (existingProduct) {
                existingProduct.quantity += 1;
            } else {
                cart.push({
                    id: productId,
                    quantity: 1
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
$conn->close();
?>
<?php include 'footer.php' ;?>

