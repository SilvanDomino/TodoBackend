<?php
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Headers: Authorization");
header("Access-Control-Allow-Methods: GET");

require_once "../connect.php";
require_once "../auth/check_auth.php";

$result = $db->query("SELECT * FROM todos WHERE user_id = ".$authenticated_user_id);
header("Content-Type: application/json; charset=UTF-8");
echo json_encode($result->fetch_all(MYSQLI_ASSOC));