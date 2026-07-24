<?php
// login.php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';

// If already logged in, send to the right place
if (isLoggedIn()) {
    header("Location: " . (isAdmin() ? "admin/index.php" : "index.php"));
    exit();
}

$pageTitle = "Login";
$errors = [];
$oldEmail = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldEmail = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($oldEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if ($password === '') {
        $errors[] = "Please enter your password.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
        $stmt->execute([$oldEmail]);
        $user = $stmt->fetch();

        // Same generic error whether the email doesn't exist or the password
        // is wrong — never tell an attacker which one it was.
        if (!$user || !password_verify($password, $user['password'])) {
            $errors[] = "Invalid email or password.";
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            header("Location: " . ($user['role'] === 'admin' ? "admin/index.php" : "index.php"));
            exit();
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<link rel="stylesheet" href="css/login.css" />
<?php require_once __DIR__ . '/includes/navbar.php'; ?>

<div class="auth-wrapper">
    <div class="auth-card">
        <h2>Customer Login</h2>

        <?php foreach ($errors as $error): ?>
            <div class="auth-error"><?php echo e($error); ?></div>
        <?php endforeach; ?>

        <form id="loginForm" method="post" action="login.php">
            <label for="loginEmail">Email</label>
            <input type="email" id="loginEmail" name="email" placeholder="Enter Email"
                   value="<?php echo e($oldEmail); ?>" />

            <label for="loginPassword">Password</label>
            <input type="password" id="loginPassword" name="password" placeholder="Enter Password" />

            <button type="submit">Login</button>
        </form>

        <div class="auth-switch">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>