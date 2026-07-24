<?php
// index.php — Home page
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "Home";

// Show a handful of featured products on the home page
$featuredProducts = getProducts($pdo);
$featuredProducts = array_slice($featuredProducts, 0, 3);
$review_s = getReviews($pdo);
$review_s = array_slice($review_s,0,6);
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<section class="hero-section"style="box-shadow:none;position: relative;width: 80%;border-radius: 15px;">
    <div class="image" style="width:100%; height:100%; object-fit:contain;">
        <img src="images/banner/ec3d35af-2937-42ce-a75e-04cff27cef60.png" alt="no image" />
    </div>
    <div class="container" style="position:absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);text-align: center;color: white;">
        <h1 style="font-size: 60px;margin-bottom: 15px;text-shadow: 2px 2px 8px black;">OUR PHONE SHOP</h1>
        <p style="font-size: 24px;text-shadow: 2px 2px 8px black;">Discover the Latest Smartphones & Premium Accessories – All in One Place.</p>
    </div>
</section>

<section >
    <h2 style="text-align:center;">Featured Products</h2>

    <div class="products">
        <?php if (empty($featuredProducts)): ?>
            <p>No products available yet. Check back soon!</p>
        <?php else: ?>
            <?php foreach ($featuredProducts as $product): ?>
                <div class="card">
                    <img src="uploads/mobiles/<?php echo e($product['image']); ?>"
                         alt="<?php echo e($product['brand'] . ' ' . $product['model']); ?>" />
                    <h3><?php echo e($product['brand'] . ' ' . $product['model']); ?></h3>
                    <p><?php echo formatPrice($product['price']); ?></p>
                    <a href="product_details.php?id=<?php echo (int) $product['id']; ?>" style="text-decoration:none;">
                        <button type="button">View Details</button>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div style="text-align:center;margin-top:20px;">
        <a href="products.php"><button type="button">View All Products</button></a>
    </div>
</section>

<section class="reviews" style="background:none;box-shadow:none;">
    <h2>What Our Customers Say</h2>
    
    <div class="review-container">
        <?php if (empty($review_s)): ?>
            <p>No reviews available yet. Check back soon!</p>
        <?php else: ?>
            <?php foreach ($review_s as $review): ?>
                <div class="review-card">
                    <h3><?php 
    for($i = 1; $i <= $review['rating']; $i++){
        echo "⭐";
    }
  ?> </h3><br>
            <?php $reviewText = $review['review'];
                    $words = explode(' ', $reviewText);
                    if(count($words) > 5){
                    $shortReview = implode(' ', array_slice($words, 0, 5));
                    ?>
    <p class="r">
    
    <span class="short-review">
        <?php echo e($shortReview); ?>...
    </span>

    <span class="full-review" style="display:none;">
        <?php echo e($reviewText); ?>
    </span>

    <a href="javascript:void(0)" class="toggle-review">More</a>

    </p>

<?php
}else{
?>
    <p>"<?php echo e($reviewText); ?>"</p>
<?php
}
?>
            <h4 style="font-size: 20px;">- <?php echo e($review['username']) ?> </h4>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
<form action="shopnow.php" method="post">
<section class="offer">
    <img src="https://cdn-icons-png.flaticon.com/128/14261/14261136.png" alt="" />
    Seasonal Offer
    <p>Get up to 30% OFF on selected smartphones!</p>
    <button type="submit" name="shopnow">Shop Now</button>
</section>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
