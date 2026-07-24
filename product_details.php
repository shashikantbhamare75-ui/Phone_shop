<?php
// product_details.php — Single product view + "Add to Cart"
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';

$productId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$product = getProductById($pdo, $productId);

if (!$product) {
    header("Location: products.php");
    exit();
}

$pageTitle = $product['brand'] . ' ' . $product['model'];
$addedToCart = false;
$cartError = '';

// Handle "Add to Cart" submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }

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

if(isset($_POST['add_to_cart'])){
    $stmt = $pdo->prepare("SELECT * FROM products WHERE discount = ?");
$stmt->execute([30]);

$products = $stmt->fetchAll();
} 
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<section>
    

    <?php if ($cartError): ?>
        <p style="color:#cc0000;font-weight:bold;"><?php echo e($cartError); ?></p>
    <?php endif; ?>

    <div style="display:flex;gap:30px;flex-wrap:wrap;">
        <img src="uploads/mobiles/<?php echo e($product['image']); ?>"
             alt="<?php echo e($product['brand'] . ' ' . $product['model']); ?>"
             style="width:280px;height:280px;object-fit:cover;border-radius:15px;" />

        <div style="flex:1;min-width:250px;">
            <h2><?php echo e($product['brand'] . ' ' . $product['model']); ?></h2>
            <p style="font-size:24px;font-weight:bold;color:#0080ff;">
                <?php echo formatPrice($product['price']); ?>
            </p>
            <p><?php echo nl2br(e($product['description'])); ?></p>
            <p style="color:<?php echo $product['stock'] > 0 ? 'green' : 'red'; ?>;font-weight:bold;">
                <?php echo $product['stock'] > 0 ? $product['stock'] . ' units in stock' : 'Out of Stock'; ?>
            </p> 
            <p><?php if($product['discount']>0){
                    echo '<h1 style="font-color:red; font-size:20px;">'. formatPrice($product['display']). ' Discount price </h1>';
                }else{
                    echo formatPrice($product['price']);
                } ?></p>

            <?php if ($product['stock'] > 0): ?>
                <form method="post" action="product_details.php?id=<?php echo (int) $product['id']; ?>">
                    <label for="quantity">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" min="1" max="<?php echo (int) $product['stock']; ?>" value="1" />
                    <button type="submit" name="add_to_cart">Add to Cart</button>
                </form>
            <?php else: ?>
                <button type="button" disabled style="background:#ccc;">Out of Stock</button>
            <?php endif; ?>
            
        </div>
    </div>
    <?php if ($addedToCart): ?>
        <!-- <p style="color:green;font-weight:bold;">
            Added to cart! <a href="cart.php">View Cart</a>
        </p> -->
        <?php
        header("location: cart.php");
        exit; 
        ?>

    <?php endif; ?>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>