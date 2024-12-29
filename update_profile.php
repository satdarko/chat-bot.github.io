<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

$admin_id = $_SESSION['user']['id'];

// Отримуємо профіль викладача
$stmt = $conn->prepare("SELECT full_name, faculty, email FROM admins WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Отримуємо курси викладача
$stmt = $conn->prepare("SELECT * FROM courses WHERE id_admin = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$courses = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $faculty = $_POST['faculty'];
    $email = $_POST['email'];
    
    $course1 = $_POST['course1'];
    $course2 = $_POST['course2'];
    $course3 = $_POST['course3'];
    $course4 = $_POST['course4'];
    $course5 = $_POST['course5'];
    $course6 = $_POST['course6'];
    $course7 = $_POST['course7'];

    // Оновлення профілю викладача
    $stmt = $conn->prepare("UPDATE admins SET full_name = ?, faculty = ?, email = ? WHERE id = ?");
    $stmt->bind_param("sssi", $full_name, $faculty, $email, $admin_id);
    $stmt->execute();
    $stmt->close();

    // Оновлення курсів викладача
    $stmt = $conn->prepare("
        REPLACE INTO courses (id_admin, course1, course2, course3, course4, course5, course6, course7) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)

    ");
    $stmt->bind_param("isssssss", $admin_id, $course1, $course2, $course3, $course4, $course5, $course6, $course7);
    $stmt->execute();
    $stmt->close();

    $_SESSION['user']['full_name'] = $full_name;
    $_SESSION['user']['faculty'] = $faculty;
    $_SESSION['user']['email'] = $email;
    $_SESSION['success'] = "Акаунт успішно оновлено!";
    
    header('Location: admin_cabinet.php');
    exit();
}

// Курси, які можна обрати
$available_courses = [
    "Веб-розробка",
    "Алгоритми",
    "Бази даних",
    "Комп'ютерні мережі",
    "Операційні системи",
    "Машинне навчання",
    "Кібербезпека"
];
?>
<!DOCTYPE html>
<html lang="uk">
    
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кабінет викладача</title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
    .container {
        width: 70%;
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
<style>
        button, a.btn {
            padding: 8px 16px;
            font-size: 14px;
        }
</style>
<body>
    <div class="container">
        <h1>Кабінет викладача</h1>
        <p><strong>ПІБ:</strong> <?php echo htmlspecialchars($admin_data['full_name'] ?? ''); ?></p>
        <p><strong>Факультет:</strong> <?php echo htmlspecialchars($admin_data['faculty'] ?? ''); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($admin_data['email'] ?? ''); ?></p>

        <a type="submit", href="update_password.php">Змінити пороль</a>

        <h1>Редагування акаунту викладача</h1>
        <form action="update_profile.php" method="post">
            <label for="full_name">ПІБ</label>
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($admin_data['full_name'] ?? ''); ?>" required>
            
            <label for="email">Пошта</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($admin_data['email'] ?? ''); ?>" required>
            
            <label for="faculty">Факультет</label>
            <input type="text" name="faculty" value="<?php echo htmlspecialchars($admin_data['faculty'] ?? ''); ?>" required>

            <h2>Курси</h2>
            <?php for ($i = 1; $i <= 7; $i++): ?>
                <label for="course<?php echo $i; ?>">Курс <?php echo $i; ?></label>
                <select name="course<?php echo $i; ?>">
                    <option value="">Оберіть курс</option>
                    <?php foreach ($available_courses as $course): ?>
                        <option value="<?php echo $course; ?>" <?php echo ($courses['course' . $i] ?? '') == $course ? 'selected' : ''; ?>>
                            <?php echo $course; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endfor; ?>
            
            <button type="submit">Оновити</button>
        </form>
        <a href="admin_cabinet.php">Повернутись назад</a>
    </div>
</body>
</html>
