<?php
session_start();
include 'db.php';

if (isset($_POST['submit'])) {
    $emp_name = $_POST['employee_name'];
    $amount = $_POST['amount'];
    $cat_id = $_POST['cat_id'];
    $desc = $_POST['description']; 
    $date = date('Y-m-d');

    $sql = "INSERT INTO transactions (cat_id, amount, employee_name, date_recorded) 
            VALUES (?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$cat_id, $amount, $emp_name, $date])) {
        header("Location: dashboard.php?msg=added");
        exit();
    } else {
        echo "There was an error saving the transaction.";
    }
}
?>