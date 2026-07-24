<?php
// admin/edit_product.php — Edit an existing product (with optional image replace)
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$pageTitle = "Edit Products";
$errors = [];
$success = false;

$categories = getAllCategories($pdo);

// Which product is being edited (via ?id=) — null means show the list only
$editId = isset($_GET['id']) ? (int) $_GET['id'] : null;
$editingProduct = $editId ? getProductById($pdo, $editId) : null;

if ($editId && !$editingProduct) {
    header("Location: edit_product.php");
    exit();
}

// Handle update submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $productId = (int) $_POST['product_id'];
    $brand = trim($_POST['brand'] ?? '');
    $model = trim($_POST['model'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $stock = trim($_POST['stock'] ?? '');
    $categoryId = (int) ($_POST['category_id'] ?? 0);
    $discount=($_POST['discount'] ?? '');
    $finalprice='';

    if ($brand === '') $errors[] = "Please enter a brand.";
    if ($model === '') $errors[] = "Please enter a model.";
    if (!is_numeric($price) || $price < 0) $errors[] = "Please enter a valid price.";
    if (!is_numeric($stock) || $stock < 0) $errors[] = "Please enter a valid stock quantity.";
    if ($categoryId <= 0) $errors[] = "Please select a category.";

    if ($discount > 0){
         $discount= ($price * $discount)/100;
    $finalprice = $price - $discount;
    }
    $currentProduct = getProductById($pdo, $productId);
    if (!$currentProduct) {
        $errors[] = "Product not found.";
    }

    $imageName = $currentProduct['image'] ?? null;

    // Only process a new image if one was actually uploaded
    if (empty($errors) && isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $fileType = mime_content_type($_FILES['image']['tmp_name']);

        if (!in_array($fileType, $allowedTypes, true)) {
            $errors[] = "Image must be a JPG, PNG, or WEBP file.";
        } elseif ($_FILES['image']['size'] > 5 * 1024 * 1024) {
            $errors[] = "Image must be smaller than 5MB.";
        } else {
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $newImageName = uniqid('product_', true) . '.' . $extension;
            $destination = __DIR__ . '/../uploads/mobiles/' . $newImageName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                // Delete the old image file now that the new one is saved
                $oldPath = __DIR__ . '/../uploads/mobiles/' . $currentProduct['image'];
                if ($currentProduct['image'] && file_exists($oldPath)) {
                    unlink($oldPath);
                }
                $imageName = $newImageName;
            } else {
                $errors[] = "Failed to upload new image. Please try again.";
            }
        }
    }

    if (empty($errors)) {
        $updateStmt = $pdo->prepare(
            "UPDATE products SET brand = ?, model = ?, price = ?, description = ?, stock = ?, image = ?, category_id = ?
             WHERE id = ?"
        );
        $updateStmt->execute([$brand, $model, $price, $description, $stock, $imageName, $categoryId, $productId]);

        $success = true;
        $editingProduct = getProductById($pdo, $productId);
    }
}

// List of all products for the table view
$allProducts = getProducts($pdo);
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
        <h1>Edit Products</h1>

        <?php if ($success): ?>
            <p style="color:green;font-weight:bold;">Product updated successfully.</p>
        <?php endif; ?>

        <?php foreach ($errors as $error): ?>
            <p style="color:#cc0000;font-weight:bold;"><?php echo e($error); ?></p>
        <?php endforeach; ?>

        <?php if ($editingProduct): ?>
            <div class="admin-form" style="margin-bottom:30px;">
                <h3 style="margin-bottom:15px;">Editing: <?php echo e($editingProduct['brand'] . ' ' . $editingProduct['model']); ?></h3>

                <form method="post" action="edit_product.php" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" value="<?php echo (int) $editingProduct['id']; ?>" />

                    <label for="brand">Brand</label>
                    <input type="text" id="brand" name="brand" value="<?php echo e($editingProduct['brand']); ?>" />

                    <label for="model">Model</label>
                    <input type="text" id="model" name="model" value="<?php echo e($editingProduct['model']); ?>" />

                    <label for="price">Price ($)</label>
                    <input type="number" step="0.01" id="price" name="price" value="<?php echo e($editingProduct['price']); ?>" />

                    <label for="stock">Stock Quantity</label>
                    <input type="number" id="stock" name="stock" value="<?php echo e($editingProduct['stock']); ?>" />

                     <label for="discount">Discount</label>
                    <input type="number" step="0.01" id="discount" name="discount" value="<?php echo e($editingProduct['discount']); ?>" />

                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id">
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo (int) $category['id']; ?>"
                                <?php echo ($category['id'] == $editingProduct['category_id']) ? 'selected' : ''; ?>>
                                <?php echo e($category['category_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4"><?php echo e($editingProduct['description']); ?></textarea>

                    <label>Current Image</label>
                    <img src="../uploads/mobiles/<?php echo e($editingProduct['image']); ?>"
                         style="width:100px;height:100px;object-fit:cover;border-radius:8px;margin-bottom:15px;display:block;" />

                    <label for="image">Replace Image (optional)</label>
                    <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp" />

                    <button type="submit" name="update_product" style="margin-top:15px;">Save Changes</button>
                    <a href="edit_product.php" style="margin-left:10px;">Cancel</a>
                </form>
            </div>
        <?php endif; ?>

        <h2 style="margin-bottom:15px;">All Products</h2>

        <?php if (empty($allProducts)): ?>
            <p>No products yet. <a href="add_product.php">Add one</a>.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Brand</th>
                        <th>Model</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allProducts as $product): ?>
                        <tr>
                            <td><img src="../uploads/mobiles/<?php echo e($product['image']); ?>" alt="" /></td>
                            <td><?php echo e($product['brand']); ?></td>
                            <td><?php echo e($product['model']); ?></td>
                            <td><?php echo formatPrice($product['price']); ?></td>
                            <td><?php echo (int) $product['stock']; ?></td>
                            <td><?php echo $product['discount']; ?></td>
                            <td class="action-btns">
                                <a href="edit_product.php?id=<?php echo (int) $product['id']; ?>" class="btn-edit">Edit</a>
                                <a href="delete_product.php?id=<?php echo (int) $product['id']; ?>" class="btn-delete"
                                   onclick="return confirm('Delete this product permanently?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

</body>
</html>