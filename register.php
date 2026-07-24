<?php
// register.php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';

// If already logged in, no need to register again
if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$pageTitle = "Register";
$errors = [];

// Keep submitted values so the form can be re-filled on error (except passwords)
$oldName = '';
$oldEmail = '';
$oldMobile = '';
$oldAddress = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldName = trim($_POST['name'] ?? '');
    $oldEmail = trim($_POST['email'] ?? '');
    $oldMobile = trim($_POST['mobile'] ?? '');
    $oldAddress = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($oldName === '') {
        $errors[] = "Please enter your name.";
    }

    if (!filter_var($oldEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if (!preg_match('/^\d{10}$/', $oldMobile)) {
        $errors[] = "Mobile number must be exactly 10 digits.";
    }

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    // Check for duplicate email before attempting insert
    if (empty($errors)) {
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $checkStmt->execute([$oldEmail]);
        if ($checkStmt->fetch()) {
            $errors[] = "An account with this email already exists. Please log in instead.";
        }
    }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare(
            "INSERT INTO users (name, email, password, mobile, address, role, created_at)
             VALUES (?, ?, ?, ?, ?, 'customer', NOW())"
        );
        $stmt->execute([$oldName, $oldEmail, $hashedPassword, $oldMobile, $oldAddress]);

        // Log the new user in immediately
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['name'] = $oldName;
        $_SESSION['role'] = 'customer';

        header("Location: index.php");
        exit();
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<link rel="stylesheet" href="css/login.css" />
<?php require_once __DIR__ . '/includes/navbar.php'; ?>

<div class="auth-wrapper">
    <div class="auth-card">
        <h2>Create an Account</h2>

        <?php foreach ($errors as $error): ?>
            <div class="auth-error"><?php echo e($error); ?></div>
        <?php endforeach; ?>

        <form id="registerForm" method="post" action="register.php">
            <label for="regName">Full Name</label>
            <input type="text" id="regName" name="name" placeholder="Enter Full Name"
                   value="<?php echo e($oldName); ?>" />

            <label for="regEmail">Email</label>
            <input type="email" id="regEmail" name="email" placeholder="Enter Email"
                   value="<?php echo e($oldEmail); ?>" />

            <label for="regMobile">Mobile Number</label>
            <input type="text" id="regMobile" name="mobile" placeholder="Enter Mobile Number"
                   value="<?php echo e($oldMobile); ?>" />

            <label for="regAddress">Address</label>
            <input type="text" id="regAddress" name="address" placeholder="Enter Address"
                   value="<?php echo e($oldAddress); ?>" />

            <label for="regPassword">Password</label>
            <input type="password" id="regPassword" name="password" placeholder="Enter Password" />

            <label for="regConfirmPassword">Confirm Password</label>
            <input type="password" id="regConfirmPassword" name="confirm_password" placeholder="Re-enter Password" />

            <button type="submit">Register</button>
        </form>

        <div class="auth-switch">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>