# 닷홈(Dothome) 배포 가이드

## 📁 닷홈 폴더 구조 이해

닷홈의 기본 폴더 구조는 다음과 같습니다:

```
public_html/                 ← 웹사이트의 루트 폴더 (웹 접근 가능)
├── index.html              ← 기본 홈페이지
├── css/
├── js/
└── ...
```

## 🚀 배포 방법 (2가지 옵션)

### 옵션 1: PHP 기반 배포 (권장 - 닷홈과 호환성 100%)

**장점**: Node.js 없이도 PHP만으로 작동, 가장 간단한 배포

**구조**:
```
public_html/
├── index.php               ← PHP로 만든 로그인 페이지
├── api/
│   ├── login.php
│   ├── games.php
│   └── auth.php
├── css/
├── js/
├── data/
│   └── scoreboard.db       ← SQLite 데이터베이스
└── ...
```

**장점**:
- PHP만 필요 (닷홈 기본 지원)
- 별도의 서버 프로세스 불필요
- 배포가 매우 간단

**단점**:
- React를 직접 사용할 수 없음
- 순수 JavaScript + AJAX로 작성해야 함

---

### 옵션 2: Node.js 기반 배포 (React 유지)

**구조**:
```
public_html/
└── badminton-scoreboard/   ← React 앱의 build 폴더 내용
    ├── index.html
    ├── static/
    │   ├── css/
    │   └── js/
    └── ...

별도 서버 (Node.js API):
└── /server                 ← Express 백엔드
    ├── index.js
    └── data/
        └── scoreboard.db
```

**장점**:
- 현재 React 구조 유지
- 모던 개발 환경

**단점**:
- Node.js 호스팅 필요 (추가 비용)
- 닷홈 기본 지원 X
- 별도 포트 포워딩 필요

---

## 💡 추천: PHP 기반으로 재작성 (가장 실용적)

닷홈에서 가장 안정적으로 운영하려면 **PHP + JavaScript 버전**을 추천합니다.

React는 프론트엔드용으로 충분하지만, 닷홈의 제약이 있으므로 다음과 같이 하는 것을 추천합니다:

### 선택지:

**A) 현재 React 앱을 빌드해서 배포** (간단함)
```bash
cd client
npm run build
```
- `client/build` 폴더의 모든 내용을 `public_html`에 업로드
- React 앱은 정적 파일로 작동
- API는 별도로 구성

**B) PHP 기반으로 완전히 재작성** (권장)
- PHP + SQLite로 백엔드 구성
- jQuery 또는 순수 JavaScript로 프론트엔드 작성
- 가장 간단하고 안정적

---

## 📝 닷홈 배포 단계별 가이드

### **1단계: FTP 접속**
- FTP 클라이언트 (FileZilla, WinSCP 등) 사용
- 닷홈에서 제공하는 FTP 정보 입력
- `public_html` 폴더에 접속

### **2단계: React 빌드 파일 업로드**
```bash
# 로컬에서 실행
cd client
npm run build
```

생성된 `build` 폴더의 모든 파일을 `public_html`에 업로드

### **3단계: API 구성**
- `public_html/api/` 폴더 생성
- PHP API 파일들 업로드
- SQLite 데이터베이스 파일 생성

### **4단계: 권한 설정**
```
data/scoreboard.db → 권한 777 또는 644 설정
```

---

## 🔌 닷홈에서 Node.js 사용 가능?

**일반적으로 불가능** (공유 호스팅의 경우)

**가능한 경우**:
- VPS/전용 서버 구매
- Heroku, Render 같은 별도 Node.js 호스팅 사용
- 비용: 월 5,000원 ~ 10,000원

---

## ✅ 최종 추천 방안

1. **프론트엔드**: React 빌드 → `public_html/` 업로드
2. **백엔드**: PHP API 버전 별도 작성 또는
3. **대안**: Node.js를 위해 Render/Railway 같은 서비스 사용

다음 중 원하시는 방식을 알려주시면:
- ✅ PHP 기반 완전한 버전 작성
- ✅ React 빌드 + PHP API 하이브리드 버전
- ✅ Render 같은 무료 호스팅으로 Node.js 배포

상세히 도와드리겠습니다!
