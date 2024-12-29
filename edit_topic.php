<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

if (isset($_GET['id']) && isset($_SESSION['user'])) {
    $topic_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM memory WHERE id = ? AND id_admin = ?");
    $stmt->bind_param("ii", $topic_id, $_SESSION['user']['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $topic = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $topic_id = $_POST['id'];
    $topic_name = $_POST['topic'];
    $text = $_POST['text'];

    $stmt = $conn->prepare("UPDATE memory SET topic = ?, text = ? WHERE id = ? AND id_admin = ?");
    $stmt->bind_param("ssii", $topic_name, $text, $topic_id, $_SESSION['user']['id']);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Тему успішно оновлено!";
    } else {
        $_SESSION['success'] = "Помилка: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
    header('Location: admin_cabinet.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редагування теми</title>
    <link rel="stylesheet" href="styles.css">
    
    <script>
        tinymce.init({
            selector: 'textarea',
            plugins: 'lists link image charmap preview anchor',
            toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist outdent indent | removeformat | help',
            menubar: false
        });
    </script>
    <style>
        .container {
            width: 80%;
            margin: auto;
            max-width: 1000px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Редагувати тему</h1>
        <form action="edit_topic.php" method="post">
            <input type="hidden" name="id" value="<?php echo $topic['id']; ?>">
            <input type="text" name="topic" value="<?php echo htmlspecialchars($topic['topic']); ?>" required>
            <textarea name="text" required><?php echo htmlspecialchars($topic['text']); ?></textarea>
            <button type="submit">Оновити</button>
        </form>
        <a href="admin_cabinet.php" class="btn">Скасувати</a>
    </div>
</body>
</html>
