<?php
/**
 * Created by PhpStorm.
 * User: yuliang
 * Date: 2019/5/20
 * Time: 上午11:00
 */

namespace app\common\lib;

class Redis
{
    public static $smsPre = 'sms_';
    public static $userPre = 'user_';

    /**
     * 存储验证码 redis  key
     * @param string $phone_no
     * @return string
     */
    public static function smsKey($phone_no = '')
    {
        return self::$smsPre . $phone_no;
    }

    public static function userKey($phone_no='')
    {
        return self::$userPre . $phone_no;
    }
}