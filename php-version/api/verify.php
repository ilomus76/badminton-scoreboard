<?php
require_once '../config.php';

header('Content-Type: application/json');

// Authorization 헤더에서 토큰 추출
$headers = getallheaders();
$token = null;

if (isset($headers['Authorization'])) {
    $authHeader = $headers['Authorization'];
    if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
        $token = $matches[1];
    }
}

if (!$token) {
    http_response_code(401);
    echo json_encode(['error' => '토큰이 없습니다']);
    exit();
}

$user = verify_token($token);

if (!$user) {
    http_response_code(403);
    echo json_encode(['error' => '유효하지 않은 토큰']);
    exit();
}

echo json_encode(['user' => $user]);
?>