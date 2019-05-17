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
$http->set([
    'enable_static_handler' => true,
    'document_root' => '/Users/yuliang/swooleTp/public/static',
]);

$http->on('request', function (swoole_http_request $request, swoole_http_response $response){
    $response->cookie('sigma', 'xssss', time() + 1800);
    $response->end("wudy.yu".json_decode($request->get));
});

$http->start();