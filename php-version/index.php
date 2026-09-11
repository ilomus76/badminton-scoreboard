<?php
require_once 'config.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'login';

if (isset($_SESSION['user']) && $page !== 'logout') {
    $user = $_SESSION['user'];
} else {
    $user = null;
}

if ($page === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🏸 배드민턴 점수판</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --light-bg: #ecf0f1;
            --dark-text: #2c3e50;
            --border-color: #bdc3c7;
            --border-radius: 8px;
            --shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen',
                'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;
            background-color: #f5f5f5;
            color: var(--dark-text);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .login-card {
            background: white;
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }

        .login-card h1 {
            text-align: center;
            margin-bottom: 30px;
            color: var(--primary-color);
            font-size: 2rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark-text);
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 1rem;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: var(--border-radius);
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--secondary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .error-message {
            background-color: #fadbd8;
            color: var(--danger-color);
            padding: 12px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
            border-left: 4px solid var(--danger-color);
            display: none;
        }

        .toggle-text {
            text-align: center;
            margin-top: 20px;
            font-size: 0.95rem;
        }

        .toggle-btn {
            background: none;
            border: none;
            color: var(--secondary-color);
            cursor: pointer;
            font-weight: 600;
            text-decoration: underline;
        }

        .demo-accounts {
            margin-top: 30px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: var(--border-radius);
            border-top: 1px solid var(--light-bg);
        }

        .demo-accounts h3 {
            font-size: 0.9rem;
            margin-bottom: 10px;
            color: var(--secondary-color);
        }

        .demo-accounts p {
            font-size: 0.85rem;
            color: #555;
            margin: 5px 0;
        }

        .header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: var(--border-radius);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .header h1 {
            font-size: 1.8rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .btn-logout {
            background-color: var(--danger-color);
            color: white;
            padding: 8px 16px;
            font-size: 0.9rem;
            width: auto;
        }

        .btn-logout:hover {
            background-color: #c0392b;
        }

        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--border-color);
        }

        .tab-btn {
            background: none;
            border: none;
            padding: 12px 20px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            color: #999;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .tab-btn.active {
            color: var(--secondary-color);
            border-bottom-color: var(--secondary-color);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .game-board {
            background-color: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }

        .game-board h2 {
            margin-bottom: 20px;
            color: var(--primary-color);
        }

        .game-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
            margin-bottom: 20px;
        }

        .game-item {
            background-color: var(--light-bg);
            padding: 15px;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            text-align: center;
        }

        .game-item:hover {
            border-color: var(--secondary-color);
            background-color: #f0f8ff;
        }

        .game-item.active {
            background-color: var(--secondary-color);
            color: white;
            border-color: var(--secondary-color);
        }

        .game-detail {
            background-color: var(--light-bg);
            padding: 20px;
            border-radius: var(--border-radius);
        }

        .game-info {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .team-section {
            background-color: white;
            padding: 15px;
            border-radius: var(--border-radius);
        }

        .team-section h4 {
            color: var(--secondary-color);
            margin-bottom: 10px;
        }

        .player {
            background-color: var(--light-bg);
            padding: 8px;
            margin-bottom: 5px;
            border-radius: 4px;
        }

        .score-display {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            font-size: 3rem;
            font-weight: bold;
            background-color: white;
            padding: 20px;
            border-radius: var(--border-radius);
        }

        .score-display .score {
            min-width: 80px;
            text-align: center;
        }

        .score1 {
            color: var(--secondary-color);
        }

        .score2 {
            color: var(--warning-color);
        }

        .score-controls {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .control-group {
            background-color: white;
            padding: 10px;
            border-radius: var(--border-radius);
        }

        .control-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .button-group {
            display: flex;
            gap: 5px;
        }

        .btn-control {
            flex: 1;
            padding: 8px;
            background-color: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-control:hover {
            background-color: #2980b9;
        }

        .button-group input {
            flex: 2;
            padding: 8px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            font-size: 1rem;
            text-align: center;
        }

        .referee-section {
            background-color: #fff3cd;
            padding: 15px;
            border-radius: var(--border-radius);
            margin-top: 10px;
        }

        .referee-section h4 {
            color: var(--warning-color);
        }

        .status-section {
            margin-top: 10px;
        }

        .status-section select {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
        }

        .scoreboard {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            border-radius: var(--border-radius);
            margin-bottom: 20px;
        }

        .scoreboard-content {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            align-items: center;
        }

        .scoreboard-team {
            text-align: center;
        }

        .scoreboard-team h3 {
            font-size: 1.3rem;
            margin-bottom: 10px;
        }

        .scoreboard-score {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            font-size: 4rem;
            font-weight: bold;
        }

        .scoreboard-score .score {
            min-width: 120px;
            text-align: center;
            background-color: rgba(0, 0, 0, 0.2);
            padding: 20px;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .game-info {
                grid-template-columns: 1fr;
            }

            .header {
                flex-direction: column;
                text-align: center;
            }

            .score-display {
                font-size: 2rem;
            }

            .scoreboard-content {
                grid-template-columns: 1fr;
            }

            .scoreboard-score {
                font-size: 2rem;
            }

            .scoreboard-score .score {
                min-width: 70px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <?php if (!$user): ?>
    <div class="login-container">
        <div class="login-card">
            <h1>🏸 배드민턴 점수판</h1>
            <form id="loginForm">
                <div id="loginError" class="error-message"></div>
                
                <div class="form-group">
                    <label for="username">사용자명</label>
                    <input type="text" id="username" name="username" placeholder="사용자명 입력" required>
                </div>
                <div class="form-group">
                    <label for="password">비밀번호</label>
                    <input type="password" id="password" name="password" placeholder="비밀번호 입력" required>
                </div>
                <button type="submit" class="btn btn-primary" id="submitBtn">로그인</button>
            </form>
            
            <p class="toggle-text">
                계정이 없으신가요? 
                <button type="button" class="toggle-btn" onclick="toggleForm()">회원가입</button>
            </p>

            <div class="demo-accounts">
                <h3>테스트 계정</h3>
                <p>👨‍💼 관리자: admin / admin123</p>
                <p>👥 관중: viewer1 / viewer123</p>
            </div>
        </div>
    </div>

    <script>
        let isLoginMode = true;

        function toggleForm() {
            isLoginMode = !isLoginMode;
            const submitBtn = document.getElementById('submitBtn');
            const toggleBtn = document.querySelector('.toggle-btn');
            
            if (isLoginMode) {
                submitBtn.textContent = '로그인';
                toggleBtn.textContent = '회원가입';
            } else {
                submitBtn.textContent = '회원가입';
                toggleBtn.textContent = '로그인';
            }
        }

        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const endpoint = isLoginMode ? 'api/login.php' : 'api/register.php';

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, password })
                });

                const data = await response.json();

                if (response.ok) {
                    localStorage.setItem('token', data.token);
                    location.reload();
                } else {
                    document.getElementById('loginError').textContent = data.error || '오류가 발생했습니다';
                    document.getElementById('loginError').style.display = 'block';
                }
            } catch (error) {
                document.getElementById('loginError').textContent = '네트워크 오류';
                document.getElementById('loginError').style.display = 'block';
            }
        });
    </script>
    <?php else: ?>
    <div class="container">
        <div class="header">
            <h1>🏸 배드민턴 점수판</h1>
            <div class="user-info">
                <span><?php echo htmlspecialchars($user['username']); ?> (<?php echo $user['role'] === 'admin' ? '관리자' : '관중'; ?>)</span>
                <a href="?page=logout" class="btn btn-logout">로그아웃</a>
            </div>
        </div>

        <div class="tabs">
            <?php if ($user['role'] === 'admin'): ?>
            <button class="tab-btn active" onclick="switchTab('manage')">⚙️ 점수 관리</button>
            <?php endif; ?>
            <button class="tab-btn" onclick="switchTab('view')">📊 점수 조회</button>
        </div>

        <?php if ($user['role'] === 'admin'): ?>
        <div id="manage" class="tab-content active">
            <div class="game-board">
                <h2>경기 점수 관리</h2>
                <div class="game-list" id="gameList"></div>
                <div id="gameDetail" class="game-detail" style="display: none;"></div>
            </div>
        </div>
        <?php endif; ?>

        <div id="view" class="tab-content<?php echo $user['role'] !== 'admin' ? ' active' : ''; ?>">
            <h2>경기 점수 조회</h2>
            <div class="game-list" id="gameListView"></div>
            <div id="scoreboardView"></div>
        </div>
    </div>

    <script>
        const token = localStorage.getItem('token');
        const apiBase = 'api/games.php';
        let selectedGameId = null;

        async function fetchGames() {
            try {
                const response = await fetch(apiBase);
                const games = await response.json();
                return games;
            } catch (error) {
                console.error('게임 로드 오류:', error);
                return [];
            }
        }

        async function displayGames() {
            const games = await fetchGames();
            const gameList = document.getElementById('gameList');
            const gameListView = document.getElementById('gameListView');
            
            let html = '';
            games.forEach(game => {
                html += `
                    <div class="game-item" onclick="selectGame(${game.id})">
                        <div>경기 ${game.game_number}</div>
                        <div>${game.score1} - ${game.score2}</div>
                    </div>
                `;
            });
            
            if (gameList) gameList.innerHTML = html;
            if (gameListView) gameListView.innerHTML = html;
            
            if (games.length > 0 && !selectedGameId) {
                selectGame(games[0].id);
            }
        }

        async function selectGame(gameId) {
            selectedGameId = gameId;
            const response = await fetch(`${apiBase}?id=${gameId}`);
            const game = await response.json();

            if ('<?php echo $user['role']; ?>' === 'admin') {
                displayGameDetail(game);
            } else {
                displayScoreboard(game);
            }
        }

        function displayGameDetail(game) {
            const detail = document.getElementById('gameDetail');
            detail.style.display = 'block';
            detail.innerHTML = `
                <h3>경기 ${game.game_number}</h3>
                <div class="game-info">
                    <div class="team-section">
                        <h4>1팀</h4>
                        <div class="player">${game.player1_name}</div>
                        <div class="player">${game.player2_name}</div>
                    </div>
                    <div class="score-display">
                        <div class="score score1">${game.score1}</div>
                        <div>:</div>
                        <div class="score score2">${game.score2}</div>
                    </div>
                    <div class="team-section">
                        <h4>2팀</h4>
                        <div class="player">${game.player3_name}</div>
                        <div class="player">${game.player4_name}</div>
                    </div>
                </div>
                <div class="score-controls">
                    <div class="control-group">
                        <label>1팀 점수</label>
                        <div class="button-group">
                            <button class="btn-control" onclick="updateScore(${game.id}, ${game.score1 - 1}, ${game.score2})">-</button>
                            <input type="number" value="${game.score1}" readonly>
                            <button class="btn-control" onclick="updateScore(${game.id}, ${game.score1 + 1}, ${game.score2})">+</button>
                        </div>
                    </div>
                    <div class="control-group">
                        <label>2팀 점수</label>
                        <div class="button-group">
                            <button class="btn-control" onclick="updateScore(${game.id}, ${game.score1}, ${game.score2 - 1})">-</button>
                            <input type="number" value="${game.score2}" readonly>
                            <button class="btn-control" onclick="updateScore(${game.id}, ${game.score1}, ${game.score2 + 1})">+</button>
                        </div>
                    </div>
                </div>
                <div class="referee-section">
                    <h4>심판</h4>
                    <p>${game.referee_name}</p>
                </div>
                <div class="status-section">
                    <label>경기 상태</label>
                    <select onchange="updateStatus(${game.id}, this.value, ${game.score1}, ${game.score2})">
                        <option value="scheduled" ${game.status === 'scheduled' ? 'selected' : ''}>예정</option>
                        <option value="in_progress" ${game.status === 'in_progress' ? 'selected' : ''}>진행 중</option>
                        <option value="completed" ${game.status === 'completed' ? 'selected' : ''}>완료</option>
                    </select>
                </div>
            `;
        }

        function displayScoreboard(game) {
            const scoreboard = document.getElementById('scoreboardView');
            scoreboard.innerHTML = `
                <div class="scoreboard">
                    <div class="scoreboard-content">
                        <div class="scoreboard-team">
                            <h3>1팀</h3>
                            <p>${game.player1_name}</p>
                            <p>${game.player2_name}</p>
                        </div>
                        <div class="scoreboard-score">
                            <div class="score">${game.score1}</div>
                            <div>:</div>
                            <div class="score">${game.score2}</div>
                        </div>
                        <div class="scoreboard-team">
                            <h3>2팀</h3>
                            <p>${game.player3_name}</p>
                            <p>${game.player4_name}</p>
                        </div>
                    </div>
                    <div style="margin-top: 20px; text-align: center;">
                        <p>심판: ${game.referee_name}</p>
                    </div>
                </div>
            `;
        }

        async function updateScore(gameId, score1, score2) {
            try {
                const response = await fetch(apiBase + `?id=${gameId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify({ score1: Math.max(0, score1), score2: Math.max(0, score2) })
                });

                if (response.ok) {
                    displayGames();
                    selectGame(gameId);
                }
            } catch (error) {
                alert('점수 업데이트 오류: ' + error);
            }
        }

        async function updateStatus(gameId, status, score1, score2) {
            try {
                const response = await fetch(apiBase + `?id=${gameId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    body: JSON.stringify({ score1, score2, status })
                });

                if (response.ok) {
                    displayGames();
                    selectGame(gameId);
                }
            } catch (error) {
                alert('상태 업데이트 오류: ' + error);
            }
        }

        function switchTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }

        displayGames();
        setInterval(displayGames, 3000);
    </script>
    <?php endif; ?>
</body>
</html>