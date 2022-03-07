<?php

namespace App\Http\Controllers\Ad;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * @group Advertisement
 * 
 * 광고에 관련한 API를 지원합니다.
 */
class AdController extends Controller
{
    /**
     * 광고 리스트
     * 
     * 포지션(광고 위치)에 맞는 광고 리스트를 가져옵니다.
     * 
     * @queryParam position int 광고 위치(기본값 : 1) Example: 1
     */
    public function list(Request $request) 
    {
        $position = isset($request->position) ? $request->position : 1;
        $now = Carbon::now();

        $ad_list = Ad::where('ad_position', $position)
        ->where('ad_start_at', '<=' , $now)
        ->where('ad_end_at', '>=', $now)
        ->orderBy('order')
        ->get();

        return jsonResponseFormat('0000', __('code.0000'), ['ad_list' => $ad_list]);
    }
}
