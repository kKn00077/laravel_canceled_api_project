<?php

namespace App\Models;

use App\Models\Logs\AccountCertificationLog;
use Carbon\Carbon;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    // 인증 메일 관련해 필요한 데이터들 
    public $auth_email = null;
    public $auth_expire_at = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'nickname',
        'password',
        'login_type',
        'sns_id',
        'connect_at',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'sns_id',
    ];

    protected $appends = [
        
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        
    ];


    /**
     * 접속 일시 업데이트
     */
    public function updateConnectAt(){
        $this->connect_at = Carbon::now();
        $this->save();
    }

    /**
     * 이메일 인증 X 처리
     */
    public function unverify(){
        $this->email_verified_at = null;
        $this->save();
    }


    /**
     * 유저 데이터 생성
     */
    public function createUserData($setting){

        // 유저 프로필 생성
        if(!isset($this->profile)) {
            $this->profile()->save(new UserProfile([]));
        }

        // 유저 디바이스 정보 생성
        if(!isset($this->device)) {
            $this->device()->save(new UserDevice([]));
        }

        // 유저 설정 생성
        if(!isset($this->setting)) {
            $is_push = isset($setting['is_push'])?$setting['is_push']:null;

            $this->setting()->save(new UserSetting([
                'is_push' => filter_var($is_push, FILTER_VALIDATE_BOOLEAN),
            ]));
        }

    }


    /**
     * 관계 정의
     */
    public function profile() {
        return $this->hasOne(UserProfile::class);
    }

    public function device() {
        return $this->hasOne(UserDevice::class);
    }

    public function setting() {
        return $this->hasOne(UserSetting::class);
    }

    public function auth_log() {
        return $this->hasMany(AccountCertificationLog::class);
    }


    /**
     * 오버라이딩
     */

     // User가 생성될 때 이벤트가 발생해(EventServiceProvider) SendEmailVerificationNotification::class를 호출한다.
     // 해당 클래스 내부에 있는 sendEmailVerificationNotification을 호출하는데
     // 해당 함수를 오버라이딩 해 이메일 인증 메일 발송을 커스텀한다.
    public function sendEmailVerificationNotification() {
        
        if($this->login_type == 'native') { // 자체 회원가입의 경우
            
            // 이메일 인증 재발송 시 재발송할 이메일을 입력 했으면 회원 이메일 정보로 해당 이메일로 업데이트
            if(isset($this->auth_email)) {
                $this->email = $this->auth_email;
                $this->save();
            }

            // 메일 내용 정의
            VerifyEmail::toMailUsing(
                function ($user, $url) { 
                    return (new MailMessage)
                    ->subject('쌀먹 이메일 인증')
                    ->line('이메일 인증을 위해서 아래의 버튼을 눌러주세요.')
                    ->action('인증하기', $url)
                    ->line('계정을 생성한 적이 없다면 문의 부탁드립니다.');
                }
            );

            // 인증 URL 정의
            VerifyEmail::createUrlUsing(
                function ($user) {
                    $user->auth_expire_at = Carbon::now()->addMinutes(Config::get('auth.verification.expire', 1440));

                    return URL::temporarySignedRoute(
                        'verification.verify',
                        $user->auth_expire_at,
                        [
                            'id' => $user->getKey(),
                            'hash' => sha1($user->getEmailForVerification()),
                        ]
                    );
                }
            );

            // 알림(인증메일) 발송
            $this->notify(new VerifyEmail);
            
            // 인증 메일 발송 로그 저장
            $this->auth_log()->save(new AccountCertificationLog([
                'email' => $this->email,
                'auth_key' => sha1($this->getEmailForVerification()),
                'is_auth' => false,
                'expiration_at' => $this->auth_expire_at,
            ]));

        } else {// sns 가입의 경우
            // 바로 이메일 인증 처리
            $this->markEmailAsVerified();
        }
    }
}
