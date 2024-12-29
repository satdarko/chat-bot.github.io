<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

$student_id = $_SESSION['user']['id'];
$stmt = $conn->prepare("SELECT full_name, faculty, u_group, email FROM users WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Отримуємо курси студента
$stmt = $conn->prepare("SELECT * FROM courses WHERE id_user = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$courses = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $faculty = $_POST['faculty'];
    $u_group = $_POST['u_group'];
    $email = $_POST['email'];
    
    $course1 = $_POST['course1'];
    $course2 = $_POST['course2'];
    $course3 = $_POST['course3'];
    $course4 = $_POST['course4'];
    $course5 = $_POST['course5'];
    $course6 = $_POST['course6'];
    $course7 = $_POST['course7'];

    $stmt = $conn->prepare("UPDATE users SET full_name = ?, faculty = ?, u_group = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $full_name, $faculty, $u_group, $email, $student_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("REPLACE INTO courses (id_user, course1, course2, course3, course4, course5, course6, course7) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssss", $student_id, $course1, $course2, $course3, $course4, $course5, $course6, $course7);
    $stmt->execute();
    $stmt->close();

    $_SESSION['user']['full_name'] = $full_name;
    $_SESSION['user']['faculty'] = $faculty;
    $_SESSION['user']['u_group'] = $u_group;
    $_SESSION['user']['email'] = $email;
    $_SESSION['success'] = "Акаунт успішно оновлено!";
    
    header('Location: student_cabinet.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редагування профілю студента</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Редагування акаунту студента</h1>
        <a type="submit", href="update_password.php">Змінити пороль</a>
        <form action="update_student_profile.php" method="post">
            <label for="full_name">ПІБ</label>
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($student_data['full_name'] ?? ''); ?>" required>
            
            <label for="email">Пошта</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($student_data['email'] ?? ''); ?>" required>
            
            <label for="faculty">Факультет</label>
            <input type="text" name="faculty" value="<?php echo htmlspecialchars($student_data['faculty'] ?? ''); ?>" required>
            
            <label for="u_group">Група</label>
            <input type="text" name="u_group" value="<?php echo htmlspecialchars($student_data['u_group'] ?? ''); ?>" required>

            <h2>Курси</h2>
            <?php
            $available_courses = [
                "Веб-розробка",
                "Алгоритми",
                "Бази даних",
                "Комп'ютерні мережі",
                "Операційні системи",
                "Машинне навчання",
                "Кібербезпека"
            ];
            for ($i = 1; $i <= 7; $i++):
            ?>
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
        <a href="student_cabinet.php">Повернутись назад</a>
    </div>
</body>
</html>
