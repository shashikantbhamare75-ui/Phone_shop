<?php
// about.php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "About Us";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<section>
    <h2>About Phone Shop</h2>
    <p><strong>Shop Name:</strong> Phone Shop</p>
    <p><strong>Owner:</strong> Shashikant Bhamare</p>
    <p><strong>Address:</strong> Bhopal, India</p>
    <p><strong>Contact:</strong> +91 9876543210</p>
    <p><strong>Email:</strong> info@phoneshop.com</p>
    <p>
        We've been helping customers find the right smartphone at the right
        price since day one. Browse our latest collection of phones from
        all major brands, backed by fast delivery and friendly support.
    </p>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>