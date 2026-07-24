<?php
// admin/add_product.php — Add a new product with image upload
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$pageTitle = "Add Product";
$errors = [];
$success = false;

$categories = getAllCategories($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brand = trim($_POST['brand'] ?? '');
    $model = trim($_POST['model'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $discount=(int) ($_POST['discount'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $stock = trim($_POST['stock'] ?? '');
    $categoryId = (int) ($_POST['category_id'] ?? 0);
    $finalprice=$price;

    if ($brand === '') $errors[] = "Please enter a brand.";
    if ($model === '') $errors[] = "Please enter a model.";
    if (!is_numeric($price) || $price < 0) $errors[] = "Please enter a valid price.";
    if (!is_numeric($stock) || $stock < 0) $errors[] = "Please enter a valid stock quantity.";
    if ($categoryId <= 0) $errors[] = "Please select a category.";
    if (!is_numeric($discount) || $discount < 0 || $discount > 100) {
    $errors[] = "Discount must be between 0 and 100.";
    }
    if ($discount > 0) {
    $discountAmount = ($price * $discount) / 100;
    $finalprice = $price - $discountAmount;
    }
    $imageName = null;

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $fileType = mime_content_type($_FILES['image']['tmp_name']);

        if (!in_array($fileType, $allowedTypes, true)) {
            $errors[] = "Image must be a JPG, PNG, or WEBP file.";
        } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            $errors[] = "Image must be smaller than 5MB.";
        } else {
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $imageName = uniqid('product_', true) . '.' . $extension;
            $destination = __DIR__ . '/../uploads/mobiles/' . $imageName;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                $errors[] = "Failed to upload image. Please try again.";
                $imageName = null;
            }
        }
    } else {
        $errors[] = "Please select a product image.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            "INSERT INTO products (brand, model, price, description, stock, image, category_id,discount, display)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$brand, $model, $price, $description, $stock, $imageName, $categoryId, $discount, $finalprice]);
        $success = true;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo e($pageTitle); ?> - Phone Shop</title>
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/admin.css" />
</head>
<body>

<div class="admin-layout">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="admin-content">
        <h1>Add Product</h1>

        <?php if ($success): ?>
            <p style="color:green;font-weight:bold;">Product added successfully.</p>
        <?php endif; ?>

        <?php foreach ($errors as $error): ?>
            <p style="color:#cc0000;font-weight:bold;"><?php echo e($error); ?></p>
        <?php endforeach; ?>

        <?php if (empty($categories)): ?>
            <p>You need to <a href="categories.php">create a category</a> before adding products.</p>
        <?php else: ?>
            <div class="admin-form">
                <form method="post" action="add_product.php" enctype="multipart/form-data">
                    <label for="brand">Brand</label>
                    <input type="text" id="brand" name="brand" placeholder="e.g. Apple"
                           value="<?php echo isset($_POST['brand']) ? e($_POST['brand']) : ''; ?>" />

                    <label for="model">Model</label>
                    <input type="text" id="model" name="model" placeholder="e.g. iPhone 15 Pro"
                           value="<?php echo isset($_POST['model']) ? e($_POST['model']) : ''; ?>" />

                    <label for="price">Price <?php echo'(&#8377;)'?></label>
                    <input type="number" step="0.01" id="price" name="price" placeholder="e.g. 999.00"
                           value="<?php echo isset($_POST['price']) ? e($_POST['price']) : ''; ?>" />

                    <label for="stock">Stock Quantity</label>
                    <input type="number" id="stock" name="stock" placeholder="e.g. 25"
                           value="<?php echo isset($_POST['stock']) ? e($_POST['stock']) : ''; ?>" />

                    <label for="discount">Discount</label>
                    <input type="number" id="discount" name="discount" placeholder="e.g. 30% "
                           value="<?php echo isset($_POST['discount']) ? e($_POST['discount']) : ''; ?>" />

                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo (int) $category['id']; ?>">
                                <?php echo e($category['category_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4"><?php
                        echo isset($_POST['description']) ? e($_POST['description']) : '';
                    ?></textarea>

                    <label for="image">Product Image</label>
                    <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" />

                    <button type="submit" style="margin-top:15px;">Add Product</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>