<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @group Email Verify ReSend
 */
class EmailVerificationNotificationController extends Controller
{
    /**
     * 이메일 인증 재발송
     * 
     * 이메일 인증을 위한 메일을 재발송하는 API입니다.
     * 인증 메일을 받게 될 이메일 정보를 입력해야합니다.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     * 
     * @authenticated
     * 
     * @bodyParam email string required 이메일 Example: test@test.co.kr
     * 
     * @response {
     *     "header": {
     *         "code": "0000",
     *         "msg": "성공"
     *     },
     *     "body": []
     * }
     * 
     * @response {
     *     "header": {
     *         "code": "1000",
     *         "msg": "이미 인증된 이메일 입니다."
     *     },
     *     "body": []
     * }
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $auth_email = $request->email;

        $this->validator($request, $user);

        // 메일 재발송할 경우 새로운 이메일 세팅
        if(!empty($auth_email)) {
            $user->auth_email = $auth_email;

            // 이메일 인증 초기화
            $user->unverify();
        }

        if ($user->hasVerifiedEmail()) {
            return jsonResponseFormat('1000', '이미 인증된 이메일 입니다.');
        }

        // 재발송
        $user->sendEmailVerificationNotification();

        return jsonResponseFormat('0000', __('code.0000'));
    }

    /**
     * 인증 메일 재발송 유효성 검사
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return $request->validate()
     */
    public function validator(Request $request, $user) {
        
        /**
         * 유효성 검사 조건 정의
         */
        $validator_data = [
            'email' => [
                'required', 
                'string', 
                'email', 
                'max:255',
            ],
        ];

        // 본인 이메일과 동일할 경우 조건 추가
        if($request->email != $user->email) {
            array_push($validator_data['email'], 'unique:users');
        }

        return $request->validate($validator_data, [
            /**
             * 조건 미달시 반환한 메세지 정의
             */
            'required' => ':attribute 값을 입력해주세요',
            'email.unique' => '이미 가입이 되어있는 :attribute입니다',
            'min' => ':attribute의 최소 글자수는 :min자리입니다',
            'max' => ':attribute의 최대 글자수는 :max자리입니다',
        ], [
            /*
             * 각 필드값 별 :attribute 값 정의 
             */
            'email' => '이메일',
        ]);
    }
}
