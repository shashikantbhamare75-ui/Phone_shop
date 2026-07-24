<?php
// admin/manage_orders.php — View all orders, update order status
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$pageTitle = "Manage Orders";
$validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = (int) $_POST['order_id'];
    $newStatus = $_POST['status'] ?? '';

    if (in_array($newStatus, $validStatuses, true)) {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $orderId]);
    }

    header("Location: manage_orders.php");
    exit();
}

// Optional filter by status
$filterStatus = $_GET['status'] ?? '';

$sql = "SELECT orders.id, orders.order_date, orders.total_price, orders.status,
               users.name, users.email
        FROM orders
        JOIN users ON orders.user_id = users.id";
$params = [];

if (in_array($filterStatus, $validStatuses, true)) {
    $sql .= " WHERE orders.status = ?";
    $params[] = $filterStatus;
}

$sql .= " ORDER BY orders.order_date DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

// Which order's items are expanded (via ?view=)
$viewOrderId = isset($_GET['view']) ? (int) $_GET['view'] : null;
$orderItems = [];

if ($viewOrderId) {
    $itemStmt = $pdo->prepare(
        "SELECT order_items.quantity, order_items.price, products.brand, products.model
         FROM order_items
         JOIN products ON order_items.product_id = products.id
         WHERE order_items.order_id = ?"
    );
    $itemStmt->execute([$viewOrderId]);
    $orderItems = $itemStmt->fetchAll();
}
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
        <h1>Manage Orders</h1>

        <form method="get" action="manage_orders.php" style="margin-bottom:20px;">
            <select name="status" onchange="this.form.submit()" style="width:220px;margin:0;">
                <option value="">All Statuses</option>
                <?php foreach ($validStatuses as $status): ?>
                    <option value="<?php echo e($status); ?>" <?php echo $filterStatus === $status ? 'selected' : ''; ?>>
                        <?php echo e(ucfirst($status)); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php if ($viewOrderId && !empty($orderItems)): ?>
            <div class="admin-form" style="margin-bottom:25px;">
                <h3 style="margin-bottom:10px;">Items in Order #<?php echo (int) $viewOrderId; ?></h3>
                <table class="admin-table">
                    <thead>
                        <tr><th>Product</th><th>Qty</th><th>Price Each</th><th>Subtotal</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderItems as $item): ?>
                            <tr>
                                <td><?php echo e($item['brand'] . ' ' . $item['model']); ?></td>
                                <td><?php echo (int) $item['quantity']; ?></td>
                                <td><?php echo formatPrice($item['price']); ?></td>
                                <td><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <a href="manage_orders.php" style="display:inline-block;margin-top:10px;">&larr; Close</a>
            </div>
        <?php endif; ?>

        <?php if (empty($orders)): ?>
            <p>No orders found.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo (int) $order['id']; ?></td>
                            <td><?php echo e($order['name']); ?><br /><small><?php echo e($order['email']); ?></small></td>
                            <td><?php echo e(date('d M Y', strtotime($order['order_date']))); ?></td>
                            <td><?php echo formatPrice($order['total_price']); ?></td>
                            <td>
                                <form method="post" action="manage_orders.php" style="display:flex;gap:5px;">
                                    <input type="hidden" name="order_id" value="<?php echo (int) $order['id']; ?>" />
                                    <select name="status" style="width:auto;margin:0;padding:5px;">
                                        <?php foreach ($validStatuses as $status): ?>
                                            <option value="<?php echo e($status); ?>" <?php echo $order['status'] === $status ? 'selected' : ''; ?>>
                                                <?php echo e(ucfirst($status)); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" name="update_status" style="width:auto;padding:5px 10px;font-size:12px;">Update</button>
                                </form>
                            </td>
                            <td class="action-btns">
                                <a href="manage_orders.php?view=<?php echo (int) $order['id']; ?>" class="btn-edit">View Items</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

</body>
</html>