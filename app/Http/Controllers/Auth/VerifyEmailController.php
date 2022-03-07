<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;

/**
 * @group Email Verify
 * 
 * 이메일 검증을 합니다. 
 * 유저가 메일 링크를 클릭하여 동작하는 로직입니다
 * 어플 내에서는 기본적으로 호출할 일이 없으니 참고용으로만 써주세요.
 */
class VerifyEmailController extends Controller
{
    /**
     * 이메일 검증
     *
     * 유저 정보를 조회해 검증 처리를 합니다.
     * 해당 로직을 접근할 수 있는 URL은 서버 내에서 검증된 URL입니다.
     * 일반적으로 접근할 수 없습니다.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     * 
     * @urlParam id integer required 유저의 키값
     * @urlParam hash string required 서버내부에서 처리한 해시값
     */
    public function __invoke(Request $request, $id)
    {
        $user = $request->user();
        $expire_at = intval($request->expires);

        if(empty($user)) {
            $user = User::find($id);
        }
        
        if(empty($user)) {
            return '유저 정보를 찾을 수 없습니다.';
        }

        if ($user->hasVerifiedEmail()) {
            return '이미 인증된 이메일입니다.';
        }

        // 이메일 인증 처리 한 후
        // 인증 이벤트를 발생 시켜 LogVerifiedUpdate@handle을 실행한다.
        if ($user->markEmailAsVerified()) {
            $user->auth_expire_at = Carbon::createFromTimestamp($expire_at);
            event(new Verified($user));
        }

        return '인증 성공. 창을 닫아주세요.';
    }
}
