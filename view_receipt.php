<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$trans_id = $_GET['id'];
$sql = "SELECT t.*, c.category_name, c.type 
        FROM transactions t 
        JOIN categories c ON t.cat_id = c.cat_id 
        WHERE t.trans_id = :tid";
$stmt = $pdo->prepare($sql);
$stmt->execute([':tid' => $trans_id]);
$trans = $stmt->fetch();

if (!$trans) {
    echo "Transaction not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaction Receipt - #<?php echo $trans['trans_id']; ?></title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; background: #f0f0f0; padding: 20px; }
        .receipt-card { 
            background: white; 
            width: 350px; 
            margin: 0 auto; 
            padding: 20px; 
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-top: 8px solid #1abc9c;
        }
        .header { text-align: center; border-bottom: 2px dashed #eee; padding-bottom: 10px; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #2c3e50; }
        .details { line-height: 1.8; margin-bottom: 20px; }
        .details div { display: flex; justify-content: space-between; }
        .amount-box { text-align: center; background: #f9f9f9; padding: 15px; border-radius: 5px; }
        .amount-box h1 { margin: 0; color: #27ae60; font-size: 2rem; }
        .footer { text-align: center; font-size: 0.8rem; color: #7f8c8d; margin-top: 20px; }
        .btn-print { 
            display: block; width: 100%; padding: 10px; background: #34495e; color: white; 
            text-align: center; text-decoration: none; border-radius: 5px; margin-top: 15px;
        }
        @media print { .btn-print { display: none; } body { background: white; padding: 0; } .receipt-card { box-shadow: none; width: 100%; } }
    </style>
</head>
<body>

<div class="receipt-card">
    <div class="header">
        <h2>PAYROLL SYSTEM</h2>
        <p>Official Transaction Receipt</p>
    </div>

    <div class="details">
        <div><span>Reference No:</span> <strong>#<?php echo $trans['trans_id']; ?></strong></div>
        <div><span>Date:</span> <strong><?php echo date('M d, Y h:i A', strtotime($trans['date_recorded'])); ?></strong></div>
        <div><span>Employee:</span> <strong><?php echo htmlspecialchars($trans['employee_name']); ?></strong></div>
        <div><span>Category:</span> <strong><?php echo htmlspecialchars($trans['category_name']); ?></strong></div>
        <div><span>Type:</span> <strong><?php echo $trans['type']; ?></strong></div>
    </div>

    <div class="amount-box">
        <p>TOTAL AMOUNT</p>
        <h1>₱ <?php echo number_format($trans['amount'], 2); ?></h1>
    </div>

    <div class="footer">
        <p>Thank you for using our system!</p>
        <p>This is a computer-generated receipt.</p>
    </div>

    <a href="#" class="btn-print" onclick="window.print()">Print Receipt</a>
    <a href="dashboard.php" class="btn-print" style="background: #bdc3c7;">Back to Dashboard</a>
</div>

</body>
</html>