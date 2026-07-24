<?php
// contact.php
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';

$pageTitle = "Contact Us";
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '') {
        $errors[] = "Please enter your name.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }
    if (strlen($message) < 10) {
        $errors[] = "Message should be at least 10 characters.";
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare(
            "INSERT INTO contact (name, email, message, date) VALUES (?, ?, ?, NOW())"
        );
        $stmt->execute([$name, $email, $message]);
        $success = true;
       
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<section>
    <h2>Shop Details</h2>
    <p><strong>Shop Name:</strong> Phone Shop</p>
    <p><strong>Owner:</strong> Shashikant Bhamare</p>
    <p><strong>Address:</strong> Bhopal, India</p>
    <p><strong>Contact:</strong> +91 9876543210</p>
    <p><strong>Email:</strong> info@phoneshop.com</p>
</section>

<section>
    <h2>Get In Touch</h2>

    <?php if ($success): ?>
        <p style="color:green;font-weight:bold;">
            Thank you! Your message has been sent successfully.
        </p>
        <?php header("location: contact.php");
        exit;?>
    <?php endif; ?>

    <?php foreach ($errors as $error): ?>
        <p style="color:#cc0000;font-weight:bold;"><?php echo e($error); ?></p>
    <?php endforeach; ?>

    <form id="contactForm" method="post" action="contact.php">
        <label for="contactName">Full Name:</label>
        <input style="background:transparent; border:2px solid #f3d780;box-shadow: 0 6px 18px rgba(243, 215, 128, 0.35);"type="text" id="contactName" name="name" placeholder="Enter Full Name"
               value="<?php echo isset($_POST['name']) ? e($_POST['name']) : ''; ?>" />

        <label for="contactEmail">Email:</label>
        <input style="background:transparent; border:2px solid #f3d780;box-shadow: 0 6px 18px rgba(243, 215, 128, 0.35);"type="email" id="contactEmail" name="email" placeholder="Enter Email"
               value="<?php echo isset($_POST['email']) ? e($_POST['email']) : ''; ?>" />

        <label for="contactMessage">Message:</label>
        <textarea style="background:transparent; border:2px solid #f3d780;box-shadow: 0 6px 18px rgba(243, 215, 128, 0.35);"id="contactMessage" name="message" rows="5" placeholder="Enter your message"><?php
            echo isset($_POST['message']) ? e($_POST['message']) : '';
        ?></textarea>

        <button type="submit" name="submit">Send Message</button>
    </form>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>