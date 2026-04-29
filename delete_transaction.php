<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $sql = "DELETE FROM transactions WHERE trans_id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);

        header("Location: dashboard.php?msg=deleted");
        exit();
    } catch (PDOException $e) {
        die("Error deleting record: " . $e->getMessage());
    }
} else {
    header("Location: dashboard.php");
    exit();
}
?>