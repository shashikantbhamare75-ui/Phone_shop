<?php
// profile.php — View/edit account info, order history
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

$pageTitle = "My Profile";
$errors = [];
$success = false;

// Load current user info
$userStmt = $pdo->prepare("SELECT name, email, mobile, address FROM users WHERE id = ?");
$userStmt->execute([$_SESSION['user_id']]);
$user = $userStmt->fetch();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($name === '') {
        $errors[] = "Please enter your name.";
    }
    if (!preg_match('/^\d{10}$/', $mobile)) {
        $errors[] = "Mobile number must be exactly 10 digits.";
    }

    if (empty($errors)) {
        $updateStmt = $pdo->prepare(
            "UPDATE users SET name = ?, mobile = ?, address = ? WHERE id = ?"
        );
        $updateStmt->execute([$name, $mobile, $address, $_SESSION['user_id']]);

        // Keep session name in sync with what's shown in the navbar
        $_SESSION['name'] = $name;
        $user['name'] = $name;
        $user['mobile'] = $mobile;
        $user['address'] = $address;

        $success = true;
    }
}

// Load order history
$orderStmt = $pdo->prepare(
    "SELECT id, order_date, total_price, status FROM orders WHERE user_id = ? ORDER BY order_date DESC"
);
$orderStmt->execute([$_SESSION['user_id']]);
$orders = $orderStmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<section>
    <h2>My Profile</h2>

    <?php if (isset($_GET['order_success'])): ?>
        <p style="color:green;font-weight:bold;">Your order has been placed successfully!</p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:green;font-weight:bold;">Profile updated successfully.</p>
    <?php endif; ?>

    <?php foreach ($errors as $error): ?>
        <p style="color:#cc0000;font-weight:bold;"><?php echo e($error); ?></p>
    <?php endforeach; ?>

    <form method="post" action="profile.php">
        <label for="profileName">Full Name:</label>
        <input type="text" id="profileName" name="name" value="<?php echo e($user['name']); ?>" />

        <label>Email:</label>
        <input type="email" value="<?php echo e($user['email']); ?>" disabled style="background:#eee;" />

        <label for="profileMobile">Mobile Number:</label>
        <input type="text" id="profileMobile" name="mobile" value="<?php echo e($user['mobile']); ?>" />

        <label for="profileAddress">Address:</label>
        <textarea id="profileAddress" name="address" rows="3"><?php echo e($user['address']); ?></textarea>

        <button type="submit" name="update_profile">Save Changes</button>
    </form>
</section>

<section>
    <h2>Order History</h2>

    <?php if (empty($orders)): ?>
        <p>You haven't placed any orders yet. <a href="products.php">Start shopping</a>.</p>
    <?php else: ?>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="text-align:left;border-bottom:2px solid #eee;">
                    <th style="padding:10px;">Order #</th>
                    <th style="padding:10px;">Date</th>
                    <th style="padding:10px;">Total</th>
                    <th style="padding:10px;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr style="border-bottom:1px solid #eee;">
                        <td style="padding:10px;">#<?php echo (int) $order['id']; ?></td>
                        <td style="padding:10px;"><?php echo e(date('d M Y, h:i A', strtotime($order['order_date']))); ?></td>
                        <td style="padding:10px;"><?php echo formatPrice($order['total_price']); ?></td>
                        <td style="padding:10px;">
                            <span style="padding:4px 10px;border-radius:6px;font-size:13px;font-weight:bold;color:white;background:<?php
                                echo match ($order['status']) {
                                    'pending' => '#f0a500',
                                    'processing' => '#0080ff',
                                    'shipped' => '#6f42c1',
                                    'delivered' => '#28a745',
                                    'cancelled' => '#d9534f',
                                    default => '#888',
                                };
                            ?>;">
                                <?php echo e(ucfirst($order['status'])); ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>