# laravel_canceled_api_project

회사에서 작업한 프로젝트였는데 프로젝트 캔슬남
이 프로젝트는 제가 나중에 base project로 잘 쓰겠습니다

**님아 이거 회원가입 로직이 이상한데요? 라고 묻지 말아주세요 저도 기획서를 따라서 해씅ㄹ 뿐...**

*laravel 8로 구현되어있음*

**주의: DB 구조는 알아서 할 것. 
해당 프로젝트에서는
sanctum과 비밀번호 찾기/변경 등은 라라벨 기본 제공 테이블을 그대로 사용함.
유저의 경우는 계정/프로필/설정/디바이스 이렇게 4개로 테이블을 분리하였음.**

## 현재까지 구현되어 있는 것
- sanctum token 로그인
- 플랫폼 자체 로그인
- sns 로그인 (프론트 측에서 sns 정보 제공 필요함)
- response format 지정
- validation exception response format 지정
- 회원가입 시 인증 이메일 발송
- 인증 시 log 처리 및 계정 인증 처리
- error 코드 지정
- API 문서 공동 작업 가능함 [scribe 라이브러리 사용](https://scribe.knuckles.wtf/)
