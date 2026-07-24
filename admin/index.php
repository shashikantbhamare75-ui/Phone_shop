<?php
// admin/index.php — Admin Dashboard
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$pageTitle = "Admin Dashboard";

// Summary stats
$totalProducts = $pdo->query("SELECT COUNT(*) AS c FROM products")->fetch()['c'];
$totalUsers = $pdo->query("SELECT COUNT(*) AS c FROM users WHERE role = 'customer'")->fetch()['c'];
$totalOrders = $pdo->query("SELECT COUNT(*) AS c FROM orders")->fetch()['c'];
$totalRevenue = $pdo->query("SELECT COALESCE(SUM(total_price), 0) AS s FROM orders WHERE status != 'cancelled'")->fetch()['s'];

// Recent orders
$recentOrders = $pdo->query(
    "SELECT orders.id, orders.total_price, orders.status, orders.order_date, users.name
     FROM orders
     JOIN users ON orders.user_id = users.id
     ORDER BY orders.order_date DESC
     LIMIT 8"
)->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo e($pageTitle); ?> - Phone Shop</title>
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/admin.css" />
</head>
<body>

<div class="admin-layout">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="admin-content">
        <h1>Dashboard</h1>

        <div class="stat-cards">
            <div class="stat-card">
                <h3><?php echo (int) $totalProducts; ?></h3>
                <p>Total Products</p>
            </div>
            <div class="stat-card">
                <h3><?php echo (int) $totalUsers; ?></h3>
                <p>Registered Customers</p>
            </div>
            <div class="stat-card">
                <h3><?php echo (int) $totalOrders; ?></h3>
                <p>Total Orders</p>
            </div>
            <div class="stat-card">
                <h3><?php echo formatPrice($totalRevenue); ?></h3>
                <p>Total Revenue</p>
            </div>
        </div>

        <h2 style="margin-bottom:15px;">Recent Orders</h2>

        <?php if (empty($recentOrders)): ?>
            <p>No orders yet.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentOrders as $order): ?>
                        <tr>
                            <td>#<?php echo (int) $order['id']; ?></td>
                            <td><?php echo e($order['name']); ?></td>
                            <td><?php echo e(date('d M Y', strtotime($order['order_date']))); ?></td>
                            <td><?php echo formatPrice($order['total_price']); ?></td>
                            <td><?php echo e(ucfirst($order['status'])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div style="margin-top:15px;">
                <a href="manage_orders.php">View all orders &rarr;</a>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>