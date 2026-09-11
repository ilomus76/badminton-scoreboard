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
    
    if (strlen($username) < 3) {
        http_response_code(400);
        echo json_encode(['error' => '사용자명은 3자 이상이어야 합니다']);
        exit();
    }
    
    if (strlen($password) < 6) {
        http_response_code(400);
        echo json_encode(['error' => '비밀번호는 6자 이상이어야 합니다']);
        exit();
    }
    
    try {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)');
        $stmt->execute([$username, $hashedPassword, 'viewer']);
        
        $token = create_token([
            'id' => $pdo->lastInsertId(),
            'username' => $username,
            'role' => 'viewer'
        ]);
        
        echo json_encode([
            'message' => '회원가입 성공',
            'token' => $token,
            'user' => [
                'id' => $pdo->lastInsertId(),
                'username' => $username,
                'role' => 'viewer'
            ]
        ]);
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'UNIQUE constraint failed') !== false) {
            http_response_code(400);
            echo json_encode(['error' => '이미 존재하는 사용자명입니다']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => '회원가입 실패']);
        }
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => '메서드 허용 안됨']);
}
?>