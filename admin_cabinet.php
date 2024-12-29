<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

include 'db_connect.php';
$user = $_SESSION['user'];
$successMessage = isset($_SESSION['success']) ? $_SESSION['success'] : '';
unset($_SESSION['success']);

// Отримуємо записи викладача
$stmt = $conn->prepare("SELECT * FROM memory WHERE id_admin = ?");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$result = $stmt->get_result();
$topics = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
// Отримуємо курси викладача

$stmt = $conn->prepare("SELECT course FROM admins WHERE id = ?");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$course_result = $stmt->get_result()->fetch_assoc();
$stmt->close();
$admin_courses = explode(',', $course_result['course']);

?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Кабінет викладача</title>
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
    <script>
    function toggleText(id) {
        var moreText = document.getElementById("more_" + id);
        var truncatedText = document.getElementById("truncated_" + id);
        var btnText = document.getElementById("btn_" + id);

        if (moreText.style.display === "none") {
            moreText.style.display = "inline";
            truncatedText.style.display = "none";
            btnText.innerHTML = "Сховати";
        } else {
            moreText.style.display = "none";
            truncatedText.style.display = "inline";
            btnText.innerHTML = "Показати повністю";
        }
    }
</script>
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

</head>
<body>
    <div class="container">
        <h1>Вітаємо, <?php echo htmlspecialchars($user['full_name'] ?? $user['login']); ?>!</h1>
        <p><strong>Факультет:</strong> <?php echo htmlspecialchars($user['faculty']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        <?php if (!empty($successMessage)): ?>
            <p id="successMessage" style="color: green;"> <?php echo $successMessage; ?> </p>
        <?php endif; ?>

        <a href="update_profile.php" class="btn">Редагувати акаунт</a>
       <h2>Ваші курси</h2>
                
                
                    <ul>
                        <?php
                        $hasCourses = false;
                        for ($i = 1; $i <= 7; $i++) {
                            if (!empty($courses['course' . $i])) {
                                echo "<li>" . htmlspecialchars($courses['course' . $i]) . "</li>";
                                $hasCourses = true;
                            }
                        }
                        if (!$hasCourses) {
                            echo "<li>Курси відсутні</li>";
                        }
                        ?>
                    </ul>

                
        <h2>Додати нову тему</h2>
        <form action="add_topic.php" method="post">
            <input type="text" name="topic" placeholder="Назва теми" required>
            <textarea name="text" placeholder="Опис теми" required></textarea>
            <button type="submit">Додати</button>
        </form>

 


        <h2>Ваші теми</h2>
        <table>
            <tr>
                <th>Назва </th>
                <th>Опис</th>
                <th>Дії</th>
            </tr>
            <?php foreach ($topics as $topic): ?>
            <tr>
                <td><?php echo htmlspecialchars($topic['topic']); ?></td>
                
                <td>
                    <span class="truncated" id="truncated_<?php echo $topic['id']; ?>">
                        <?php echo substr($topic['text'], 0, 50); ?>
                    </span>
                    <span class="full-text" id="more_<?php echo $topic['id']; ?>" style="display:none;">
                        <?php echo $topic['text']; ?>
                    </span>
                    <a href="javascript:void(0);" id="btn_<?php echo $topic['id']; ?>" onclick="toggleText('<?php echo $topic['id']; ?>')">Показати повністю</a>
                </td>


                <td>
                    <a href="edit_topic.php?id=<?php echo $topic['id']; ?>">Редагувати</a> |
                    <a href="delete_topic.php?id=<?php echo $topic['id']; ?>" onclick="return confirm('Ви впевнені?')">Видалити</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <a href="logout.php" class="btn">Вийти</a>
    </div>
</body>
</html>
