<?php
session_start();
include 'db.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user_id'];
$username = $_SESSION['username'];
$fullname = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : $username; 
$role = $_SESSION['role'];

$has_new_salary = 0;
if ($role != 'admin') {
    $notif_sql = "SELECT COUNT(*) FROM transactions t 
                  JOIN categories c ON t.cat_id = c.cat_id 
                  WHERE t.employee_name = :fname 
                  AND (c.category_name LIKE '%Salary%' OR c.category_name LIKE '%Payment%')
                  AND t.date_recorded >= NOW() - INTERVAL 1 DAY";
    $stmt_notif = $pdo->prepare($notif_sql);
    $stmt_notif->execute([':fname' => $fullname]); // Ginamit ang fullname
    $has_new_salary = $stmt_notif->fetchColumn();
}

if ($role == 'admin') {
    $summary_sql = "SELECT 
        SUM(CASE WHEN c.type = 'Income' AND c.category_name NOT LIKE '%Salary%' AND c.category_name NOT LIKE '%Payment%' THEN t.amount ELSE 0 END) AS total_inc, 
        SUM(CASE WHEN c.type = 'Expense' OR c.category_name LIKE '%Salary%' OR c.category_name LIKE '%Payment%' THEN t.amount ELSE 0 END) AS total_exp 
        FROM transactions t 
        JOIN categories c ON t.cat_id = c.cat_id";
    $stmt_sum = $pdo->prepare($summary_sql);
    $stmt_sum->execute();
} else {
    $summary_sql = "SELECT 
        SUM(CASE WHEN c.category_name LIKE '%Salary%' OR c.category_name LIKE '%Payment%' THEN t.amount ELSE 0 END) AS total_inc, 
        SUM(CASE WHEN c.type = 'Expense' AND c.category_name NOT LIKE '%Salary%' AND c.category_name NOT LIKE '%Payment%' THEN t.amount ELSE 0 END) AS total_exp 
        FROM transactions t 
        JOIN categories c ON t.cat_id = c.cat_id 
        WHERE t.employee_name = :fname"; // Ginamit ang fullname
    $stmt_sum = $pdo->prepare($summary_sql);
    $stmt_sum->execute([':fname' => $fullname]);
}
$totals = $stmt_sum->fetch();
$income = $totals['total_inc'] ?? 0; 
$expense = $totals['total_exp'] ?? 0; 
$balance = $income - $expense; 

if ($role == 'admin') {
    $table_sql = "SELECT t.trans_id, t.date_recorded, t.employee_name, t.amount, c.category_name, c.type 
                  FROM transactions t JOIN categories c ON t.cat_id = c.cat_id 
                  ORDER BY t.date_recorded DESC LIMIT 10";
    $stmt_table = $pdo->prepare($table_sql);
    $stmt_table->execute();
} else {
    $table_sql = "SELECT t.trans_id, t.date_recorded, t.employee_name, t.amount, c.category_name, c.type 
                  FROM transactions t JOIN categories c ON t.cat_id = c.cat_id 
                  WHERE t.employee_name = :fname ORDER BY t.date_recorded DESC LIMIT 10"; // Ginamit ang fullname
    $stmt_table = $pdo->prepare($table_sql);
    $stmt_table->execute([':fname' => $fullname]);
}
$transactions = $stmt_table->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="dashboard-container">
    <nav class="sidebar">
        <h2>FMS</h2>
        <ul>
            <li><a href="dashboard.php" class="active">Overview <?php if ($has_new_salary > 0): ?><span class="notif-dot"></span><?php endif; ?></a></li>
            <li><a href="add_transaction.php">Add Transaction</a></li>
            <?php if($role == 'admin'): ?>
                <li><a href="manage_user.php">Manage Users</a></li>
            <?php endif; ?>
            <li><a href="reports.php">Monthly Reports</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <header>
            <?php if(isset($_GET['msg'])): ?>
                <div class="notif-banner" style="background: #d4edda; color: #155724; padding: 12px; border-radius: 5px; margin-bottom: 20px; border-left: 5px solid #28a745;">
                    <?php 
                        if($_GET['msg'] == 'deleted') echo "✓ Record successfully deleted!";
                        if($_GET['msg'] == 'added') echo "✓ New transaction recorded!";
                        if($_GET['msg'] == 'user_deleted') echo "✓ User account has been removed.";
                    ?>
                </div>
            <?php endif; ?>

            <?php if ($role != 'admin' && $has_new_salary > 0): ?>
                <div class="salary-alert" style="background: #e1f5fe; border: 1px solid #b3e5fc; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <span style="font-size: 20px;">💰</span>
                    <strong>New Payment Received!</strong> Your salary has been recorded in the system.
                </div>
            <?php endif; ?>

            <h1>Welcome, <?php echo htmlspecialchars($fullname); ?>!</h1>
        </header>

        <section class="stats-grid">
            <div class="card balance">
                <h3><?php echo ($role == 'admin') ? "Total Business Balance" : "My Net Balance"; ?></h3>
                <p class="amount">₱ <?php echo number_format($balance, 2); ?></p>
            </div>
            <div class="card income">
                <h3>Total Income</h3>
                <p class="amount">+ ₱ <?php echo number_format($income, 2); ?></p>
            </div>
            <div class="card expenses">
                <h3>Total Expenses</h3>
                <p class="amount">- ₱ <?php echo number_format($expense, 2); ?></p>
            </div>
        </section>

        <section class="recent-activity">
            <h3>Recent Transactions</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Employee/Recipient</th>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Receipt</th> 
                        <?php if($role == 'admin'): ?>
                        <th>Action</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $row): ?>
                        <?php $is_salary = (stripos($row['category_name'], 'Salary') !== false || stripos($row['category_name'], 'Payment') !== false); ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($row['date_recorded'])); ?></td>
                            <td><strong><?php echo htmlspecialchars($row['employee_name'] ?? 'N/A'); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                            <td class="<?php 
                                if ($role == 'admin') {
                                    echo ($row['type'] == 'Expense' || $is_salary) ? 'text-danger' : 'text-success';
                                } else {
                                    echo ($is_salary) ? 'text-success' : 'text-danger';
                                }
                            ?>">
                                <?php 
                                    $symbol = ($role == 'admin') ? (($row['type'] == 'Expense' || $is_salary) ? '- ' : '+ ') : (($is_salary) ? '+ ' : '- ');
                                    echo $symbol . "₱ " . number_format($row['amount'], 2); 
                                ?>
                            </td>
                            <td>
                                <a href="view_receipt.php?id=<?php echo $row['trans_id']; ?>" style="color: #1abc9c; text-decoration: none; font-weight: bold;">
                                    📄 Receipt
                                </a>
                            </td>
                            <?php if($role == 'admin'): ?>
                            <td>
                                <a href="edit_transaction.php?id=<?php echo $row['trans_id']; ?>" class="btn-edit">Edit</a>
                                <a href="delete_transaction.php?id=<?php echo $row['trans_id']; ?>" class="btn-delete" onclick="return confirm('Sigurado ka?')">Delete</a>
                            </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>

        <?php if($role != 'admin'): ?>
        <section class="danger-zone" style="margin-top: 30px;">
            <div class="danger-box" style="border: 1px solid #fab1a0; padding: 20px; background: #fff5f5; border-radius: 8px;">
                <h4 style="color: #d63031;">Account Settings</h4>
                <p>Do you want to delete your account? All your data will be lost.</p>
                <a href="delete_account.php?id=<?php echo $uid; ?>" class="btn-delete" style="background: #d63031; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none; display: inline-block;" onclick="return confirm('Warning: Are you sure? You can no longer recover your account.')">Delete My Account</a>
            </div>
        </section>
        <?php endif; ?>

    </main>
</div>
</body>
</html>