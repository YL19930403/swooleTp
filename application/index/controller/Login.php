<?php
/**
 * Created by PhpStorm.
 * User: yuliang
 * Date: 2019/5/20
 * Time: 下午2:48
 */


namespace app\index\controller;

use app\common\lib\Redis;
use app\common\lib\redis\Predis;
use app\common\lib\Util;

class Login
{

    public function index()
    {
        $phone_num = intval($_GET['phone_num']);
        $code = intval($_GET['code']);
        if(empty($phone_num) || empty($code))
        {
            return Util::show(config('code.error'), 'param invalid');
        }

        try{
            $redis_code = Predis::getInstance()->get(Redis::smsKey($phone_num));
        }catch(\Exception $e){
            return Util::show(config('code.error'), $e->getMessage());
        }

        if($redis_code == $code)
        {
            $user_data = [
                'user' => $phone_num,
                'srckey' => md5(Redis::userKey($phone_num)),
                'time' => time(),
            ];
            Predis::getInstance()->set(Redis::userkey($phone_num), $user_data);
            return Util::show(config('code.success'), 'ok', $user_data);
        }else{
            return Util::show(config('code.error'), 'login failed!!!');
        }
    }
}