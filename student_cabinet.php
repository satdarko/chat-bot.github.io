<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'student') {
    header('Location: index.php');
    exit();
}

include 'db_connect.php';
$user = $_SESSION['user'];
$successMessage = isset($_SESSION['success']) ? $_SESSION['success'] : '';
unset($_SESSION['success']);

// Отримуємо курси студента
$stmt = $conn->prepare("SELECT * FROM courses WHERE id_user = ?");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$courses = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кабінет студента</title>
    <link rel="stylesheet" href="styles.css">
    <script>
    window.onload = function() {
        var message = document.getElementById('successMessage');
        if (message) {
            setTimeout(function() {
                message.style.display = 'none';
            }, 5000);
        }
    }
    </script>
</head>
<body>
    <div class="container">
        <h1>Вітаємо, <?php echo htmlspecialchars($user['full_name'] ?? $user['login']); ?>!</h1>
        <p><strong>ПІБ:</strong> <?php echo htmlspecialchars($user['full_name'] ?? 'Не вказано'); ?></p>
        <p><strong>Факультет:</strong> <?php echo htmlspecialchars($user['faculty']); ?></p>
        <p><strong>Група:</strong> <?php echo htmlspecialchars($user['u_group']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        
        <?php if (!empty($successMessage)): ?>
            <p id="successMessage" style="color: green;"> <?php echo $successMessage; ?> </p>
        <?php endif; ?>

        <a href="update_student_profile.php" class="btn">Редагувати акаунт</a>
        <a button type="submit", href="chat.php" class="btn">Перейти в чат</a>
        <h2>Ваші курси</h2>
        <ul>
            <?php
            if ($courses) {
                for ($i = 1; $i <= 7; $i++) {
                    if (!empty($courses['course' . $i])) {
                        echo "<li>" . htmlspecialchars($courses['course' . $i]) . "</li>";
                    }
                }
            } else {
                echo "<li>Курси відсутні</li>";
            }
            ?>
        </ul>

        <a href="logout.php" class="btn">Вийти</a>
    </div>
</body>
</html>
