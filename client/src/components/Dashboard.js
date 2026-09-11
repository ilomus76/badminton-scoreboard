import React, { useState, useEffect } from 'react';
import axios from 'axios';
import GameBoard from './GameBoard';
import Scoreboard from './Scoreboard';

const API_URL = 'http://localhost:5000/api';

function Dashboard({ user, token, onLogout }) {
  const [activeTab, setActiveTab] = useState(user.role === 'admin' ? 'manage' : 'view');

  return (
    <div className="dashboard">
      <header className="dashboard-header">
        <div className="header-content">
          <h1>🏸 배드민턴 점수판</h1>
          <div className="user-info">
            <span>{user.username} ({user.role === 'admin' ? '관리자' : '관중'})</span>
            <button onClick={onLogout} className="btn btn-logout">
              로그아웃
            </button>
          </div>
        </div>
      </header>

      <div className="tabs">
        {user.role === 'admin' && (
          <button
            className={`tab-btn ${activeTab === 'manage' ? 'active' : ''}`}
            onClick={() => setActiveTab('manage')}
          >
            ⚙️ 점수 관리
          </button>
        )}
        <button
          className={`tab-btn ${activeTab === 'view' ? 'active' : ''}`}
          onClick={() => setActiveTab('view')}
        >
          📊 점수 조회
        </button>
      </div>

      <div className="tab-content">
        {activeTab === 'manage' && user.role === 'admin' && (
          <GameBoard token={token} />
        )}
        {activeTab === 'view' && (
          <Scoreboard token={token} />
        )}
      </div>
    </div>
  );
}

export default Dashboard;
