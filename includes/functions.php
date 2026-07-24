<?php
// includes/functions.php
// Shared helper functions used across the project

require_once __DIR__ . '/../database/db.php';

/**
 * Escapes output to prevent XSS when printing user-supplied data in HTML.
 * Use this every time you echo a name, email, message, etc. into a page.
 */
function e($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Fetches all categories for building nav/filter dropdowns.
 */
function getAllCategories($pdo) {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY category_name ASC");
    return $stmt->fetchAll();
}

/**
 * Fetches a single product by id. Returns false if not found.
 */
function getProductById($pdo, $productId) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :productId");
    $stmt->execute([$productId]);
    return $stmt->fetch();
}

function getUserOrders($pdo, $userId)
{
    $stmt = $pdo->prepare("
        SELECT *
        FROM orders
        WHERE user_id = ?
        ORDER BY order_date DESC
    ");

    $stmt->execute([$userId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getOrderById($pdo, $orderId)
{
    $stmt = $pdo->prepare("
        SELECT *
        FROM orders
        WHERE id = ?
    ");

    $stmt->execute([$orderId]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}
function getOrderItems($pdo, $orderId)
{
    $stmt = $pdo->prepare("
        SELECT
            oi.quantity,
            oi.price,
            p.brand,
            p.model,
            p.image
        FROM order_items oi
        INNER JOIN products p
            ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");

    $stmt->execute([$orderId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
function getReviewById($pdo, $reviewId) {
    $stm = $pdo->prepare("SELECT * FROM reviews WHERE id = ?");
    $stm->execute([$reviewId]);
    return $stm->fetch();
}
function getReviews($pdo){
    $sql ="SELECT * FROM reviews where 1=1";
    $params = [];
    $sql .= " ORDER BY id DESC";
    $stm = $pdo->prepare($sql);
    $stm->execute($params);
    return $stm->fetchAll();
    
}
/**
 * Fetches all products, optionally filtered by category or search term.
 */
function getProducts($pdo, $categoryId = null, $search = null) {
    $sql = "SELECT * FROM products WHERE 1=1";
    $params = [];

    if (!empty($categoryId)) {
        $sql .= " AND category_id = ?";
        $params[] = $categoryId;
    }

    if (!empty($search)) {
        $sql .= " AND (brand LIKE ? OR model LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    $sql .= " ORDER BY id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Returns the number of distinct items in the logged-in user's cart,
 * for showing a badge count in the navbar.
 */
function getCartCount($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT COALESCE(SUM(quantity), 0) AS total FROM cart WHERE user_id = ?");
    $stmt->execute([$userId]);
    return (int) $stmt->fetch()['total'];
}

/**
 * Formats a number as currency for display.
 */
function formatPrice($price) {
    return '&#8377;' . number_format((float) $price, 2);
}?>