<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @group Social Login
 * 
 * 소셜 로그인/회원가입 처리를 합니다.
 */
class AuthenticatedSocialController extends Controller
{
    /**
     * 소셜 로그인/회원가입
     * 
     * 소셜 로그인을 통해 소셜 측 유저 데이터를 서버에서 전달 받아<br>
     * 해당 데이터를 기반으로 가입 여부를 판단합니다.
     * 
     * <p>
     *     <b>가입하지 않았을 경우 회원가입, 가입했을 경우 로그인 로직을 탑니다.</b><br>
     *     <b>response 데이터는 회원가입 API와 로그인 API 부분을 참고해주세요</b>
     * </p>
     * 
     * <aside>
     * 유저의 닉네임 데이터는 로그인 처리할 때는 필요하지 않지만 회원가입 처리 시에는 필요하므로 꼭 보내주세요.
     * </aside>
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * 
     * 필수 데이터
     * - email
     * - id
     * - platform
     * 
     * 선택 데이터
     * - nickname (회원가입 처리를 할 경우에는 필수)
     * 
     * @bodyParam platform string required 로그인 플랫폼<br>(`naver`, `kakao`, `google`) Example: naver
     * @bodyParam email string required 이메일 Example: test@test.co.kr
     * @bodyParam nickname string required 닉네임 Example: mynickname1234
     * @bodyParam device_name string required 토큰 발급에 쓰일 디바이스명 Example: device-name
     * @bodyParam id string required SNS 아이디 Example: 111086235715594204387
     * @bodyParam is_push boolean/string 푸시 발송 여부 Example: false
     */
    public function __invoke(Request $request)
    {
        $email = $request->email;
        $sns_id = $request->id;
        $platform = $request->platform;

        // 유저 정보가 있는지 없는지 조회한다.
        $user = User::where('email', $email)
                ->where('sns_id', $sns_id)
                ->where('login_type', $platform)->first();
    

        if(empty($user)) {
            // 회원가입
            return (new RegisteredUserController)->store($request);
        } else {
            // 로그인
            return (new AuthenticatedTokenController)->generate($request);
        }
    }
}
