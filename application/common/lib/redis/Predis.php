<?php
/**
 * Created by PhpStorm.
 * User: yuliang
 * Date: 2019/5/20
 * Time: 下午3:05
 */
namespace  app\common\lib\redis;

class Predis
{
    private static $_instance = null;


    private function __construct()
    {
        $this->redis = new \Redis();
        $result = $this->redis->connect(config('redis.host'), config('redis.port'), config('redis.out_time'));
        if($result === false ){
            throw new \Exception('redis connect error');
        }
    }

    public static function getInstance()
    {
        if(empty(self::$_instance))
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * set设值
     * @param $key
     * @param $value
     * @param int $time
     * @return bool|string
     */
    public function set($key, $value, $time=0)
    {
        if(!$key)
        {
            return '';
        }

        if(is_array($value))
        {
            $value = json_encode($value);
        }

        if(!$time)
        {
            return $this->redis->set($key, $value);
        }

        return $this->redis->setex($key, $time, $value);
    }

    /**
     * get获取值
     * @param $key
     * @return bool|string
     */
    public function get($key)
    {
        if(!$key)
        {
            return '';
        }
        return $this->redis->get($key);
    }
}