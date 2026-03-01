<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');
require_once "../connect.php";
$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['text'])) {
    echo "Geen of incorrecte JSON!";
    exit;
}

$text = $data['text'];
$stmt = $db->prepare("INSERT INTO todos (text, status) VALUES (?, ?)");

$status = "todo";
$stmt->bind_param("sss", $text, $status);

if ($stmt->execute()) {
    echo "Success!";
} else {
    echo "Failure!";
}

$stmt->close();
$db->close();
?>