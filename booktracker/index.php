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