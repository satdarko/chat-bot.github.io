<?php
include 'db_connect.php';
include 'cache_passwords.php';

// Уникаємо повторного session_start()
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($role == 'student') {
        $query = "SELECT * FROM users WHERE login='$login'";
    } else {
        $query = "SELECT * FROM admins WHERE login='$login'";
    }

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            $_SESSION['role'] = $role;
            
            if ($role === 'student') {
                header('Location: student_cabinet.php');
            } else {
                header('Location: admin_cabinet.php');
            }
            exit();
        } else {
            echo "<p style='color: red; text-align: center;'>Невірний пароль!</p>";
            echo "<a href='index.php' style='text-align: center; display: block;'>Повернутись до входу</a>";
        }
    } else {
        echo "<p style='color: red; text-align: center;'>Користувача не знайдено!</p>";
        echo "<a href='index.php' style='text-align: center; display: block;'>Повернутись до входу</a>";
    }
} else {
    header('Location: index.php');
    exit();
}
?>
