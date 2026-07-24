<?php
session_start();

require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';

$orderId = $_GET['id'] ?? 0;

$order = getOrderById($pdo, $orderId);

$orderItems = getOrderItems($pdo, $orderId);

if (!$order) {
    die("Order not found.");
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Details</title>

<style>

.container{
    max-width:1000px;
    margin:40px auto;
    padding:20px;
}

.order-box{
    background:#fff;
    border-radius:12px;
    padding:25px;
    box-shadow:0 5px 15px rgba(0,0,0,.15);
}

.order-box h2{
    margin-bottom:25px;
    color:#1E293B;
}

.order-info{
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:15px;
    margin-bottom:30px;
}

.info-card{
    background:#f8f8f8;
    padding:15px;
    border-radius:8px;
}

.info-card h4{
    margin:0;
    color:#777;
    font-size:14px;
}

.info-card p{
    margin-top:8px;
    font-size:18px;
    font-weight:bold;
}

.product{
    display:flex;
    align-items:center;
    gap:20px;
    padding:20px;
    margin-top:15px;
    border:1px solid #ddd;
    border-radius:10px;
}

.product img{
    width:130px;
    height:130px;
    object-fit:contain;
}

.product-details{
    flex:1;
}

.product-details h3{
    margin-bottom:10px;
}

.product-details p{
    margin:6px 0;
}

.back-btn{
    display:inline-block;
    margin-top:25px;
    padding:12px 25px;
    background:#1E293B;
    color:white;
    text-decoration:none;
    border-radius:8px;
}

.back-btn:hover{
    background:#334155;
}

</style>

</head>

<body>

<div class="container">

<div class="order-box">

<h2>Order Details</h2>
<div class="order-info">

<div class="info-card">
<h4>Order ID</h4>
<p><?= e($order['id']) ?></p>
</div>

<div class="info-card">
<h4>Order Date</h4>
<p><?= e($order['order_date']) ?></p>
</div>

<div class="info-card">
<h4>Status</h4>
<p><?= ucfirst(e($order['status'])) ?></p>
</div>

<div class="info-card">
<h4>Total Amount</h4>
<p><?= formatPrice($order['total_price']) ?></p>
</div>

</div>
<h2>Purchased Products</h2>

<?php foreach($orderItems as $item): ?>

<div class="product">

<img src="uploads/mobiles/<?= e($item['image']) ?>">

<div class="product-details">

<h3><?= e($item['brand']) ?> <?= e($item['model']) ?></h3>

<p><strong>Quantity :</strong> <?= e($item['quantity']) ?></p>

<p><strong>Price :</strong> <?= formatPrice($item['price']) ?></p>

<p><strong>Subtotal :</strong>
<?= formatPrice($item['price'] * $item['quantity']) ?>
</p>

</div>

</div>

<?php endforeach; ?>

<a href="my_orders.php" class="back-btn">
← Back to My Orders
</a>

</div>

</div>

</body>
</html>