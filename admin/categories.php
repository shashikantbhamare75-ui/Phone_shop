<?php
// admin/categories.php — Add/delete product categories
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$pageTitle = "Categories";
$errors = [];
$success = false;

// Add new category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $categoryName = trim($_POST['category_name'] ?? '');

    if ($categoryName === '') {
        $errors[] = "Please enter a category name.";
    } else {
        $checkStmt = $pdo->prepare("SELECT id FROM categories WHERE category_name = ?");
        $checkStmt->execute([$categoryName]);
        if ($checkStmt->fetch()) {
            $errors[] = "This category already exists.";
        } else {
            $insertStmt = $pdo->prepare("INSERT INTO categories (category_name) VALUES (?)");
            $insertStmt->execute([$categoryName]);
            $success = true;
        }
    }
}

// Delete category
if (isset($_GET['delete'])) {
    $deleteId = (int) $_GET['delete'];
    $deleteStmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $deleteStmt->execute([$deleteId]);
    header("Location: categories.php");
    exit();
}

$categories = getAllCategories($pdo);
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
        <h1>Categories</h1>

        <?php if ($success): ?>
            <p style="color:green;font-weight:bold;">Category added successfully.</p>
        <?php endif; ?>

        <?php foreach ($errors as $error): ?>
            <p style="color:#cc0000;font-weight:bold;"><?php echo e($error); ?></p>
        <?php endforeach; ?>

        <div class="admin-form" style="margin-bottom:30px;">
            <h3 style="margin-bottom:15px;">Add New Category</h3>
            <form method="post" action="categories.php" style="display:flex;gap:10px;align-items:flex-end;">
                <div style="flex:1;">
                    <label for="categoryName">Category Name</label>
                    <input type="text" id="categoryName" name="category_name" placeholder="e.g. Apple" />
                </div>
                <button type="submit" name="add_category" style="width:auto;">Add Category</button>
            </form>
        </div>

        <?php if (empty($categories)): ?>
            <p>No categories yet.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?php echo (int) $category['id']; ?></td>
                            <td><?php echo e($category['category_name']); ?></td>
                            <td class="action-btns">
                                <a href="categories.php?delete=<?php echo (int) $category['id']; ?>"
                                   class="btn-delete"
                                   onclick="return confirm('Delete this category? Products in it will become uncategorized.');">
                                    Delete
                                </a>
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