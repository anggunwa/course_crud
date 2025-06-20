<?php

require_once __DIR__ . "/../config/Database_b.php";

$db = new Database;
$conn = $db->connect();

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST["title"] ?? "";
    $status = $_POST["status"] ?? "";
    
    if ($title && $status) {
        $stmt = $conn->prepare("INSERT INTO books (title, status) VALUES (:title, :status)");
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":status", $status);

        if ($stmt->execute()) {
            $success = "Successfully added a book.";
        } else {
            $error = "Failed adding a book.";
        }
    } else {
        $error = "Please fill all fields.";
    }
}

// Fetch all task
$stmt = $conn->query("SELECT * FROM books ORDER BY FIELD(status, 'reading', 'finished'), created_at DESC");
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book List</title>
</head>
<body>


    <h1>📖 Book List</h1>

    <?php if(!empty($success)): ?>
        <p style="color:green"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>
    <?php if(!empty($error)): ?>
        <p style="color:red"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    
    <form method="POST">
        <h3>Enter the book information:</h3>
        <h4>Book title?</h4>
        <input name="title" placeholder="Title"><br>
        <h4>Book status?</h4>
        <input type="radio" id="reading" name="status" value="reading">
        <label for="reading">Reading</label>
        <input type="radio" id="finished" name="status" value="finished">
        <label for="finished">Finished</label><br><br>
        <button type="submit">Add book</button>
    </form>
    <br>
    <hr>
    <h2>List of books:</h2>
    <ul>
        <?php foreach($books as $book): ?>
            <li>
                <?php if ($book["status"] === "reading"): ?>
                    📕 <?= htmlspecialchars($book['title']) ?> [<?= htmlspecialchars($book['status']) ?>]
                <?php else: ?>
                    📗 <?= htmlspecialchars($book['title']) ?> [<?= htmlspecialchars($book['status']) ?>]
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>

</body>
</html>