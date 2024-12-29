<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user'])) {
    $topic = $_POST['topic'];
    $text = $_POST['text'];
    $admin_id = $_SESSION['user']['id'];

    $stmt = $conn->prepare("INSERT INTO memory (id_admin, topic, text) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $admin_id, $topic, $text);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Тему успішно додано!";
    } else {
        $_SESSION['success'] = "Помилка: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
    header('Location: admin_cabinet.php');
    exit();
}
?>
