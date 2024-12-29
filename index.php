<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизація</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Вхід до системи</h2>
        <form method="POST" action="auth.php">
            <input type="text" name="login" placeholder="Логін" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <select name="role">
                <option value="student">Студент</option>
                <option value="admin">Викладач</option>
            </select>
            <button type="submit">Увійти</button>
        </form>
        <div style="text-align: center; margin-top: 20px;">
            <a href="student_register.php">Реєстрація студента</a> |
            <a href="admin_register.php">Реєстрація викладача</a>
        </div>
    </div>
</body>
</html>
