<?php
session_start();
include 'db.php';

if (!isset($_GET['id'])) { header("Location: dashboard.php"); exit(); }

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT t.*, c.category_name FROM transactions t JOIN categories c ON t.cat_id = c.cat_id WHERE t.trans_id = ?");
$stmt->execute([$id]);
$t = $stmt->fetch();

if (isset($_POST['update'])) {
    $new_amount = $_POST['amount'];
    $new_name = $_POST['employee_name'];
    
    $update = $pdo->prepare("UPDATE transactions SET amount = ?, employee_name = ? WHERE trans_id = ?");
    if ($update->execute([$new_amount, $new_name, $id])) {
        header("Location: dashboard.php?msg=updated");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Balance - FMS</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="background: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh;">
    <div class="card" style="width: 400px; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
        <h2>Correct Amount</h2>
        <form method="POST">
            <div style="margin-bottom: 15px;">
                <label>Employee Name:</label>
                <input type="text" name="employee_name" value="<?php echo htmlspecialchars($t['employee_name']); ?>" required style="width:100%; padding:10px; margin-top:5px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label>Amount (₱):</label>
                <input type="number" step="0.01" name="amount" value="<?php echo $t['amount']; ?>" required style="width:100%; padding:10px; margin-top:5px;">
                <small style="color: gray;">Baguhin ito para maitama ang iyong Total Balance.</small>
            </div>
            <button type="submit" name="update" style="background: #1abc9c; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; width: 100%;">Update Balance</button>
            <a href="dashboard.php" style="display: block; text-align: center; margin-top: 10px; color: #666; text-decoration: none;">Cancel</a>
        </form>
    </div>
</body>
</html>