<?php
// admin/delete_product.php — Deletes a product and its image file
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$productId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($productId > 0) {
    $product = getProductById($pdo, $productId);

    if ($product) {
        // Delete the product row (cart/order_items reference it via foreign keys —
        // cart has ON DELETE CASCADE, order_items does too per our schema, so
        // historical orders will lose their item detail rows if you delete a
        // product that's been ordered before. Consider disabling/hiding instead
        // of deleting products that appear in past orders.)
        $deleteStmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $deleteStmt->execute([$productId]);

        // Remove the image file from disk
        $imagePath = __DIR__ . '/../uploads/mobiles/' . $product['image'];
        if ($product['image'] && file_exists($imagePath)) {
            unlink($imagePath);
        }
    }
}

header("Location: edit_product.php");
exit();