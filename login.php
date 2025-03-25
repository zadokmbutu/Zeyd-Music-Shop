<?php
session_start(); // Must be first, before any output

include 'header.php';
include 'navbar.php';
include 'database.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Fetch user by email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_regenerate_id(true);
            }

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
                exit;
            } elseif ($user['role'] === 'teacher') {
                header("Location: teacher_dashboard.php");
                exit;
            } elseif ($user['role'] === 'student') {
                header("Location: student_dashboard.php");
                exit;
            } else {
                $message = "Role not recognized.";
            }
        } else {
            $message = "Invalid password. Please try again.";
        }
    } else {
        $message = "No account found with this email. Please register.";
    }
}
?>


<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-center">Login</h2>
            <?php if (!empty($message)): ?>
                <div class="alert alert-danger text-center"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" id="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" id="password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
                <div class="mt-3 text-center">
                    <p>Don't have an account? <a href="register.php">Register here</a>.</p>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>