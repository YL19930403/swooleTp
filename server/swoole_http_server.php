<?php
/**
 * Created by PhpStorm.
 * User: yuliang
 * Date: 2019/5/17
 * Time: 下午2:04
 */



$http = new swoole_http_server('127.0.0.1', 9501);

//设置静态文件
//设置document_root并设置enable_static_handler为true后，底层收到Http请求会先判断document_root路径下是否存在此文件，
//如果存在会直接发送文件内容给客户端，不再触发onRequest回调。
//开启swoole_http_server :  php  swoole_http_server.php
//在浏览器访问 ： http://127.0.0.1:9501/index.html


//注意：ThinkPHP默认只支持pathinfo访问:  pathinfo的访问形式: http://wudy.live.cn:8090/?s=index/index/sendSms
// http://wudy.live.cn:8090/index/index/index  这种访问在swoole到ThinkPHP是不支持的（除非配置了）


$http->set([
    //设置了worker_num和task_worker_num超过1时，每个进程都会触发一次onWorkerStart事件，可通过判断$worker_id区分不同的工作进程
    'work_num' => 5,
    'task_worker_num' => 10,

    'enable_static_handler' => true,
    'document_root' => '/Users/yuliang/swooleTp/public/static',
]);

$http->on('request', function (swoole_http_request $request, swoole_http_response $response) use ($http){

//    print_r($request->server);

    //将swoole的请求获取参数转换为PHP原生的形式
    $_POST = [];
    if(isset($request->post))
    {
       foreach ($request->post as $k=>$v)
       {
           $_POST[$k] = $v;
       }
    }

    //PHP中超全局不会被释放(会导致每次的请求参数都累计在一个数组里面)，需要我们手动去释放掉
//    if(!empty($_GET))
//    {
//        unset($_GET);
//    }
    $_GET = [];
    if(isset($request->get))
    {
        foreach ($request->get as $k=>$v)
        {
            $_GET[$k] = $v;
        }
    }

    $_SERVER = [];
    if(isset($request->header))
    {
        foreach ($request->header as $k=>$v)
        {
            $_SERVER[strtoupper($k)] = $v;
        }
    }

    if(isset($request->server))
    {
        foreach ($request->server as $k=>$v)
        {
            $_SERVER[strtoupper($k)] = $v;
        }
    }

    ob_start();
    //执行应用
    try {
        think\App::run()->send();
    }catch (\Exception $e){
        echo '异常错误' . $e->getCode() . $e->getMessage();
        exit;
    }

    echo 'action-'.request()->action().PHP_EOL;
    $res = ob_get_contents();
    ob_end_clean();

    $response->cookie('sigma', 'xssss', time() + 1800);
    //end操作后将向客户端浏览器发送HTML内容,只能调用一次
    $response->end($res);

    //关闭客户端连接: 会把变量全部注销掉，就不需要判断 !empty($_GET)
    //直接close是不太有好的，需要我们修改TP框架的源码: 注释掉thinkphp/library/think/Request中 path() 跟 pathinfo()的is_null判断逻辑

    $http->close();



});

/**
 * 事件在Worker进程/Task进程启动时发生
 * @param $worker_id : worker进程的id
 */
$http->on('WorkerStart', function(swoole_http_server $serv , $worker_id){
    // 定义应用目录
    define('APP_PATH', __DIR__ . '/../application/');
    require_once  __DIR__ .   '/../thinkphp/base.php';;
});

$http->on('Task', function (swoole_http_server $serv, swoole_server_task $task){

});

$http->start();