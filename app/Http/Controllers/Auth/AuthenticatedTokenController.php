<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * @group Bearer Token
 * 
 * 인증(로그인)에 필요한 토큰 관련 API입니다.
 */
class AuthenticatedTokenController extends Controller
{
    protected $platform = 'native';

    /**
     * 토큰 생성 (로그인)
     * 
     * 토큰 생성 API 입니다. 일반적으로 로그인 처리를 위한 토큰을 발급받을 때 사용할 수 있습니다.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return Bearer token
     * 
     * 필요 데이터
     * - 닉네임을 제외하고 회원가입과 동일함.
     * 
     * @bodyParam platform string 로그인 플랫폼<br>(`native`-기본값, `naver`, `kakao`, `google`) Example: naver
     * @bodyParam email string required 이메일 Example: test@test.co.kr
     * @bodyParam device_name string required 토큰 발급에 쓰일 디바이스명 Example: device-name
     * @bodyParam password string 패스워드<br>(쌀먹 자체 로그인의 경우 꼭 기입해주세요) Example: test1234
     * @bodyParam id string SNS 아이디<br>(SNS 로그인의 경우 꼭 기입해주세요) Example: 111086235715594204387
     * 
     * @response {
     *    "header": {
     *        "code": "0000",
     *        "msg": "성공"
     *    },
     *    "body": {
     *        "bearer_token": "19|0HbLWGnpiQ6yYNr01HnFFION139HxCnAltAoM2qm"
     *    }
     * }
     * 
     * @responseField bearer_token Bearer 토큰
     */
    public function generate(Request $request) {

        // 플랫폼 전역 변수 셋팅
        $this->platform = empty($request->platform) ? 'native' : $request->platform;

        $this->validator($request);

        // 유저 조회
        $user = User::where('email', $request->email)->first();
        
        /**
         * 쌀먹 자체 로그인
         * 
         * SNS 로그인의 경우에는 유저 체크를 /auth/social 에서
         * 미리 하기 때문에 바로 토큰을 생성한다.
         */
        if($this->platform == 'native') {

            // 유저 존재 여부 및 패스워드 일치 여부 체크
            if (!$user || !Hash::check($request->password, $user->password)) {
                // 일치하지 않을 경우 메세지 반환
                throw ValidationException::withMessages([
                    'email' => ['계정을 찾을 수 없습니다.'],
                ]);
            }
        }

        // 토큰 생성
        return jsonResponseFormat('0000', __('code.0000'), ['bearer_token' => $user->createToken($request->device_name)->plainTextToken]);
    }


    /**
     * 토큰 생성에 필요한 데이터 유효성 검사
     */
    public function validator(Request $request) {

        $validate = [
            'email' => 'required|email',
            'device_name' => 'required', 
        ];

        $message = [
            'required' => ':attribute 값을 입력해주세요',
        ];

        $field = [
            'email' => '이메일',
            'device_name' => '디바이스명'
        ];

        if($this->platform == 'native') { // 자체 로그인의 추가 유효성 체크

            $validate['password'] = 'required';
            $field['password'] = '패스워드';

        } else { // SNS 로그인의 추가 유효성 체크

            $validate['id'] = 'required';
            $field['id'] = 'SNS 아이디';

        }

        return $request->validate($validate, $message, $field);
    }

    /**
     * 토큰 삭제 (로그아웃)
     * 
     * 유저가 현재 액세스한 토큰의 정보를 DB상에서 삭제합니다. 일반적으로 로그아웃 시에 사용되며
     * 해당 API 실행 이후 파괴된 토큰으로는 두번 다시 해당 토큰을 통한 인증이 불가능합니다.
     * 
     * @authenticated
     * 
     */
    public function destroy(Request $request) {
        $request->user()->currentAccessToken()->delete();
    }
}
