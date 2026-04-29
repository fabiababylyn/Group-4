<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Transaction</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Record New Transaction</h2>
    <form action="process_transaction.php" method="POST">
        <label>Amount:</label>
        <input type="number" step="0.01" name="amount" required>

        <label>Category:</label>
        <select name="cat_id" required>
            <option value="1">Salary</option>
            <option value="3">Food & Dining</option>
            <option value="4">Transportation</option>
        </select>

        <label>Date:</label>
        <input type="date" name="date_recorded" value="<?php echo date('Y-m-d'); ?>" required>

        <label>Description:</label>
        <textarea name="description"></textarea>
        <section class="stats-grid">
    <div class="card balance">
        <h3>Total Balance</h3>
        <p class="amount">₱ <?php echo number_format($balance, 2); ?></p>
    </div>
    <div class="card income">
        <h3>Total Income</h3>
        <p class="amount">+ ₱ <?php echo number_format($totals['total_inc'], 2); ?></p>
    </div>
    <div class="card expenses">
        <h3>Total Expenses</h3>
        <p class="amount">- ₱ <?php echo number_format($totals['total_exp'], 2); ?></p>
    </div>
</section>

        <button type="submit" name="submit">Save Transaction</button>
    </form>
</body>
</html>