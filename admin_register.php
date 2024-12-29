<?php
include 'db_connect.php';
include 'cache_passwords.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $login = $_POST['login'];
    $email = $_POST['email'];
    $password = hashPassword($_POST['password']);
    $faculty = $_POST['faculty'];

    $query = "INSERT INTO admins (login, email, password, faculty, role) VALUES ('$login', '$email', '$password', '$faculty', 'admin')";

    if ($conn->query($query) === TRUE) {
        echo "<p style='color: green; text-align: center;'>Викладача зареєстровано!</p>";
        header('Refresh: 2; URL=index.php');
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
    <title>Реєстрація викладача</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Реєстрація викладача</h2>
        <form method="POST" action="admin_register.php">
            <input type="text" name="login" placeholder="Логін" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <input type="text" name="faculty" placeholder="Факультет" required>
            <button type="submit">Зареєструватися</button>
        </form>
    </div>
</body>
</html>
