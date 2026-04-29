<?php
include 'db.php';

if (isset($_POST['register'])) {
    $user = trim($_POST['username']);
    $pass = $_POST['password'];

    $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

    $check = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $check->execute([$user]);

    if ($check->rowCount() > 0) {
        echo "Username already exists. <a href='register.php'>Try again</a>";
    } else {
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        
        if ($stmt->execute([$user, $hashed_pass])) {
            echo "Account created successfully! <a href='login.php'>Login now</a>";
        } else {
            echo "Something went wrong.";
        }
    }
}
?>