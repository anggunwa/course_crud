<?php

require_once __DIR__ . "/../config/Database.php";

$db = new Database;
$conn = $db->connect();

$success = "";
$error = "";
$postToEdit = null;

// Handle form submission (insert or update)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST["title"] ?? "";
    $body = $_POST["body"] ?? "";
    $postId = $_POST["id"] ?? "";

    if ($title && $body) {
        // UPDATE
        if ($postId) {
            $stmt = $conn->prepare("UPDATE posts SET title = :title, body = :body WHERE id = :id");
            $stmt->bindParam(":title", $title);
            $stmt->bindParam(":body", $body);
            $stmt->bindParam(":id", $postId);

            if ($stmt->execute()) {
                $success = "Post updated successfully.";
            } else {
                $error = "Failed to update the post";
            }
        } else {
            // INSERT
            $stmt = $conn->prepare("INSERT INTO posts (title, body) VALUES (:title, :body)");
            $stmt->bindParam(":title", $title);
            $stmt->bindParam(":body", $body);

            if ($stmt->execute()) {
                $success = "Successfully added a post.";
            } else {
                $error = "Failed to add post.";
            }
        }
    } else {
        $error = "Please fill in all fields.";
    }
}

//Load post to edit if ?edit=ID is present
if (isset($_GET["edit"])) {
    $id = $_GET["edit"];
    $stmt = $conn->prepare("SELECT * FROM posts WHERE id = :id");
    $stmt->bindParam(":id", $id);
    $stmt->execute();
    $postToEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (isset($_GET["delete"])) {
    $id = $_GET["delete"];

    $stmt = $conn->prepare("DELETE FROM posts WHERE id = :id");
    $stmt->bindParam(":id", $id);

    if ($stmt->execute()) {
        $success = "Post deleted successfully";
    } else {
        $error = "Failed to delete post";
    }
}

// fetch posts
$stmt = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Posts</title>
</head>
<body>
    <h1><?= $postToEdit ? "Edit post" : "Add a new post" ?></h1>
    
    <?php if (!empty($success)): ?>
        <p style="color:green"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <p style="color:red"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <?php if ($postToEdit): ?>
            <input type="hidden" name="id" value="<?= $postToEdit['id'] ?>">
        <?php endif; ?>
        <input name="title" placeholder="Title" value="<?= $postToEdit['title'] ?? '' ?>"><br>
        <textarea name="body" placeholder="Body" rows="5" cols="30"><?= $postToEdit['body'] ?? '' ?></textarea><br>
        <button type="submit"><?= $postToEdit ? "Edit Post" : "Add Post" ?></button>
    </form>

    <h2>All Posts</h2>
    <ul>
    <?php foreach($posts as $post): ?>
        <li>
            <strong><?= htmlspecialchars($post["title"]) ?></strong><br>
            <?= nl2br(htmlspecialchars($post["body"])) ?>
            <a href="?edit=<?= $post['id'] ?>">Edit</a>
            |
            <a href="?delete=<?= $post['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
            <hr>
        </li>
    <?php endforeach; ?>
    </ul>

</body>
</html>