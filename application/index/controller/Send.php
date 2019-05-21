<?php
/**
 * Created by PhpStorm.
 * User: yuliang
 * Date: 2019/5/19
 * Time: 下午2:44
 */

namespace app\index\controller;

use app;
use app\common\Sms;
use app\common\lib\Redis;
use app\common\lib\Util;
use think\Log;

// 指定允许其他域名访问
header('Access-Control-Allow-Origin:*');
// 响应类型
header('Access-Control-Allow-Methods:GET');
// 响应头设置
header('Access-Control-Allow-Headers:x-requested-with,content-type');

class Send{
    /**
     * 发送验证码
     */
    public function index()
    {
        $phone_num = request()->get('phone_num', 0,'intval');
//        echo $phone_num;

        if(empty($phone_num))
        {
//            return app\show(config('code.success'), 'success');
            Log::record('手机号不能为空', 'ERROR');
            return Util::show(config('code.error'), '手机号不能为空');
        }

        //生产随机数
        $authCodeMT = mt_rand(100000,999999);
        /*
        try{
            $result = Sms::sendSms($phone_num, $authCodeMT);
        }catch(\Exception $e){
            Log::record('阿里大鱼内部异常', 'ERROR');
            return Util::show(config('code.error'), '阿里大鱼内部异常');
        }

        if($result['Code'] == 'OK')
        {
            //记录redis
            $redis = new \Swoole\Coroutine\Redis();
            $redis->connect(config('redis.host'), config('redis.port'));
            $redis->set(Redis::smsKey($phone_num), $authCodeMT, config('redis.out_time'));
            return Util::show(config('code.success'), 'success');
        }else{
            Log::record('验证码发送失败', 'ERROR');
            return Util::show(config('code.error'), '验证码发送失败' );
        }
        */

        $task_data = [
            'method' => 'sendSms',
            'data' => [
                'phone' => $phone_num,
                'code' => $authCodeMT,
            ]
        ];
        print_r($_GET);

        $_GET['http_server']->task($task_data);
    }
}