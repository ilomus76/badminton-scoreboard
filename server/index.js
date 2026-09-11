const express = require('express');
const cors = require('cors');
const sqlite3 = require('sqlite3').verbose();
const jwt = require('jwt-simple');
const bcryptjs = require('bcryptjs');
require('dotenv').config();

const app = express();
const PORT = process.env.PORT || 5000;
const JWT_SECRET = process.env.JWT_SECRET || 'your-secret-key-change-in-production';

// 미들웨어
app.use(cors());
app.use(express.json());

// SQLite 데이터베이스 설정
const db = new sqlite3.Database('./data/scoreboard.db', (err) => {
  if (err) console.error('DB 연결 오류:', err);
  else console.log('SQLite 데이터베이스 연결됨');
});

// 데이터베이스 초기화
const initializeDB = () => {
  db.serialize(() => {
    // 사용자 테이블
    db.run(`
      CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        role TEXT DEFAULT 'viewer',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
      )
    `);

    // 경기 테이블
    db.run(`
      CREATE TABLE IF NOT EXISTS games (
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
      )
    `, (err) => {
      if (err) console.error(err);
    });

    // 기본 계정 생성 (이미 없으면)
    db.get('SELECT COUNT(*) as count FROM users', (err, row) => {
      if (row.count === 0) {
        const adminPassword = bcryptjs.hashSync('admin123', 10);
        const viewerPassword = bcryptjs.hashSync('viewer123', 10);

        db.run(
          'INSERT INTO users (username, password, role) VALUES (?, ?, ?)',
          ['admin', adminPassword, 'admin'],
          () => console.log('관리자 계정 생성됨')
        );

        db.run(
          'INSERT INTO users (username, password, role) VALUES (?, ?, ?)',
          ['viewer1', viewerPassword, 'viewer'],
          () => console.log('관중 계정 생성됨')
        );

        // 샘플 경기 데이터 생성
        const sampleGames = [
          {
            game_number: 1,
            player1_name: '김철수',
            player2_name: '이영희',
            player3_name: '박민준',
            player4_name: '최지현',
            referee_name: '홍길동'
          },
          {
            game_number: 2,
            player1_name: '정다은',
            player2_name: '이준호',
            player3_name: '강승현',
            player4_name: '유미라',
            referee_name: '이상훈'
          },
          {
            game_number: 3,
            player1_name: '우진영',
            player2_name: '신성주',
            player3_name: '곽태영',
            player4_name: '한소희',
            referee_name: '김민석'
          },
          {
            game_number: 4,
            player1_name: '문재인',
            player2_name: '이순신',
            player3_name: '장영실',
            player4_name: '이이',
            referee_name: '세종대왕'
          }
        ];

        sampleGames.forEach((game) => {
          db.run(
            `INSERT INTO games (game_number, player1_name, player2_name, player3_name, player4_name, referee_name, score1, score2, status)
             VALUES (?, ?, ?, ?, ?, ?, 0, 0, 'scheduled')`,
            [game.game_number, game.player1_name, game.player2_name, game.player3_name, game.player4_name, game.referee_name]
          );
        });
      }
    });
  });
};

initializeDB();

// 인증 미들웨어
const authenticateToken = (req, res, next) => {
  const authHeader = req.headers['authorization'];
  const token = authHeader && authHeader.split(' ')[1];

  if (!token) {
    return res.status(401).json({ error: '토큰이 없습니다' });
  }

  try {
    const decoded = jwt.decode(token, JWT_SECRET);
    req.user = decoded;
    next();
  } catch (err) {
    res.status(403).json({ error: '유효하지 않은 토큰' });
  }
};

// ==================== 인증 라우트 ====================

// 회원가입
app.post('/api/auth/register', (req, res) => {
  const { username, password } = req.body;

  if (!username || !password) {
    return res.status(400).json({ error: '사용자명과 비밀번호가 필요합니다' });
  }

  const hashedPassword = bcryptjs.hashSync(password, 10);

  db.run(
    'INSERT INTO users (username, password, role) VALUES (?, ?, ?)',
    [username, hashedPassword, 'viewer'],
    function (err) {
      if (err) {
        if (err.message.includes('UNIQUE constraint failed')) {
          return res.status(400).json({ error: '이미 존재하는 사용자명입니다' });
        }
        return res.status(500).json({ error: '회원가입 실패' });
      }

      const token = jwt.encode(
        { id: this.lastID, username, role: 'viewer' },
        JWT_SECRET
      );
      res.json({ message: '회원가입 성공', token });
    }
  );
});

// 로그인
app.post('/api/auth/login', (req, res) => {
  const { username, password } = req.body;

  if (!username || !password) {
    return res.status(400).json({ error: '사용자명과 비밀번호가 필요합니다' });
  }

  db.get('SELECT * FROM users WHERE username = ?', [username], (err, user) => {
    if (err) {
      return res.status(500).json({ error: '데이터베이스 오류' });
    }

    if (!user) {
      return res.status(401).json({ error: '사용자를 찾을 수 없습니다' });
    }

    const isPasswordValid = bcryptjs.compareSync(password, user.password);

    if (!isPasswordValid) {
      return res.status(401).json({ error: '비밀번호가 잘못되었습니다' });
    }

    const token = jwt.encode(
      { id: user.id, username: user.username, role: user.role },
      JWT_SECRET
    );
    res.json({ token, user: { id: user.id, username: user.username, role: user.role } });
  });
});

// 로그인 확인
app.post('/api/auth/verify', authenticateToken, (req, res) => {
  res.json({ user: req.user });
});

// ==================== 경기 라우트 ====================

// 모든 경기 조회
app.get('/api/games', (req, res) => {
  db.all('SELECT * FROM games ORDER BY game_number', (err, games) => {
    if (err) {
      return res.status(500).json({ error: '데이터베이스 오류' });
    }
    res.json(games);
  });
});

// 특정 경기 조회
app.get('/api/games/:id', (req, res) => {
  const { id } = req.params;

  db.get('SELECT * FROM games WHERE id = ?', [id], (err, game) => {
    if (err) {
      return res.status(500).json({ error: '데이터베이스 오류' });
    }

    if (!game) {
      return res.status(404).json({ error: '경기를 찾을 수 없습니다' });
    }

    res.json(game);
  });
});

// 경기 점수 업데이트 (관리자만)
app.put('/api/games/:id/score', authenticateToken, (req, res) => {
  if (req.user.role !== 'admin') {
    return res.status(403).json({ error: '권한이 없습니다' });
  }

  const { id } = req.params;
  const { score1, score2, status } = req.body;

  db.run(
    'UPDATE games SET score1 = ?, score2 = ?, status = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?',
    [score1, score2, status || 'in_progress', id],
    (err) => {
      if (err) {
        return res.status(500).json({ error: '데이터베이스 오류' });
      }

      res.json({ message: '점수가 업데이트되었습니다' });
    }
  );
});

// 경기 생성 (관리자만)
app.post('/api/games', authenticateToken, (req, res) => {
  if (req.user.role !== 'admin') {
    return res.status(403).json({ error: '권한이 없습니다' });
  }

  const { game_number, player1_name, player2_name, player3_name, player4_name, referee_name } = req.body;

  db.run(
    `INSERT INTO games (game_number, player1_name, player2_name, player3_name, player4_name, referee_name, score1, score2, status)
     VALUES (?, ?, ?, ?, ?, ?, 0, 0, 'scheduled')`,
    [game_number, player1_name, player2_name, player3_name, player4_name, referee_name],
    (err) => {
      if (err) {
        return res.status(500).json({ error: '데이터베이스 오류' });
      }

      res.json({ message: '경기가 생성되었습니다' });
    }
  );
});

// 서버 시작
app.listen(PORT, () => {
  console.log(`서버가 포트 ${PORT}에서 실행 중입니다`);
});
