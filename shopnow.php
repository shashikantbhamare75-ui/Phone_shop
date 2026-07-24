<?php
session_start();
require_once __DIR__ .'/includes/session.php';
require_once __DIR__ .'/includes/functions.php';


$categories = getAllCategories($pdo);

$selectedCategory = isset($_GET['category']) ? (int) $_GET['category'] : null;
$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : null;

$products = getProducts($pdo, $selectedCategory, $searchTerm);


if(isset($_POST['shopnow'])){
    $stmt = $pdo->prepare("SELECT * FROM products WHERE discount = ?");
$stmt->execute([30]);

$products = $stmt->fetchAll();
}
require_once __DIR__ .'/includes/header.php';
require_once __DIR__ .'/includes/navbar.php';
?>
<section class="product-banner" style="background:none;margin-bottom:0px;box-shadow:none;">
    <h1>Offer</h1>
    <p>Choose Your Favorite Smartphone</p>
</section>

<section style="background:none;margin-top: 0px;box-shadow:none;">
    <form method="get" action="products.php" style="display:flex;gap:15px;flex-wrap:wrap;align-items:center;justify-content:center;margin-bottom:20px;">
        <select name="category" onchange="this.form.submit()" style="width:auto;margin:0;">
            <option value="">All Categories</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo (int) $category['id']; ?>"
                    <?php echo ($selectedCategory === (int) $category['id']) ? 'selected' : ''; ?>>
                    <?php echo e($category['category_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <input type="text" name="search" placeholder="Search brand or model"
               value="<?php echo e($searchTerm ?? ''); ?>" style="width:220px;margin:0;" />

        <button type="submit" style="width:auto;">Filter</button>

        <?php if ($selectedCategory || $searchTerm): ?>
            <a href="products.php" style="text-decoration:none;">
                <button type="button" style="width:auto;background:#888;">Clear</button>
            </a>
        <?php endif; ?>
    </form>
</section>

<section class="products" style="background:none;box-shadow:none;padding:0;">
    <?php if (empty($products)): ?>
        <p style="grid-column:1/-1;text-align:center;">No products found matching your search.</p>
    <?php else: ?>
        <?php foreach ($products as $product): ?>
            <div class="card">
                <img src="uploads/mobiles/<?php echo e($product['image']); ?>"
                     alt="<?php echo e($product['brand'] . ' ' . $product['model']); ?>" />
                <h3><?php echo e($product['brand'] . ' ' . $product['model']); ?></h3>
                <p><?php if($product['discount']>0){
                    echo formatPrice($product['display']);
                }else{
                    echo formatPrice($product['price']);
                } ?></p>
                <p style="font-size:13px;font-weight:normal;color:<?php echo $product['stock'] > 0 ? 'green' : 'red'; ?>;">
                    <?php echo $product['stock'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
                </p>
                <a href="product_details.php?id=<?php echo (int) $product['id']; ?>" style="text-decoration:none;">
                    <button type="button">View Details</button>
                </a>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<?php 
require_once __DIR__ .'/includes/footer.php';
?>