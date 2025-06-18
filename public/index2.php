<?php

require_once __DIR__ . "/../config/Database_t.php";

$db = new Database;
$conn = $db->connect();

$success = "";
$error = "";
$taskToEdit = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST["title"] ?? "";
    $status = $_POST["status"] ?? "";
    $postId = $_POST["id"] ?? "";

    if ($title && $status) {
        if ($postId) {
            // UPDATE
            $stmt = $conn->prepare("UPDATE tasks SET status = :status, title = :title WHERE id = :id");
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":title", $title);
            $stmt->bindParam("id", $id);

            if ($stmt->execute()) {
                $success = "Task updated successfully";
            } else {
                $error = "Failed to update the task";
            }
        } else {
            // INSERT
            $stmt = $conn->prepare("INSERT INTO tasks (title, status) VALUES (:title, :status)");
            $stmt->bindParam(":title", $title);
            $stmt->bindParam(":status", $status);
    
            if ($stmt->execute()) {
                $success = "Task successfully added.";
            } else {
                $error = "Failed to add task.";
            }
        }
    } else {
        $error = "Fill in all fields";
    }
}

// Fetch all tasks
$stmt = $conn->query("SELECT * FROM tasks ORDER BY created_at DESC");
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task List</title>
</head>
<body>
    <h1>📋 Add a new task:</h1>
    <hr>
    <?php if (!empty($success)): ?>
        <p style="color:green"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <p style="color:red"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <p>Input you task: </p>
        <input name="title" placeholder="Task name"><br>
        <p>Select the task status:</p>
        <input type="radio" id="pending" name="status" value="pending">
        <label for="pending">Pending</label><br>
        <input type="radio" id="done" name="status" value="done">
        <label for="done">Done</label><br><br>
        <button type="submit">Add Task</button>
    </form>

    <h2>All task:</h2>
    <ul>
        <?php foreach($tasks as $task): ?>
            <li>
                [<?= $task["status"] ?>] - <?= $task["title"] ?>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>