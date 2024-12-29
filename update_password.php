<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

$user_id = $_SESSION['user']['id'];
$role = $_SESSION['role'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = "Новий пароль та підтвердження паролю не збігаються.";
        header('Location: update_password.php');
        exit();
    }

    $table = ($role === 'admin') ? 'admins' : 'users';
    $stmt = $conn->prepare("SELECT password FROM $table WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (password_verify($current_password, $result['password'])) {
        $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE $table SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $new_password_hashed, $user_id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['success'] = "Пароль успішно оновлено!";
        header('Location: ' . ($role === 'admin' ? 'admin_cabinet.php' : 'student_cabinet.php'));
        exit();
    } else {
        $_SESSION['error'] = "Невірний поточний пароль.";
        header('Location: update_password.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
<style>
    .container {
        width: 60%;
        margin: auto;
    }
    .truncated {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 200px;
        display: inline-block;
    }
    .full-text {
        display: none;
        white-space: normal;
    }
</style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оновлення паролю</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Оновлення паролю</h1>
        <?php if (isset($_SESSION['error'])): ?>
            <p style="color: red;"> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?> </p>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <p style="color: green;"> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?> </p>
        <?php endif; ?>
        <form action="update_password.php" method="post">
            <label for="current_password">Поточний пароль</label>
            <input type="password" name="current_password" required>
            
            <label for="new_password">Новий пароль</label>
            <input type="password" name="new_password" required>
            
            <label for="confirm_password">Підтвердити новий пароль</label>
            <input type="password" name="confirm_password" required>
            
            <button type="submit">Оновити пароль</button>
        </form>
        <a href="<?php echo $role === 'admin' ? 'admin_cabinet.php' : 'student_cabinet.php'; ?>">Повернутись назад</a>
    </div>
</body>
</html>
