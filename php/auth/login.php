<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Access-Control-Allow-Credentials: true');

//connect to db
require_once "../connect.php";

//get body of request
$data = json_decode(file_get_contents("php://input"), true);

$json_data = file_get_contents('php://input');
$data = json_decode($json_data, true);

$username = $data["username"] ?? '';
$plain_pw = $data["password"] ?? '';

if($username == '' || $plain_pw == ''){
    exit("Error, username or pw are missing"); 
}

//execute SQL
$stmt = $db->prepare("SELECT id, password FROM users WHERE username=?");
$stmt->bind_param("s", $username);

$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

//verify password
if($user && password_verify($plain_pw, $user['password'])){
    //insert token into db
    $user_id = $user["id"];
    $token = bin2hex(random_bytes(32));
    $expires_at = date('Y-m-d H:i:s', time() + 360000);
    $sql_token = "INSERT INTO auth_tokens (user_id, token, expires_at) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE token = VALUES(token), expires_at = VALUES(expires_at)";
    $stmt_token = $db->prepare($sql_token);
    $stmt_token->bind_param("iss", $user_id, $token, $expires_at);
    if ($stmt_token->execute()) {
        $stmt_token->close();
        header("Content-Type: application/json; charset=UTF-8");

        //Dit is deel 4 het terugsturen van de token
        echo json_encode([
            "message" => "Login successful", 
            "token" => $token,
            "expiration" => $expires_at]);
        //Einde deel 4
    } else {
        $stmt_token->close();
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode(["error" => "Could not issue authentication token."]);
    }
}
