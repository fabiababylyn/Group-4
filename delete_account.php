<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];
$current_role = $_SESSION['role'];
$target_user_id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$target_user_id) {
    header("Location: dashboard.php");
    exit();
}

if ($current_role == 'admin' || $current_user_id == $target_user_id) {
    
    try {
        $sql = "DELETE FROM users WHERE user_id = ?";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$target_user_id])) {
            
            if ($current_user_id == $target_user_id) {
                session_destroy();
                header("Location: login.php?msg=account_deleted");
            } else {
                $back = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'dashboard.php';
                header("Location: " . $back);
            }
            exit();
        }
    } catch (PDOException $e) {
        header("Location: dashboard.php?error=db_error");
        exit();
    }
} else {
    echo "Access Denied!";
}
?>