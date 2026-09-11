import React, { useState } from 'react';
import axios from 'axios';

const API_URL = 'http://localhost:5000/api';

function Login({ onLogin }) {
  const [isLogin, setIsLogin] = useState(true);
  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      const endpoint = isLogin ? '/auth/login' : '/auth/register';
      const response = await axios.post(`${API_URL}${endpoint}`, {
        username,
        password,
      });

      const { token, user } = response.data;
      onLogin(token, user);
    } catch (error) {
      setError(error.response?.data?.error || '오류가 발생했습니다');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="login-container">
      <div className="login-card">
        <h1>🏸 배드민턴 점수판</h1>
        <form onSubmit={handleSubmit}>
          <div className="form-group">
            <label htmlFor="username">사용자명</label>
            <input
              id="username"
              type="text"
              value={username}
              onChange={(e) => setUsername(e.target.value)}
              placeholder="사용자명 입력"
              required
            />
          </div>
          <div className="form-group">
            <label htmlFor="password">비밀번호</label>
            <input
              id="password"
              type="password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              placeholder="비밀번호 입력"
              required
            />
          </div>
          {error && <div className="error-message">{error}</div>}
          <button type="submit" disabled={loading} className="btn btn-primary">
            {loading ? '처리 중...' : isLogin ? '로그인' : '회원가입'}
          </button>
        </form>
        <p className="toggle-text">
          {isLogin ? '계정이 없으신가요? ' : '이미 계정이 있으신가요? '}
          <button
            type="button"
            className="toggle-btn"
            onClick={() => {
              setIsLogin(!isLogin);
              setError('');
            }}
          >
            {isLogin ? '회원가입' : '로그인'}
          </button>
        </p>
        <div className="demo-accounts">
          <h3>테스트 계정</h3>
          <p>관리자: admin / admin123</p>
          <p>관중: viewer1 / viewer123</p>
        </div>
      </div>
    </div>
  );
}

export default Login;
