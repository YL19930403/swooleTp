<?php
/**
 * Created by PhpStorm.
 * User: yuliang
 * Date: 2019/5/21
 * Time: 上午10:41
 */

namespace app\common\lib\task;

use app\common\Sms;
use app\common\lib\Redis;
use app\common\lib\redis\Predis;

class Task
{
    /**
     * 异步发送验证码
     * @param $data
     */
    public function sendSms($data)
    {
        try{
            $response = Sms::sendSms($data['phone'], $data['code']);

        }catch(\Exception $e){
//            echo $e->getMessage();
            return false;
        }

        if($response['Code'] == 'OK')
        {
            Predis::getInstance()->set(Redis::smsKey($data['phone'], $data['code']), $data['code'], config('redis.out_time'));
        }else{
            return false;
        }
        return true;
    }
}