<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];

$sql = "SELECT user_id, fullname, username, role FROM users";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users - Financial System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="dashboard-container">
    <nav class="sidebar">
        <h2>FMS</h2>
        <ul>
            <li><a href="dashboard.php">Overview</a></li>
            <li><a href="manage_user.php" class="active">Manage Users</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <h1>User Management</h1>
        
        <?php if(isset($_GET['msg']) && $_GET['msg'] == 'user_deleted'): ?>
            <div class="notif-banner" style="background: #d4edda; color: #155724; padding: 10px; margin-bottom: 20px; border-left: 5px solid #28a745;">
                ✓ User successfully deleted!
            </div>
        <?php endif; ?>

        <div class="recent-activity">
            <table>
                <thead>
                    <tr>
                        <th>Full Name</th> <th>Username (Gmail)</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($user['fullname'] ?? 'No Name Set'); ?></strong></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo ucfirst(htmlspecialchars($user['role'])); ?></td>
                        <td>
                            <?php if ($user['user_id'] != $current_user_id): ?>
                                <a href="delete_account.php?id=<?php echo $user['user_id']; ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('Are you sure you want to delete this user? All of their transactions may be affected.')">
                                   Delete
                                </a>
                            <?php else: ?>
                                <span style="color: #7f8c8d; font-style: italic;">You (Admin)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>