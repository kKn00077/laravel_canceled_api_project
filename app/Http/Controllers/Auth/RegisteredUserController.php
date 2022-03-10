<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

/**
 * @group Sign Up
 * 
 * 자체 회원가입 / SNS 로그인(회원가입)을 지원합니다.
 */
class RegisteredUserController extends Controller
{

    protected $platform = 'native';

    /**
     * 회원가입
     * 
     * 회원가입 처리를 하는 API입니다.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Bearer token
     * 
     * 
     * 필요 데이터 (Request)
     * 
     * - 자체 회원가입/SNS 회원가입 공통 데이터
     *     - platform (native - 자체 회원가입, kakao - 카카오 로그인, google - 구글 로그인, naver - 네아로)
     *     - nickname
     *     - email
     *     - device_name (토큰 생성을 위한 토큰 구별값으로 쓰임 -> 사실상 아무값이어도 상관 없음)
     *     - is_push
     * 
     * - 자체 회원가입
     *     - platform = 'native'
     *     - password
     *     - is_push
     * 
     * - SNS 회원가입
     *     - platform = 'kakao' or 'google' or 'naver'
     *     - id (sns_id)
     *     - is_push
     * 
     * @bodyParam platform string 회원가입 플랫폼<br>(`native`-기본값, `naver`, `kakao`, `google`) Example: naver
     * @bodyParam nickname string required 닉네임 Example: mynickname1234
     * @bodyParam email string required 이메일 Example: test@test.co.kr
     * @bodyParam device_name string required 토큰 발급에 쓰일 디바이스명 Example: device-name
     * @bodyParam password string 패스워드<br>(쌀먹 자체 회원가입의 경우 꼭 기입해주세요) Example: test1234
     * @bodyParam id string SNS 아이디<br>(SNS 회원가입의 경우 꼭 기입해주세요) Example: 111086235715594204387
     * @bodyParam is_push boolean/string 푸시 발송 여부 Example: false
     * 
     * @response 201 {
     *    "header": {
     *        "code": "0001",
     *        "msg": "성공적으로 생성되었습니다."
     *    },
     *    "body": {
     *        "bearer_token": "19|0HbLWGnpiQ6yYNr01HnFFION139HxCnAltAoM2qm"
     *    }
     *}
     * 
     * @responseField bearer_token Bearer 토큰
     */
    public function store(Request $request)
    {
        // 플랫폼 전역 변수 셋팅
        $this->platform = empty($request->platform) ? 'native' : $request->platform;

        // 유효성 검사
        $this->validator($request);

        // 계정 생성
        $user = $this->register($request);

        // 접속 일시 업데이트
        $user->updateConnectAt();

        // 토큰 생성
        return jsonResponseFormat('0001', __('code.0001'), ['bearer_token' => $user->createToken($request->device_name)->plainTextToken], 201);
    }


    /**
     * 계정 생성
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return User
     */
    public function register(Request $request) {

        $user = new User;

        $user->nickname = $request->nickname;
        $user->email = $request->email;
        $user->login_type = $this->platform;

        if($this->platform == 'native') { // 쌀먹 자체 회원가입일 경우에는 패스워드를 추가
            $user->password = Hash::make($request->password);
        } else { // sns 회원가입일 경우에는 sns 로그인 유저 고유 아이디를 추가
            $user->sns_id = $request->id;
        }

        // 유저 데이터 생성
        $user->save();

        // 계정 정보 이외의 유저 데이터 생성
        $user->createUserData($request->all());

        event(new Registered($user));

        return $user;
    }

    /**
     * 회원가입 유효성 검사
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return $request->validate()
     */
    public function validator(Request $request) {

        /**
         * 유효성 검사 조건 정의
         */
        $validate = [
            'nickname' => [
                'required', 
                'string', 
                'max:10',
                'regex:/([\p{Hangul}]{2,})|\w{3,}/u', // 한글 2글자 이상 / 문자/숫자 3글자 이상
                'not_regex:/[!?@#$%^&*():;+-=~{}<>\_\[\]\|\\\"\'\,\.\/\`\₩]$/', // 특수문자 포함 금지
                ],
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:255', 
                'unique:users'
            ],
            'device_name' => [
                'required',
                'string',
                'max:255'
            ]
        ];

        /**
         * 조건 미달시 반환한 메세지 정의
         */
        $message = [];

        /*
        * 각 필드값 별 :attribute 값 정의 
        */
        $field = [];

        if($this->platform == 'native') { // 자체 회원가입의 추가 유효성 체크
            $validate['password'] = [
                'required', 
                Rules\Password::min(8),
                'regex:/(?=.*\d)(?=.*[!?@#$%^&*():;+-=~])([^\s]){8,}$|^(?=.*\d)(?=.*[a-zA-Z])([^\s]){8,}$|^(?=.*[a-zA-Z])(?=.*[!?@#$%^&*():;+-=~])([^\s]){8,}$/', // 영문/숫자/특수문자 중 2가지 이상 조합
                'not_regex:/(\w)\1\1/' // 연속된 문자 3개 이상일 경우 불일치 체크
            ];
        } else { // SNS 회원가입의 추가 유효성 체크
            $validate['id'] = [
                'required',
                'string',
                'max:255'
            ];

            $field['id'] = 'SNS 아이디';
        }

        return $request->validate($validate, $message, $field);
    }
}
