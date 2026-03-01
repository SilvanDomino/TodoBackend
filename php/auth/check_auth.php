<?php
//Retrieve the headers from the request
$headers = getallheaders();
$auth_header = $headers['Authorization'] ?? $headers['authorization'] ?? '';
$token = null;
//Retrieve the token from the header
if (preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) {
    $token = $matches[1];
}

//Execute SQL query to see if token exists and is active
$sql_token_check = "SELECT user_id FROM auth_tokens WHERE token =? AND expires_at > NOW()";
$stmt_token = $db->prepare($sql_token_check);
$stmt_token->bind_param("s", $token);
$stmt_token->execute();
$result_token = $stmt_token->get_result();
$auth_data = $result_token->fetch_assoc();
if (!$auth_data) {
    exit("Invalid or expired token. You must be logged in to do that.");
}
$authenticated_user_id = $auth_data['user_id'];
