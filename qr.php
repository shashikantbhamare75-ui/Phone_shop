
<?php

session_start();

require_once  __DIR__ .'/includes/session.php';
require_once  __DIR__ .'/includes/functions.php';


$productId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$product = getProductById($pdo, $productId);

$pageTitle="QR";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment'])) {
     $quantity = max(1, (int) ($_POST['quantity'] ?? 1));

    if ($quantity > $product['stock']) {
        $cartError = "Only " . (int) $product['stock'] . " unit(s) left in stock.";
    } else {
        // If this product is already in the user's cart, increase quantity instead of duplicating
        $checkStmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $checkStmt->execute([$_SESSION['user_id'], $product['id']]);
        $existing = $checkStmt->fetch();

        if ($existing) {
            // $newQuantity = $existing['quantity'] + $quantity;
            // $updateStmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            // $updateStmt->execute([$newQuantity, $existing['id']]);
        } else {
            $insertStmt = $pdo->prepare(
                "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)"
            );
            $insertStmt->execute([$_SESSION['user_id'], $product['id'], $quantity]);
        }

        $addedToCart = true;
    }
}
require_once  __DIR__ .'/includes/header.php';
require_once  __DIR__ .'/includes/navbar.php';

?>
<section>
    <div>
        <img src="images/users/" alt="qrimage">
    </div>
    <div>
        <h1>Product-Details</h1>
        
    </div>
</section>