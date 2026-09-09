<?php
// 데이터베이스 설정
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'badminton_scoreboard');

// SQLite를 사용하는 경우 (닷홈 권장)
define('USE_SQLITE', true);
define('DB_FILE', __DIR__ . '/data/scoreboard.db');

// 디렉토리 생성
if (!is_dir(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0755, true);
}

// SQLite 연결
if (USE_SQLITE) {
    try {
        $pdo = new PDO('sqlite:' . DB_FILE);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("데이터베이스 연결 오류: " . $e->getMessage());
    }
} else {
    // MySQL 연결
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASS,
            array(PDO::MYSQL_ATTR_CHARSET => "utf8")
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("데이터베이스 연결 오류: " . $e->getMessage());
    }
}

// 세션 시작
session_start();

function initialize_database() {
    global $pdo;
    
    if (USE_SQLITE) {
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
        
        // 기본 관리자 계정 생성
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        if ($stmt->fetchColumn() == 0) {
            $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)")
                ->execute(['admin', password_hash('admin123', PASSWORD_DEFAULT), 'admin']);
            $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)")
                ->execute(['viewer1', password_hash('viewer123', PASSWORD_DEFAULT), 'viewer']);
        }
    }
}

// 초기화
initialize_database();
?>
