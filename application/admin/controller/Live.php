<?php
/**
 * Created by PhpStorm.
 * User: yuliang
 * Date: 2019/5/21
 * Time: 下午5:25
 */

namespace  app\admin\controller;

use app\common\lib\Util;
use think\Config;

class Live
{
    public function push()
    {
        $_POST['http_server']->push(2, 'hello-live-push-data');
    }
}