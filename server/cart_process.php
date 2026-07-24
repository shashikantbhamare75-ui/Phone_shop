<?php
// server/cart_process.php — AJAX endpoint for cart.php
// Always responds with JSON, never HTML.

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json');

function respond($data) {
    echo json_encode($data);
    exit();
}

if (!isLoggedIn()) {
    respond(['success' => false, 'message' => 'You must be logged in.']);
}

$userId = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

/**
 * Recalculates the cart total and item count for this user,
 * used to refresh the page after any change.
 */
function getCartSummary($pdo, $userId) {
    $stmt = $pdo->prepare(
        "SELECT cart.quantity, products.price
         FROM cart
         JOIN products ON cart.product_id = products.id
         WHERE cart.user_id = ?"
    );
    $stmt->execute([$userId]);
    $rows = $stmt->fetchAll();

    $total = 0;
    $count = 0;
    foreach ($rows as $row) {
        $total += $row['price'] * $row['quantity'];
        $count += (int) $row['quantity'];
    }

    return [$total, $count];
}

if ($action === 'update') {
    $cartId = (int) ($_POST['cart_id'] ?? 0);
    $quantity = (int) ($_POST['quantity'] ?? 1);

    if ($cartId <= 0 || $quantity < 1) {
        respond(['success' => false, 'message' => 'Invalid request.']);
    }

    // Confirm this cart row belongs to the logged-in user, and get product/stock info
    $stmt = $pdo->prepare(
        "SELECT cart.id, products.price, products.stock
         FROM cart
         JOIN products ON cart.product_id = products.id
         WHERE cart.id = ? AND cart.user_id = ?"
    );
    $stmt->execute([$cartId, $userId]);
    $row = $stmt->fetch();

    if (!$row) {
        respond(['success' => false, 'message' => 'Cart item not found.']);
    }

    if ($quantity > $row['stock']) {
        respond(['success' => false, 'message' => 'Only ' . $row['stock'] . ' unit(s) available.']);
    }

    $updateStmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $updateStmt->execute([$quantity, $cartId]);

    [$cartTotal, $itemCount] = getCartSummary($pdo, $userId);

    respond([
        'success' => true,
        'rowSubtotal' => formatPrice($row['price'] * $quantity),
        'cartTotal' => formatPrice($cartTotal),
        'itemCount' => $itemCount,
    ]);
}

if ($action === 'remove') {
    $cartId = (int) ($_POST['cart_id'] ?? 0);

    if ($cartId <= 0) {
        respond(['success' => false, 'message' => 'Invalid request.']);
    }

    // Only delete if it belongs to this user
    $deleteStmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $deleteStmt->execute([$cartId, $userId]);

    if ($deleteStmt->rowCount() === 0) {
        respond(['success' => false, 'message' => 'Cart item not found.']);
    }

    [$cartTotal, $itemCount] = getCartSummary($pdo, $userId);

    respond([
        'success' => true,
        'cartTotal' => formatPrice($cartTotal),
        'itemCount' => $itemCount,
    ]);
}

respond(['success' => false, 'message' => 'Unknown action.']);