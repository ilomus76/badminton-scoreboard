import React, { useState, useEffect } from 'react';
import axios from 'axios';

const API_URL = 'http://localhost:5000/api';

function GameBoard({ token }) {
  const [games, setGames] = useState([]);
  const [selectedGame, setSelectedGame] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    fetchGames();
  }, []);

  const fetchGames = async () => {
    try {
      setLoading(true);
      const response = await axios.get(`${API_URL}/games`);
      setGames(response.data);
      if (response.data.length > 0) {
        setSelectedGame(response.data[0]);
      }
    } catch (err) {
      setError('경기 데이터를 불러올 수 없습니다');
    } finally {
      setLoading(false);
    }
  };

  const handleScoreUpdate = async (gameId, score1, score2, status) => {
    try {
      await axios.put(
        `${API_URL}/games/${gameId}/score`,
        { score1, score2, status },
        { headers: { Authorization: `Bearer ${token}` } }
      );
      fetchGames();
      setError('');
    } catch (err) {
      setError('점수 업데이트 실패');
    }
  };

  if (loading) {
    return <div className="loading">로딩 중...</div>;
  }

  return (
    <div className="game-board">
      <h2>경기 점수 관리</h2>
      {error && <div className="error-message">{error}</div>}

      <div className="game-board-container">
        <div className="game-list">
          <h3>경기 목록</h3>
          {games.map((game) => (
            <div
              key={game.id}
              className={`game-item ${selectedGame?.id === game.id ? 'active' : ''}`}
              onClick={() => setSelectedGame(game)}
            >
              <span>경기 {game.game_number}</span>
              <span className="score">
                {game.score1} - {game.score2}
              </span>
            </div>
          ))}
        </div>

        {selectedGame && (
          <div className="game-detail">
            <h3>경기 {selectedGame.game_number}</h3>
            <div className="game-info">
              <div className="team-section">
                <h4>1팀</h4>
                <div className="player-info">
                  <span className="player-name">{selectedGame.player1_name}</span>
                  <span className="player-role">복식</span>
                </div>
                <div className="player-info">
                  <span className="player-name">{selectedGame.player2_name}</span>
                  <span className="player-role">복식</span>
                </div>
              </div>

              <div className="score-section">
                <div className="score-display">
                  <span className="team1-score">{selectedGame.score1}</span>
                  <span className="separator">:</span>
                  <span className="team2-score">{selectedGame.score2}</span>
                </div>
                <div className="score-controls">
                  <div className="control-group">
                    <label>1팀 점수</label>
                    <div className="button-group">
                      <button
                        onClick={() =>
                          handleScoreUpdate(
                            selectedGame.id,
                            Math.max(0, selectedGame.score1 - 1),
                            selectedGame.score2,
                            selectedGame.status
                          )
                        }
                        className="btn-control"
                      >
                        -
                      </button>
                      <input
                        type="number"
                        value={selectedGame.score1}
                        onChange={(e) =>
                          setSelectedGame({
                            ...selectedGame,
                            score1: parseInt(e.target.value) || 0,
                          })
                        }
                      />
                      <button
                        onClick={() =>
                          handleScoreUpdate(
                            selectedGame.id,
                            selectedGame.score1 + 1,
                            selectedGame.score2,
                            selectedGame.status
                          )
                        }
                        className="btn-control"
                      >
                        +
                      </button>
                    </div>
                  </div>

                  <div className="control-group">
                    <label>2팀 점수</label>
                    <div className="button-group">
                      <button
                        onClick={() =>
                          handleScoreUpdate(
                            selectedGame.id,
                            selectedGame.score1,
                            Math.max(0, selectedGame.score2 - 1),
                            selectedGame.status
                          )
                        }
                        className="btn-control"
                      >
                        -
                      </button>
                      <input
                        type="number"
                        value={selectedGame.score2}
                        onChange={(e) =>
                          setSelectedGame({
                            ...selectedGame,
                            score2: parseInt(e.target.value) || 0,
                          })
                        }
                      />
                      <button
                        onClick={() =>
                          handleScoreUpdate(
                            selectedGame.id,
                            selectedGame.score1,
                            selectedGame.score2 + 1,
                            selectedGame.status
                          )
                        }
                        className="btn-control"
                      >
                        +
                      </button>
                    </div>
                  </div>
                </div>
              </div>

              <div className="team-section">
                <h4>2팀</h4>
                <div className="player-info">
                  <span className="player-name">{selectedGame.player3_name}</span>
                  <span className="player-role">복식</span>
                </div>
                <div className="player-info">
                  <span className="player-name">{selectedGame.player4_name}</span>
                  <span className="player-role">복식</span>
                </div>
              </div>
            </div>

            <div className="referee-section">
              <h4>심판</h4>
              <span>{selectedGame.referee_name}</span>
            </div>

            <div className="status-section">
              <label>경기 상태</label>
              <select
                value={selectedGame.status}
                onChange={(e) =>
                  handleScoreUpdate(
                    selectedGame.id,
                    selectedGame.score1,
                    selectedGame.score2,
                    e.target.value
                  )
                }
              >
                <option value="scheduled">예정</option>
                <option value="in_progress">진행 중</option>
                <option value="completed">완료</option>
              </select>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}

export default GameBoard;
