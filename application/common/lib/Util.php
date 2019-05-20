<?php
/**
 * Created by PhpStorm.
 * User: yuliang
 * Date: 2019/5/20
 * Time: 上午11:40
 */

namespace app\common\lib;

class Util
{
    public static function show($status, $message='', $data = [])
    {
        $result = [
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ];
        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}