<?php
// includes/navbar.php
// Requires: session.php, functions.php, and $pdo already loaded by the parent page.

$cartCount = 0;
if (isLoggedIn()) {
    $cartCount = getCartCount($pdo, $_SESSION['user_id']);
}
?>
<header>
    <nav>
        <div class="logo">
            <a href="index.php"><img src="images/icons/bb7c1e38-7814-4808-a24d-7456176beca1.png" /></a>
              <h6>Phone Shop</h6>
        </div>

        <div class="welcome">
            Welcome, <span id="userName">
                <?php echo isLoggedIn() ? e($_SESSION['name']) : 'Guest'; ?>
            </span>
        </div>

        <!-- <form class="search" action="products.php" method="get">
            <input type="text" name="search" placeholder="search any brand" />
            <button type="submit">Search</button>
        </form> -->

        <div class="menu-toggle" id="menuToggle">☰</div>

        <div class="menu">
            <ul id="navMenu">
            
                <li><a href="products.php">Products</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact</a></li>
                <?php if (isAdmin()): ?>
                    <li><a href="admin/index.php">Admin</a></li>
                <?php endif; ?>
                <?php if(isLoggedIn() && !isAdmin()){?>
                      <li><a href="review.php?id=<?php echo $_SESSION['user_id'] ?>">Review</a></li>  
                      <li><a href="my_orders.php?id=<?php echo $_SESSION['user_id'] ?>">My_orders</a></li>
                <?php } ?>
            </ul>
        </div>

        <?php if (isLoggedIn()): ?>
            <a href="logout.php" id="logout" style="border-radius:5px;border:none;padding:8px 15px;background-color:blue;color:white;text-decoration:none;"
            onmouseover="this.style.background='#1D4ED8'"
            onmouseout="this.style.background='#2563EB'">
                Logout
            </a>
            
        <?php else: ?>
            <a href="login.php" id="loginNav" style="border-radius:5px;border:none;padding:8px 15px;background:#2563EB;color:white;text-decoration:none;"
            onmouseover="this.style.background='#1D4ED8'"
            onmouseout="this.style.background='#2563EB'">
                Login
            </a>
        <?php endif; ?>

        <div class="icon">
            <a href="cart.php" style="text-decoration:none;">
                <button type="button">Cart Now (<?php echo $cartCount; ?>)</button>
            </a>
        </div>
    </nav>
</header>