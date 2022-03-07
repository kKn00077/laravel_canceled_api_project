<?php

return [

    /*
    |--------------------------------------------------------------------------
    | 상태 코드
    |--------------------------------------------------------------------------
    |
    | Response에 담아 전달할 상태코드 정의
    |
    */


    // 0xxx => API 정상 수행
    '0000' => '성공',
    '0001' => '성공적으로 생성되었습니다.',

    // 1xxx => API 정상 수행 했으나 이슈 사항 안내 코드
    '1000' => '이미 처리된 데이터입니다.'

    // 2xxx => API 수행 시 오류 발생
];
