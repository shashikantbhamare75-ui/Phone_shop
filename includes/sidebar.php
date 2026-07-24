<?php
// includes/sidebar.php
// Used only inside admin/ pages. Requires session.php + functions.php already loaded,
// and requireAdmin() already called by the parent page.

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar">
    <div class="admin-logo">
        <h2>Phone Shop</h2>
        <p>Admin Panel</p>
    </div>

    <ul class="admin-menu">
        <li class="<?php echo $currentPage === 'index.php' ? 'active' : ''; ?>">
            <a href="index.php">Dashboard</a>
        </li>
        <li class="<?php echo $currentPage === 'add_product.php' ? 'active' : ''; ?>">
            <a href="add_product.php">Add Product</a>
        </li>
        <li class="<?php echo $currentPage === 'edit_product.php' ? 'active' : ''; ?>">
            <a href="edit_product.php">Edit Products</a>
        </li>
        <li class="<?php echo $currentPage === 'categories.php' ? 'active' : ''; ?>">
            <a href="categories.php">Categories</a>
        </li>
        <li class="<?php echo $currentPage === 'manage_orders.php' ? 'active' : ''; ?>">
            <a href="manage_orders.php">Manage Orders</a>
        </li>
        <li class="<?php echo $currentPage === 'manage_users.php' ? 'active' : ''; ?>">
            <a href="manage_users.php">Manage Users</a>
        </li>
        <li class="<?php echo $currentPage === 'inquiry.php' ? 'active' : ''; ?>">
            <a href="inquiry.php">Inquiry</a>
        </li>
    </ul>

    <div class="admin-back">
        <a href="../index.php">&larr; Back to Store</a>
        <a href="../logout.php">Logout</a>
    </div>
</aside>