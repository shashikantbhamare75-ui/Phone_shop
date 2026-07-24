<?php
// cart.php — View and manage cart items
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

$pageTitle = "My Cart";

$stmt = $pdo->prepare(
    "SELECT cart.id AS cart_id, cart.quantity, products.id AS product_id,
            products.brand, products.model, products.price, products.image, products.stock
     FROM cart
     JOIN products ON cart.product_id = products.id
     WHERE cart.user_id = ?
     ORDER BY cart.id DESC"
);
$stmt->execute([$_SESSION['user_id']]);
$cartItems = $stmt->fetchAll();

$cartTotal = 0;
foreach ($cartItems as $item) {
    $cartTotal += $item['price'] * $item['quantity'];
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<section>
    <h2>My Cart</h2>

    <div class="cart-wrapper">
        <?php if (empty($cartItems)): ?>
            <p>Your cart is empty. <a href="products.php">Browse products</a>.</p>
        <?php else: ?>
            <table id="cartTable" style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="text-align:left;border-bottom:2px solid #eee;">
                        <th style="padding:10px;">Product</th>
                        <th style="padding:10px;">Price</th>
                        <th style="padding:10px;">Quantity</th>
                        <th style="padding:10px;">Subtotal</th>
                        <th style="padding:10px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr data-cart-id="<?php echo (int) $item['cart_id']; ?>" style="border-bottom:1px solid #eee;">
                            <td style="padding:10px;display:flex;align-items:center;gap:10px;">
                                <img src="uploads/mobiles/<?php echo e($item['image']); ?>"
                                     alt="" style="width:50px;height:50px;object-fit:cover;border-radius:8px;" />
                                <?php echo e($item['brand'] . ' ' . $item['model']); ?>
                            </td>
                            <td style="padding:10px;"><?php echo formatPrice($item['price']); ?></td>
                            <td style="padding:10px;">
                                <input type="number" class="qty-input" min="1" max="<?php echo (int) $item['stock']; ?>"
                                       value="<?php echo (int) $item['quantity']; ?>" style="width:70px;margin:0;" />
                            </td>
                            <td style="padding:10px;" class="row-subtotal">
                                <?php echo formatPrice($item['price'] * $item['quantity']); ?>
                            </td>
                            <td style="padding:10px;">
                                <button type="button" class="remove-btn" style="background:#d9534f;">Remove</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div style="text-align:right;margin-top:20px;font-size:20px;font-weight:bold;">
                Total: <span id="cartTotal"><?php echo formatPrice($cartTotal); ?></span>
            </div>

            <div style="text-align:right;margin-top:15px;">
                <a href="checkout.php"><button type="button">Proceed to Checkout</button></a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>