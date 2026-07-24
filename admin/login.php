<?php
// admin/login.php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

if (isLoggedIn() && isAdmin()) {
    header("Location: index.php");
    exit();
}

$pageTitle = "Admin Login";
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

        if (!$user || !password_verify($password, $user['password'])) {
            $errors[] = "Invalid email or password.";
        } elseif ($user['role'] !== 'admin') {
            $errors[] = "This account does not have admin access.";
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];

            header("Location: index.php");
            exit();
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Login - Phone Shop</title>
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/login.css" />
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">
        <h2>Admin Login</h2>

        <?php foreach ($errors as $error): ?>
            <div class="auth-error"><?php echo e($error); ?></div>
        <?php endforeach; ?>

        <form method="post" action="login.php">
            <label for="adminEmail">Email</label>
            <input type="email" id="adminEmail" name="email" placeholder="Enter Admin Email"
                   value="<?php echo e($oldEmail); ?>" />

            <label for="adminPassword">Password</label>
            <input type="password" id="adminPassword" name="password" placeholder="Enter Password" />

            <button type="submit">Login</button>
        </form>

        <div class="auth-switch">
            <a href="../index.php">&larr; Back to Store</a>
        </div>
    </div>
</div>

</body>
</html>