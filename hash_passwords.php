<?php
include 'db_connect.php';
include 'cache_passwords.php';

// Оновлення паролів студентів
$result = $conn->query("SELECT * FROM users");
while ($row = $result->fetch_assoc()) {
    if (strlen($row['password']) < 60) {
        $hashedPassword = password_hash($row['password'], PASSWORD_DEFAULT);
        $conn->query("UPDATE users SET password='$hashedPassword' WHERE id=" . $row['id']);
    }
}

// Оновлення паролів викладачів
$result = $conn->query("SELECT * FROM admins");
while ($row = $result->fetch_assoc()) {
    if (strlen($row['password']) < 60) {
        $hashedPassword = password_hash($row['password'], PASSWORD_DEFAULT);
        $conn->query("UPDATE admins SET password='$hashedPassword' WHERE id=" . $row['id']);
    }
}

echo "Паролі оновлено для всіх користувачів!";
?>
