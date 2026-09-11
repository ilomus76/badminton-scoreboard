<?php
// 데이터베이스 설정
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'badminton_scoreboard');

// 데이터 폴더 설정
define('DATA_DIR', __DIR__ . '/data');
define('DB_FILE', DATA_DIR . '/scoreboard.db');

// 디렉토리 생성
if (!is_dir(DATA_DIR)) {
    mkdir(DATA_DIR, 0755, true);
}

// SQLite 연결
try {
    $pdo = new PDO('sqlite:' . DB_FILE);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['error' => '데이터베이스 연결 오류: ' . $e->getMessage()]));
}

// 세션 시작
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 데이터베이스 초기화
function initialize_database() {
    global $pdo;
    
    try {
        // 사용자 테이블
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            role TEXT DEFAULT 'viewer',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // 경기 테이블
        $pdo->exec("CREATE TABLE IF NOT EXISTS games (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            game_number INTEGER NOT NULL,
            player1_name TEXT NOT NULL,
            player2_name TEXT NOT NULL,
            player3_name TEXT,
            player4_name TEXT,
            referee_name TEXT NOT NULL,
            score1 INTEGER DEFAULT 0,
            score2 INTEGER DEFAULT 0,
            status TEXT DEFAULT 'scheduled',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // 기본 계정 확인
        $stmt = $pdo->query('SELECT COUNT(*) FROM users');
        if ($stmt->fetchColumn() == 0) {
            // 관리자 계정
            $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
            $pdo->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)')
                ->execute(['admin', $adminPassword, 'admin']);
            
            // 관중 계정
            $viewerPassword = password_hash('viewer123', PASSWORD_DEFAULT);
            $pdo->prepare('INSERT INTO users (username, password, role) VALUES (?, ?, ?)')
                ->execute(['viewer1', $viewerPassword, 'viewer']);
            
            // 샘플 경기 데이터
            $sampleGames = [
                [
                    'game_number' => 1,
                    'player1_name' => '김철수',
                    'player2_name' => '이영희',
                    'player3_name' => '박민준',
                    'player4_name' => '최지현',
                    'referee_name' => '홍길동'
                ],
                [
                    'game_number' => 2,
                    'player1_name' => '정다은',
                    'player2_name' => '이준호',
                    'player3_name' => '강승현',
                    'player4_name' => '유미라',
                    'referee_name' => '이상훈'
                ],
                [
                    'game_number' => 3,
                    'player1_name' => '우진영',
                    'player2_name' => '신성주',
                    'player3_name' => '곽태영',
                    'player4_name' => '한소희',
                    'referee_name' => '김민석'
                ],
                [
                    'game_number' => 4,
                    'player1_name' => '문재인',
                    'player2_name' => '이순신',
                    'player3_name' => '장영실',
                    'player4_name' => '이이',
                    'referee_name' => '세종대왕'
                ]
            ];
            
            foreach ($sampleGames as $game) {
                $pdo->prepare(
                    'INSERT INTO games (game_number, player1_name, player2_name, player3_name, player4_name, referee_name, score1, score2, status) VALUES (?, ?, ?, ?, ?, ?, 0, 0, "scheduled")'
                )->execute([
                    $game['game_number'],
                    $game['player1_name'],
                    $game['player2_name'],
                    $game['player3_name'],
                    $game['player4_name'],
                    $game['referee_name']
                ]);
            }
        }
    } catch (Exception $e) {
        error_log('DB 초기화 오류: ' . $e->getMessage());
    }
}

// JWT 토큰 생성 (간단한 버전)
function create_token($data) {
    $header = base64_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
    $payload = base64_encode(json_encode($data));
    $signature = base64_encode(hash_hmac('sha256', $header . '.' . $payload, 'secret-key', true));
    return $header . '.' . $payload . '.' . $signature;
}

// JWT 토큰 검증
function verify_token($token) {
    try {
        $parts = explode('.', $token);
        if (count($parts) != 3) return null;
        
        $payload = json_decode(base64_decode($parts[1]), true);
        return $payload;
    } catch (Exception $e) {
        return null;
    }
}

// CORS 헤더 설정
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 데이터베이스 초기화
initialize_database();
?>