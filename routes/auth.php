<?php

use App\Http\Controllers\Auth\AuthenticatedSocialController;
use App\Http\Controllers\Auth\AuthenticatedTokenController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// 소셜 로그인/회원가입 분기 처리
Route::post('/auth/social', AuthenticatedSocialController::class);

// 회원가입 + 이메일 인증 메일 발송 + 토큰 생성 (로그인)
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register');

// 토큰 생성 (로그인)
Route::post('/auth/token', [AuthenticatedTokenController::class, 'generate'])->name('login');

// 이메일 인증
Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
->middleware(['signed', 'throttle:6,1'])
->name('verification.verify');

Route::group(['middleware' => ['auth:sanctum']], function() {
    
    // 이메일 재발송
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('verification.send');
    
    // 토큰 삭제 (로그아웃)
    Route::delete('/auth/token', [AuthenticatedTokenController::class, 'destroy'])->name('logout');
});