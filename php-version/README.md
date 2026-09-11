# PHP 버전 - 배드민턴 점수판

닷홈에서 직접 작동하는 PHP 기반 배드민턴 점수판입니다.

## 📁 폴더 구조

```
php-version/
├── index.php              ← 메인 페이지 (로그인 + 대시보드)
├── config.php             ← 데이터베이스 설정
├── .htaccess              ← Apache 라우팅
├── api/
│   ├── login.php          ← 로그인 API
│   ├── register.php       ← 회원가입 API
│   ├── verify.php         ← 토큰 검증 API
│   └── games.php          ← 경기 API
└── data/
    └── scoreboard.db      ← SQLite 데이터베이스 (자동 생성)
```

## 🚀 닷홈 배포 방법

### 1단계: FTP 업로드
- FileZilla 또는 WinSCP로 접속
- `php-version/` 폴더의 모든 파일을 `public_html/`에 업로드

### 2단계: 권한 설정
- `data` 폴더 권한: 755
- `data/scoreboard.db` 권한: 644

### 3단계: 접속
- `http://yoursite.dothome.co.kr/php-version/`

## 🔐 기본 계정

- **관리자**: admin / admin123
- **관중**: viewer1 / viewer123

## ✨ 기능

✅ 사용자 인증 (로그인/회원가입)  
✅ 4경기 복식 점수 관리  
✅ 실시간 점수 업데이트  
✅ 관리자 점수 관리  
✅ 관중 점수 조회  
✅ 반응형 디자인  

## 📝 API 엔드포인트

### 인증
- `POST api/login.php` - 로그인
- `POST api/register.php` - 회원가입
- `POST api/verify.php` - 토큰 검증

### 경기
- `GET api/games.php` - 모든 경기 조회
- `GET api/games.php?id=1` - 특정 경기 조회
- `PUT api/games.php?id=1` - 점수 업데이트 (관리자만)
- `POST api/games.php` - 경기 생성 (관리자만)

## 🔒 보안

- 비밀번호는 bcrypt로 해시 처리됨
- JWT 토큰 기반 인증
- 역할별 접근 제어 (admin/viewer)

## 💾 데이터베이스

자동으로 SQLite 데이터베이스가 생성되며, 다음 테이블을 포함합니다:

- `users` - 사용자 정보
- `games` - 경기 정보
