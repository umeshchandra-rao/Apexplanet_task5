<?php
require_once 'validation.php';
require_once 'auth.php';

// Initialize variables
$username = $email = '';
$role = 'user';
$error = '';
$success = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $username = validate_input($_POST['username'] ?? '');
    $email = validate_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'user';
    
    // Validate form
    $validation = validate_form(
        [
            'username' => $username, 
            'email' => $email,
            'password' => $password
        ],
        [
            'username' => ['required' => true, 'min_length' => 3, 'max_length' => 50],
            'email' => ['required' => true, 'type' => 'email'],
            'password' => ['required' => true, 'type' => 'password']
        ]
    );
    
    if (!$validation['valid']) {
        $error = reset($validation['errors']);
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        // Register user
        $result = register_user($username, $password, $email, $role);
        
        if ($result['success']) {
            $success = $result['message'];
            // Clear form data after successful registration
            $username = $email = '';
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
                    <h3 class="text-center">Create account</h3>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <?php echo $success; ?> <a href="login.php">Login now</a>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required minlength="3" maxlength="50">
                            <div class="invalid-feedback">Username must be 3-50 characters</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                            <div class="invalid-feedback">Please enter a valid email address</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="8">
                            <div class="invalid-feedback">Password must be at least 8 characters with uppercase, lowercase, and numbers</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            <div class="invalid-feedback">Passwords must match</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</label>
                            <select class="form-control" id="role" name="role" required>
                                <option value="user" <?php echo ($role==='user')?'selected':''; ?>>User</option>
                                <option value="editor" <?php echo ($role==='editor')?'selected':''; ?>>Editor</option>
                                <option value="admin" <?php echo ($role==='admin')?'selected':''; ?>>Admin</option>
                            </select>
                            <div class="invalid-feedback">Please choose a role</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Sign up</button>
                        </div>
                    </form>
                    
                    <div class="mt-3 text-center">
                        <p>Already have an account? <a href="login.php">Login here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="js/validation.js"></script>

<?php include 'footer.php'; ?>