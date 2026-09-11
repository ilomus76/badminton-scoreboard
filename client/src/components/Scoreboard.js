import React, { useState, useEffect } from 'react';
import axios from 'axios';

const API_URL = 'http://localhost:5000/api';

function Scoreboard({ token }) {
  const [games, setGames] = useState([]);
  const [selectedGameId, setSelectedGameId] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    fetchGames();
    const interval = setInterval(fetchGames, 3000);
    return () => clearInterval(interval);
  }, []);

  const fetchGames = async () => {
    try {
      const response = await axios.get(`${API_URL}/games`);
      setGames(response.data);
      if (!selectedGameId && response.data.length > 0) {
        setSelectedGameId(response.data[0].id);
      }
    } catch (err) {
      setError('경기 데이터를 불러올 수 없습니다');
    } finally {
      setLoading(false);
    }
  };

  const selectedGame = games.find((g) => g.id === selectedGameId);

  if (loading) {
    return <div className="loading">로딩 중...</div>;
  }

  return (
    <div className="scoreboard">
      <h2>경기 점수 조회</h2>
      {error && <div className="error-message">{error}</div>}

      <div className="game-selector">
        <h3>경기 선택</h3>
        <div className="game-buttons">
          {games.map((game) => (
            <button
              key={game.id}
              className={`game-btn ${selectedGameId === game.id ? 'active' : ''}`}
              onClick={() => setSelectedGameId(game.id)}
            >
              <span>경기 {game.game_number}</span>
              <span className="game-status">
                {game.status === 'scheduled'
                  ? '예정'
                  : game.status === 'in_progress'
                  ? '진행 중'
                  : '완료'}
              </span>
            </button>
          ))}
        </div>
      </div>

      {selectedGame && (
        <div className="scoreboard-detail">
          <div className="scoreboard-header">
            <span className="game-number">경기 {selectedGame.game_number}</span>
            <span className="game-status">
              {selectedGame.status === 'scheduled'
                ? '예정'
                : selectedGame.status === 'in_progress'
                ? '진행 중'
                : '완료'}
            </span>
          </div>

          <div className="scoreboard-main">
            <div className="team team1">
              <h3>1팀</h3>
              <div className="players">
                <div className="player">{selectedGame.player1_name}</div>
                <div className="player">{selectedGame.player2_name}</div>
              </div>
            </div>

            <div className="score-display-large">
              <div className="score team1-score">{selectedGame.score1}</div>
              <div className="separator">:</div>
              <div className="score team2-score">{selectedGame.score2}</div>
            </div>

            <div className="team team2">
              <h3>2팀</h3>
              <div className="players">
                <div className="player">{selectedGame.player3_name}</div>
                <div className="player">{selectedGame.player4_name}</div>
              </div>
            </div>
          </div>

          <div className="scoreboard-footer">
            <div className="referee-info">
              <span className="label">심판:</span>
              <span className="value">{selectedGame.referee_name}</span>
            </div>
          </div>
        </div>
      )}

      <div className="all-games-summary">
        <h3>전체 경기 현황</h3>
        <div className="games-grid">
          {games.map((game) => (
            <div key={game.id} className="game-card">
              <div className="card-header">경기 {game.game_number}</div>
              <div className="card-score">
                <span>{game.score1}</span>
                <span>-</span>
                <span>{game.score2}</span>
              </div>
              <div className="card-players">
                <div className="team-players">
                  <span className="player-small">{game.player1_name}</span>
                  <span className="player-small">{game.player2_name}</span>
                </div>
                <span className="vs">vs</span>
                <div className="team-players">
                  <span className="player-small">{game.player3_name}</span>
                  <span className="player-small">{game.player4_name}</span>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}

export default Scoreboard;
