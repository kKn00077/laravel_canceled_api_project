<?php

namespace App\Listeners;

use App\Models\Logs\AccountCertificationLog;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * 인증 로그 업데이트
 */
class LogVerifiedUpdate
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Verified  $event
     * @return void
     */
    public function handle(Verified $event)
    {
        $user = $event->user;

        AccountCertificationLog::where('user_id', $user->id)
        ->where('email', $user->email)
        ->where('expiration_at', $user->auth_expire_at)
        ->update(['is_auth' => true]);
    }
}
