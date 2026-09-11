<?php
require_once '../config.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$pathParts = explode('/', trim($requestUri, '/'));

// 게임 ID 추출
$gameId = null;
if (isset($pathParts[count($pathParts) - 1]) && is_numeric($pathParts[count($pathParts) - 1])) {
    $gameId = $pathParts[count($pathParts) - 1];
}

// 쿼리 문자열에서 액션 파악
$action = isset($_GET['action']) ? $_GET['action'] : null;
$id = isset($_GET['id']) ? $_GET['id'] : $gameId;

try {
    if ($method === 'GET') {
        // 모든 경기 조회 또는 특정 경기 조회
        if ($id) {
            $stmt = $pdo->prepare('SELECT * FROM games WHERE id = ?');
            $stmt->execute([$id]);
            $game = $stmt->fetch();
            
            if (!$game) {
                http_response_code(404);
                echo json_encode(['error' => '경기를 찾을 수 없습니다']);
            } else {
                echo json_encode($game);
            }
        } else {
            // 모든 경기 조회
            $stmt = $pdo->query('SELECT * FROM games ORDER BY game_number');
            $games = $stmt->fetchAll();
            echo json_encode($games);
        }
    } 
    elseif ($method === 'PUT') {
        // 점수 업데이트 (관리자만)
        $headers = getallheaders();
        $token = null;
        
        if (isset($headers['Authorization'])) {
            if (preg_match('/Bearer\s+(.*)$/i', $headers['Authorization'], $matches)) {
                $token = $matches[1];
            }
        }
        
        $user = verify_token($token);
        
        if (!$user || $user['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => '관리자만 수정할 수 있습니다']);
            exit();
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$id || !isset($input['score1']) || !isset($input['score2'])) {
            http_response_code(400);
            echo json_encode(['error' => '필수 정보가 없습니다']);
            exit();
        }
        
        $status = isset($input['status']) ? $input['status'] : 'in_progress';
        
        $stmt = $pdo->prepare(
            'UPDATE games SET score1 = ?, score2 = ?, status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?'
        );
        $stmt->execute([$input['score1'], $input['score2'], $status, $id]);
        
        echo json_encode(['message' => '점수가 업데이트되었습니다']);
    }
    elseif ($method === 'POST') {
        // 경기 생성 (관리자만)
        $headers = getallheaders();
        $token = null;
        
        if (isset($headers['Authorization'])) {
            if (preg_match('/Bearer\s+(.*)$/i', $headers['Authorization'], $matches)) {
                $token = $matches[1];
            }
        }
        
        $user = verify_token($token);
        
        if (!$user || $user['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => '관리자만 생성할 수 있습니다']);
            exit();
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        $required = ['game_number', 'player1_name', 'player2_name', 'player3_name', 'player4_name', 'referee_name'];
        foreach ($required as $field) {
            if (!isset($input[$field])) {
                http_response_code(400);
                echo json_encode(['error' => $field . '이 필요합니다']);
                exit();
            }
        }
        
        $stmt = $pdo->prepare(
            'INSERT INTO games (game_number, player1_name, player2_name, player3_name, player4_name, referee_name, score1, score2, status) VALUES (?, ?, ?, ?, ?, ?, 0, 0, "scheduled")'
        );
        $stmt->execute([
            $input['game_number'],
            $input['player1_name'],
            $input['player2_name'],
            $input['player3_name'],
            $input['player4_name'],
            $input['referee_name']
        ]);
        
        echo json_encode(['message' => '경기가 생성되었습니다', 'id' => $pdo->lastInsertId()]);
    }
    else {
        http_response_code(405);
        echo json_encode(['error' => '메서드 허용 안됨']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => '데이터베이스 오류: ' . $e->getMessage()]);
}
?>