<?php
session_start();
include 'db.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];

if ($role == 'admin') {
    $cat_sql = "SELECT * FROM categories ORDER BY category_name ASC";
    $stmt_cat = $pdo->query($cat_sql);
} else {
    $cat_sql = "SELECT * FROM categories WHERE type = 'Expense' ORDER BY category_name ASC";
    $stmt_cat = $pdo->query($cat_sql);
}
$categories = $stmt_cat->fetchAll();

if ($role == 'admin') {
    // Si Admin pwedeng pumili ng kahit sino sa dropdown
    $emp_sql = "SELECT fullname FROM users ORDER BY fullname ASC";
    $employees = $pdo->query($emp_sql)->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Transaction - FMS</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="dashboard-container">
    <nav class="sidebar">
        <h2>FMS</h2>
        <ul>
            <li><a href="dashboard.php">Overview</a></li>
            <li><a href="add_transaction.php" class="active">Add Transaction</a></li>
            <li><a href="reports.php">Monthly Reports</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <header>
            <h1>Add Transaction</h1>
            <p><?php echo ($role == 'admin') ? "Record a salary or expenses." : "Record your Expenses."; ?></p>
        </header>

        <section class="card" style="max-width: 500px;">
            <form action="process_transaction.php" method="POST">
                
                <div style="margin-bottom: 15px;">
                    <label>Employee/Recipient Name:</label>
                    <?php if ($role == 'admin'): ?>
                        <select name="employee_name" required style="width:100%; padding:12px; border:2px solid #ddd; border-radius:4px; background: white;">
                            <option value="">-- Select Recipient --</option>
                            <?php foreach ($employees as $emp): ?>
                                <option value="<?php echo htmlspecialchars($emp['fullname']); ?>">
                                    <?php echo htmlspecialchars($emp['fullname']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <input type="text" name="employee_name" value="<?php echo htmlspecialchars($_SESSION['fullname']); ?>" readonly 
                               style="width:100%; padding:12px; border:2px solid #eee; border-radius:4px; background: #f9f9f9; color: #555;">
                        <small style="color: #666;">Locked to your account.</small>
                    <?php endif; ?>
                </div>

                <div style="margin-bottom: 15px;">
                    <label>Amount (₱):</label>
                    <input type="number" step="0.01" name="amount" placeholder="0.00" required style="width:100%; padding:12px; border:2px solid #ddd; border-radius:4px;">
                </div>

                <div style="margin-bottom: 15px;">
                    <label>Category (Type: <?php echo ($role == 'admin') ? "All" : "Expenses Only"; ?>):</label>
                    <select name="cat_id" required style="width:100%; padding:12px; border:2px solid #ddd; border-radius:4px; background: white;">
                        <option value="">-- Choose Category --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['cat_id']; ?>">
                                <?php echo htmlspecialchars($cat['category_name']); ?> (<?php echo $cat['type']; ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="margin-bottom: 15px;">
                    <label>Description:</label>
                    <textarea name="description" rows="3" placeholder="What were the expenses="width:100%; padding:12px; border:2px solid #ddd; border-radius:4px;"></textarea>
                </div>

                <button type="submit" name="submit" style="background:#1abc9c; color:white; border:none; padding:15px; border-radius:4px; cursor:pointer; width:100%; font-weight: bold;">
                    Save Transaction
                </button>
            </form>
        </section>
    </main>
</div>

</body>
</html>