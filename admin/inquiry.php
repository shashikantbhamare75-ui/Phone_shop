<?php
session_start();

require_once __DIR__ .'/../includes/session.php';
require_once __DIR__ .'/../includes/functions.php';

requireAdmin();
$pagetitle="inquiry";


$inquiry=$pdo->query("SELECT id ,name, message ,date FROM contact ORDER BY date DESC")->fetchAll();


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pagetitle); ?> - Phone Shop</title>
      <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/admin.css" />
</head>
<body>

<div class="admin-layout">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="admin-content">
        <h1>Customer Issus</h1>
 <table class="admin-table">
            <thead>
                <tr>
                    <th>id</th>
                    <th>Name</th>
                    <th>Message</th>
                    <th>Date</th>
                    </tr>
            </thead>
            <tbody>
                <?php foreach ($inquiry as $user): ?>
                    <tr>
                        <td><?php echo e($user['id']); ?></td>
                        <td><?php echo e(ucfirst($user['name'])); ?></td>
                        <td><?php echo e($user['message']); ?></td>
                        <td><?php echo e($user['date']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>