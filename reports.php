<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$uid = $_SESSION['user_id'];
$username = $_SESSION['username'];
$role = $_SESSION['role'];


if ($role == 'admin') {
    $reports_sql = "SELECT 
        DATE_FORMAT(t.date_recorded, '%M %Y') AS month_year,
        SUM(CASE WHEN c.type = 'Income' THEN t.amount ELSE 0 END) AS monthly_inc,
        SUM(CASE WHEN c.type = 'Expense' THEN t.amount ELSE 0 END) AS monthly_exp
        FROM transactions t
        JOIN categories c ON t.cat_id = c.cat_id
        GROUP BY month_year
        ORDER BY t.date_recorded DESC";
    $stmt = $pdo->prepare($reports_sql);
    $stmt->execute();
} else {
    $reports_sql = "SELECT 
        DATE_FORMAT(t.date_recorded, '%M %Y') AS month_year,
        SUM(CASE WHEN c.type = 'Income' THEN t.amount ELSE 0 END) AS monthly_inc,
        SUM(CASE WHEN c.type = 'Expense' THEN t.amount ELSE 0 END) AS monthly_exp
        FROM transactions t
        JOIN categories c ON t.cat_id = c.cat_id
        WHERE t.employee_name = :uname
        GROUP BY month_year
        ORDER BY t.date_recorded DESC";
    $stmt = $pdo->prepare($reports_sql);
    $stmt->execute([':uname' => $username]);
}

$monthly_data = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports - Financial System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="dashboard-body"> <div class="dashboard-container">
    <nav class="sidebar">
        <h2>FMS</h2>
        <ul>
            <li><a href="dashboard.php">Overview</a></li>
            <li><a href="add_transaction.php">Add Transaction</a></li>
            <li><a href="reports.php" class="active">Monthly Reports</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main class="main-content">
        <header>
            <h1>Monthly Financial Reports</h1>
            <p><?php echo ($role == 'admin') ? "Company-wide monthly financial summary." : "Summary of your income and expenses per month."; ?></p>
        </header>

        <section class="recent-activity">
            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <thead>
                    <tr style="background-color: #f8f9fa; text-align: left;">
                        <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Month</th>
                        <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Total Income</th>
                        <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Total Expenses</th>
                        <th style="padding: 12px; border-bottom: 2px solid #dee2e6;">Net Savings</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($monthly_data) > 0): ?>
                        <?php foreach ($monthly_data as $row): 
                            $savings = $row['monthly_inc'] - $row['monthly_exp'];
                        ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px;"><strong><?php echo $row['month_year']; ?></strong></td>
                            <td style="padding: 12px; color: #2ecc71;">₱ <?php echo number_format($row['monthly_inc'], 2); ?></td>
                            <td style="padding: 12px; color: #e74c3c;">₱ <?php echo number_format($row['monthly_exp'], 2); ?></td>
                            <td style="padding: 12px; font-weight: bold; color: <?php echo $savings >= 0 ? '#27ae60' : '#e74c3c'; ?>">
                                ₱ <?php echo number_format($savings, 2); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align:center; padding: 20px;">No data found. add transactions first.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>

</body>
</html>