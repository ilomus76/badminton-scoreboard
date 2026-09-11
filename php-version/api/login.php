<?php
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['username']) || !isset($input['password'])) {
        http_response_code(400);
        echo json_encode(['error' => '사용자명과 비밀번호가 필요합니다']);
        exit();
    }
    
    $username = $input['username'];
    $password = $input['password'];
    
    try {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => '사용자를 찾을 수 없습니다']);
            exit();
        }
        
        if (!password_verify($password, $user['password'])) {
            http_response_code(401);
            echo json_encode(['error' => '비밀번호가 잘못되었습니다']);
            exit();
        }
        
        // 토큰 생성
        $token = create_token([
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
        ]);
        
        echo json_encode([
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ]
        ]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => '데이터베이스 오류']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => '메서드 허용 안됨']);
}
?>