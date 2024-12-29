<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

if (isset($_GET['id']) && isset($_SESSION['user'])) {
    $topic_id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM memory WHERE id = ? AND id_admin = ?");
    $stmt->bind_param("ii", $topic_id, $_SESSION['user']['id']);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Тему успішно видалено!";
    } else {
        $_SESSION['success'] = "Помилка: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
    header('Location: admin_cabinet.php');
    exit();
}
?>
