<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    use HasFactory;

    /**
     * 라라벨 내부에서 모델명의 복수형을 스네이크 케이스 변환한 형태가 테이블명이라고 인식한다.
     * 
     * ex) UserDevice -> user_devices
     * 
     * 그렇기에 따로 테이블명을 지정해 모델에 인식시킨다.
     */
    protected $table = 'user_device';

    /**
     * 대량할당을 허용할 컬럼 지정
     * 
     * 허용한 컬럼에 한해서 create 메소드로 데이터 insert가 가능함
     */
    protected $fillable = [
        'user_id',
        'firebase_token',
    ];
}
