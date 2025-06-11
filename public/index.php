<?php

require_once __DIR__ . "/../config/Database.php";

$db = new Database;
$conn = $db->connect();

$stmt = $conn->query("SELECT * FROM posts");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
print_r($posts);

?>