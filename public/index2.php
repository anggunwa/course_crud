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
    $taskId = $_POST["id"] ?? "";

    if ($title && $status) {
        if ($taskId) {
            // UPDATE
            $stmt = $conn->prepare("UPDATE tasks SET status = :status, title = :title WHERE id = :id");
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":title", $title);
            $stmt->bindParam(":id", $taskId);

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

// Load posts if ?edit=ID is present
if(isset($_GET["edit"])) {
    $id = $_GET["edit"];
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = :id");
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $taskToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}

if(isset($_GET["delete"])) {
    $id = $_GET["delete"];

    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = :id");
    $stmt->bindParam(":id", $id);

    if ($stmt->execute()) {
        $success = "Task deleted successfully";
    } else {
        $error = "Failed to delete the task";
    }
}

// Fetch all tasks
$stmt = $conn->query("SELECT * FROM tasks ORDER BY FIELD(status, 'pending', 'done'), created_at DESC");
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
    <h1><?= $taskToEdit ? "✏️ Edit the task" : "📋 Add a new task:"?></h1>
    <hr>
    <?php if (!empty($success)): ?>
        <p style="color:green"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <p style="color:red"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <?php if ($taskToEdit): ?>
            <input type="hidden" name="id" value="<?= $taskToEdit['id'] ?>">
        <?php endif; ?>
        <p><?= $taskToEdit ? "Your task name:" : "Input your task:" ?> </p>
        <input name="title" placeholder="Task name" value="<?= $taskToEdit['title'] ?? '' ?>"><br>
        <p>Select the task status:</p>
        <input type="radio" id="pending" name="status" value="pending"
            <?= ($taskToEdit && $taskToEdit['status'] === "pending") ? "checked" : "" ?> required>
        <label for="pending">Pending</label><br>
        <input type="radio" id="done" name="status" value="done"
            <?= ($taskToEdit && $taskToEdit['status'] == "done") ? "checked" : "" ?> required>
        <label for="done">Done</label><br><br>
        <button type="submit"><?= $taskToEdit ? "Edit Task" : "Add Task" ?></button>
    </form>

    <h2>All tasks:</h2>
    <ul>
        <?php foreach($tasks as $task): ?>
            <li>
                [<?= htmlspecialchars($task["status"]) ?>] - <?= htmlspecialchars($task["title"]) ?>
                [<a href="?edit=<?= htmlspecialchars($task['id']) ?>">EDIT</a>] [<a href="?delete=<?= htmlspecialchars($task['id']) ?>" onclick="return confirm('Are you sure you want to delete this task?')">DELETE</a>]
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>