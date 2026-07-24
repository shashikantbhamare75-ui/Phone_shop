<?php
// checkout.php — Review cart, collect delivery/payment info, place order
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';

requireLogin();

$productId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$product = getProductById($pdo, $productId);

$pageTitle = "Checkout";
$errors = [];

// Load cart items for this user
$stmt = $pdo->prepare(
    "SELECT cart.id AS cart_id, cart.quantity, products.id AS product_id,
            products.brand, products.model, products.price, products.stock, products.image
     FROM cart
     JOIN products ON cart.product_id = products.id
     WHERE cart.user_id = ?"
);
$stmt->execute([$_SESSION['user_id']]);
$cartItems = $stmt->fetchAll();

// Nothing to check out
if (empty($cartItems)) {
    header("Location: cart.php");
    exit();
}

$cartTotal = 0;
foreach ($cartItems as $item) {
    $cartTotal += $item['price'] * $item['quantity'];
}

// Pre-fill address fields from the user's profile, if set
$userStmt = $pdo->prepare("SELECT address, mobile FROM users WHERE id = ?");
$userStmt->execute([$_SESSION['user_id']]);
$userInfo = $userStmt->fetch();

$oldAddress = $userInfo['address'] ?? '';
$oldCity = '';
$oldPincode = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldAddress = trim($_POST['address'] ?? '');
    $oldCity = trim($_POST['city'] ?? '');
    $oldPincode = trim($_POST['pincode'] ?? '');
    $paymentMethod = $_POST['payment'] ?? '';

    if ($oldAddress === '') {
        $errors[] = "Please enter your delivery address.";
    }
    if ($oldCity === '') {
        $errors[] = "Please enter your city.";
    }
    if (!preg_match('/^\d{5,6}$/', $oldPincode)) {
        $errors[] = "Please enter a valid pincode.";
    }
    if (!in_array($paymentMethod, ['COD', 'UPI', 'Card'], true)) {
        $errors[] = "Please select a payment method.";
    }
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $payment = $_POST['payment'];

    if ($payment == "UPI") {
        header("Location: qr.php");
        exit();
    }

    if ($payment == "COD") {
     }
}

    // Re-check stock right before placing the order, in case it changed
    if (empty($errors)) {
        foreach ($cartItems as $item) {
            if ($item['quantity'] > $item['stock']) {
                $errors[] = "Sorry, \"" . e($item['brand'] . ' ' . $item['model']) . "\" only has " . $item['stock'] . " unit(s) left.";
            }
        }
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // 1. Create the order
            $orderStmt = $pdo->prepare(
                "INSERT INTO orders (user_id, order_date, total_price, status)
                 VALUES (?, NOW(), ?, 'pending')"
            );
            $orderStmt->execute([$_SESSION['user_id'], $cartTotal]);
            $orderId = $pdo->lastInsertId();

            // 2. Create order_items and decrement stock for each cart item
            $itemStmt = $pdo->prepare(
                "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)"
            );
            $stockStmt = $pdo->prepare(
                "UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?"
            );

            foreach ($cartItems as $item) {
                $itemStmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);

                $stockStmt->execute([$item['quantity'], $item['product_id'], $item['quantity']]);
                if ($stockStmt->rowCount() === 0) {
                    // Stock ran out mid-transaction; abort everything
                    throw new Exception("Stock unavailable for " . $item['brand'] . ' ' . $item['model']);
                }
            }

            // 3. Clear the user's cart
            $clearStmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
            $clearStmt->execute([$_SESSION['user_id']]);

            $pdo->commit();

            header("Location: profile.php?order_success=1");
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Something went wrong placing your order. Please try again.";
        }
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<section>
    <h2>Checkout</h2>

    <?php foreach ($errors as $error): ?>
        <p style="color:#cc0000;font-weight:bold;"><?php echo e($error); ?></p>
    <?php endforeach; ?>

    <h3 style="margin-bottom:10px;">Order Summary</h3>
    <table style="width:100%;border-collapse:collapse;margin-bottom:25px;">
        <?php foreach ($cartItems as $item): ?>
            <tr style="border-bottom:1px solid #eee;">
                <td style="padding:8px;"><?php echo e($item['brand'] . ' ' . $item['model']); ?> &times; <?php echo (int) $item['quantity']; ?></td>
                <td style="padding:8px;text-align:right;"><?php echo formatPrice($item['price'] * $item['quantity']); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td style="padding:8px;font-weight:bold;">Total</td>
            <td style="padding:8px;text-align:right;font-weight:bold;"><?php echo formatPrice($cartTotal); ?></td>
        </tr>
    </table>

    <form id="checkoutForm" method="post" action="checkout.php">
        <label for="checkoutAddress">Delivery Address:</label>
        <textarea id="checkoutAddress" name="address" rows="3"><?php echo e($oldAddress); ?></textarea>

        <label for="checkoutCity">City:</label>
        <input type="text" id="checkoutCity" name="city" placeholder="Enter City" value="<?php echo e($oldCity); ?>" />

        <label for="checkoutPincode">Pincode:</label>
        <input type="text" id="checkoutPincode" name="pincode" placeholder="Enter Pincode" value="<?php echo e($oldPincode); ?>" />

        <label>Payment Method:</label><br />
        <input type="radio" name="payment" value="COD" id="payCod" /> <label for="payCod" style="font-weight:normal;">Cash on Delivery</label><br />
        <input type="radio" name="payment" value="UPI" id="payUpi" /> <label for="payUpi" style="font-weight:normal;">UPI</label><br />
       
        <button type="submit">Place Order</button>
    </form>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>