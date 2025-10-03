<?php
require_once 'validation.php';
require_once 'auth.php';
require_once 'csrf.php';

// Initialize variables
$username = $password = '';
$error = '';
$success = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_post()) { $error = 'Invalid session. Please refresh and try again.'; }
    // Validate and sanitize input
    $username = validate_input($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate form
    $validation = validate_form(
        ['username' => $username, 'password' => $password],
        [
            'username' => ['required' => true],
            'password' => ['required' => true]
        ]
    );
    
    if (!$validation['valid']) {
        $error = reset($validation['errors']);
    } else {
        // Attempt login
        $result = login_user($username, $password);
        
        if ($result['success']) {
            $success = $result['message'];
            // Redirect to home page after successful login
            header('Location: index.php');
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

// Include header
include 'header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card auth-card">
                <div class="card-header">
                    <h3 class="text-center">Welcome back</h3>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    
                    <form method="post" class="needs-validation" novalidate>
                        <?php csrf_field(); ?>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                            <div class="invalid-feedback">Please enter your username</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="invalid-feedback">Please enter your password</div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Sign in</button>
                        </div>
                    </form>
                    
                    <div class="mt-3 text-center">
                        <a href="register.php">Create an account</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/validation.js"></script>

<?php include 'footer.php'; ?>