<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

include 'db_connect.php';

if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message'])) {
    $message = $_POST['message'];
    $user_id = $_SESSION['user']['id'];

    $_SESSION['chat_history'][] = "<div class='user-message'><strong>Ви:</strong> " . htmlspecialchars($message) . "</div>";

    // Розділяємо запит на окремі слова

    // Розділяємо запит на окремі слова
    $words = explode(' ', $message);
    $likeConditions = [];
    $params = [];
    $sql = "SELECT topic, text, 
            SUM(CASE WHEN text LIKE ? THEN 2 ELSE 0 END + 
                CASE WHEN topic LIKE ? THEN 5 ELSE 0 END) as relevance 
            FROM memory 
            WHERE " . implode(' OR ', $likeConditions) . "
            GROUP BY topic, text
            ORDER BY relevance DESC, CHAR_LENGTH(text) ASC 
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    // Формуємо умови LIKE для кожного слова
    foreach ($words as $word) {
        $likeConditions[] = "(text LIKE ? OR topic LIKE ?)";
        $params[] = '%' . $word . '%';
        $params[] = '%' . $word . '%';
    }

    // Об'єднуємо умови через OR
    $sql = "SELECT topic, text, COUNT(*) as relevance 
            FROM memory 
            WHERE " . implode(' OR ', $likeConditions) . "
            GROUP BY topic, text
            ORDER BY relevance DESC, CHAR_LENGTH(text) ASC 
            LIMIT 1";

    $stmt = $conn->prepare($sql);

    // Динамічно прив'язуємо параметри
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['chat_history'][] = "<div class='bot-message'><strong>Ось що мені вдалось знайти по запиту:</strong><br>" . htmlspecialchars($row['text']) . "</div><hr>";
    } else {
        $_SESSION['chat_history'][] = "<div class='bot-message'>На жаль, я не знайшов відповідної інформації.</div><hr>";
    }
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Чат</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .user-message {
            background-color: #dcf8c6;
            padding: 10px;
            border-radius: 10px;
            margin: 5px 0;
            text-align: right;
        }
        .bot-message {
            background-color: #ececec;
            padding: 10px;
            border-radius: 10px;
            margin: 5px 0;
            text-align: left;
        }
        #chat-box {
            max-height: 400px;
            overflow-y: auto;
            padding: 10px;
            width: 70%;
            margin: auto;
        }
        .container {
            width: 80%;
            margin: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Чат-бот</h1>
        <div id="chat-box">
            <div id="messages">
                <?php
                if (isset($_SESSION['chat_history'])) {
                    foreach ($_SESSION['chat_history'] as $message) {
                        echo $message;
                    }
                }
                ?>
            </div>
        </div>
        <form method="post" id="chat-form">
            <input type="text" name="message" placeholder="Введіть запит..." required>
            <button type="submit">Відправити</button>
        </form>
        <a href="student_cabinet.php" class="btn">Перейти в кабінет</a>
        <a href="news_feed.php" class="btn">Перейти на стрічку новин</a>
        <a href="chat.php?reset=1" class="btn">Новий чат</a>
    </div>
</body>
</html>

<?php
if (isset($_GET['reset']) && $_GET['reset'] == 1) {
    unset($_SESSION['chat_history']);
    header('Location: chat.php');
    exit();
}
?>
