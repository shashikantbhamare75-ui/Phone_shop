<?php
session_start();

require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';



$userId = $_SESSION['user_id'];

$orders = getUserOrders($pdo, $userId);

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="orders-container">

<?php if (!empty($orders)): ?>

    <?php foreach ($orders as $order): ?>

        <div class="order-card">
            <p><strong>Order ID:</strong> <?= e($order['id']) ?></p>
            <p><strong>Date:</strong> <?= e($order['order_date']) ?></p>
            <p><strong>Total Price:</strong> <?= formatPrice($order['total_price']) ?></p>
            <p><strong>Status:</strong> <?= e($order['status']) ?></p>

            <a href="order_detail.php?id=<?= e($order['id']) ?>">
                View Details
            </a>
        </div>

    <?php endforeach; ?>

<?php else: ?>

    <p>You haven't placed any orders yet.</p>

<?php endif; ?>

</div>