<?php
include 'db_connect.php';
include 'cache_passwords.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $email = $_POST['email'];
    $u_group = $_POST['u_group'];
    $password = hashPassword($_POST['password']);

    $query = "INSERT INTO users (login, email, password, u_group, role) VALUES ('$login', '$email', '$password', '$u_group', 'student')";
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $u_group = $_POST['u_group'];
    $faculty = $_POST['faculty'];
    $full_name = $_POST['full_name'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Перевірка на унікальність email, логіну та ПІБ
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR login = ? OR full_name = ?");
    $stmt->bind_param("sss", $email, $login, $full_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Користувач з таким email, логіном або ПІБ вже існує.";
        header('Location: student_register.php');
        exit();
    }

    // Додаємо нового студента
    $stmt = $conn->prepare("INSERT INTO users (login, email, password, u_group, faculty, full_name) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $login, $email, $hashed_password, $u_group, $faculty, $full_name);

    if ($conn->query($query) === TRUE) {
        echo "<p style='color: green; text-align: center;'>Студента зареєстровано!</p>";
        header('Refresh: 2; URL=index.php');
        exit();
    } else {
        echo "<p style='color: red; text-align: center;'>Помилка: " . $conn->error . "</p>";
    }
}
?>



<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Реєстрація студента</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Реєстрація студента</h2>
        <form method="POST" action="student_register.php">
            <input type="text" name="login" placeholder="Логін" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="u_group" placeholder="Група" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit">Зареєструватися</button>
        </form>
    </div>
</body>
</html>

