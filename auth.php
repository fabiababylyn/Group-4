<?php
session_start();
include 'db.php'; 

// --- LOGIC PARA SA LOGIN ---
if (isset($_POST['login'])) {
    $user_input = $_POST['username'];
    $pass_input = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$user_input]);
    $user = $stmt->fetch();

    if ($user && $pass_input == $user['password']) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        
        // Isave ang fullname sa session para sa dashboard welcome message
        $_SESSION['fullname'] = $user['fullname']; 
        
        $_SESSION['role'] = $user['role']; 
        header("Location: dashboard.php");
        exit();
    } else {
        header("Location: login.php?error=invalid");
        exit();
    }
}

if (isset($_POST['register'])) {
    $fullname = trim($_POST['fullname']);
    $user = trim($_POST['new_username']); // Email/Username field
    $pass = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

   
    if (!filter_var($user, FILTER_VALIDATE_EMAIL) || !str_ends_with($user, '@gmail.com')) {
        header("Location: register.php?error=invalid_email");
        exit();
    }

    if (!preg_match("/^[a-zA-Z\s]*$/", $fullname)) {
        header("Location: register.php?error=invalid_name");
        exit();
    }

    if ($confirm !== $pass) {
        header("Location: register.php?error=mismatch");
        exit();
    }

    $check_stmt = $pdo->prepare("SELECT username FROM users WHERE username = ?");
    $check_stmt->execute([$user]);
    
    if ($check_stmt->rowCount() > 0) {
        header("Location: register.php?error=exists");
        exit();
    } else {
        try {
            $sql = "INSERT INTO users (fullname, username, password, role) VALUES (?, ?, ?, 'employee')";
            $stmt = $pdo->prepare($sql);

            if ($stmt->execute([$fullname, $user, $pass])) {
                header("Location: login.php?msg=registered");
                exit();
            }
        } catch (PDOException $e) {
            die("Database Error: Ensure that your table has a 'fullname' column. " . $e->getMessage());
        }
    }
}
?>