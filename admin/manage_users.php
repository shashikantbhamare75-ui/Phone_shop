<?php
// admin/manage_users.php — View customers, promote/demote admin role, delete accounts
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/functions.php';

requireAdmin();

$pageTitle = "Manage Users";
$errors = [];

// Toggle role (admin <-> customer), but never let the current admin demote themselves
if (isset($_GET['toggle_role'])) {
    $targetId = (int) $_GET['toggle_role'];

    if ($targetId === (int) $_SESSION['user_id']) {
        $errors[] = "You cannot change your own admin role.";
    } else {
        $userStmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
        $userStmt->execute([$targetId]);
        $target = $userStmt->fetch();

        if ($target) {
            $newRole = $target['role'] === 'admin' ? 'customer' : 'admin';
            $updateStmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
            $updateStmt->execute([$newRole, $targetId]);
        }
    }
}

// Delete a user, but never the currently logged-in admin
if (isset($_GET['delete'])) {
    $targetId = (int) $_GET['delete'];

    if ($targetId === (int) $_SESSION['user_id']) {
        $errors[] = "You cannot delete your own account while logged in.";
    } else {
        $deleteStmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $deleteStmt->execute([$targetId]);
    }
}

$users = $pdo->query("SELECT id, name, email, mobile, role, created_at FROM users ORDER BY created_at DESC")->fetchAll();
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
        <h1>Manage Users</h1>

        <?php foreach ($errors as $error): ?>
            <p style="color:#cc0000;font-weight:bold;"><?php echo e($error); ?></p>
        <?php endforeach; ?>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Role</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo e($user['name']); ?></td>
                        <td><?php echo e($user['email']); ?></td>
                        <td><?php echo e($user['mobile']); ?></td>
                        <td><?php echo e(ucfirst($user['role'])); ?></td>
                        <td><?php echo e(date('d M Y', strtotime($user['created_at']))); ?></td>
                        <td class="action-btns">
                            <?php if ((int) $user['id'] !== (int) $_SESSION['user_id']): ?>
                                <a href="manage_users.php?toggle_role=<?php echo (int) $user['id']; ?>" class="btn-edit"
                                   onclick="return confirm('Change this user\'s role to <?php echo $user['role'] === 'admin' ? 'customer' : 'admin'; ?>?');">
                                    Make <?php echo $user['role'] === 'admin' ? 'Customer' : 'Admin'; ?>
                                </a>
                                <a href="manage_users.php?delete=<?php echo (int) $user['id']; ?>" class="btn-delete"
                                   onclick="return confirm('Delete this user account permanently?');">
                                    Delete
                                </a>
                            <?php else: ?>
                                <em style="font-size:12px;color:#888;">(You)</em>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>