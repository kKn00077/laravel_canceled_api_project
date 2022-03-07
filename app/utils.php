<?php

function jsonResponseFormat($code, $msg=null, $body=[], $status=200) {
    $response = [
        'header' => [
            'code' => $code,
            'msg' => $msg
        ],
        'body' => $body
    ];

    return response()->json($response, $status);
}