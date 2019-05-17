<?php
/**
 * Created by PhpStorm.
 * User: yuliang
 * Date: 2019/5/17
 * Time: 下午1:49
 */


//开启http_server :  php http_server.php
//访问形式: http://127.0.0.1:9501/?name=wudy.yu&age=26
$http = new swoole_http_server("0.0.0.0", 9501);

$http->on('request', function ($request, $response) {

    //处理chrom浏览器多次请求的问题
    if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
        return $response->end();
    }

    //类似于写日志：只要有请求过来就写入到文件中去
    $content = [
        'date' => date('Y-m-d H:i:s', time()),
        'post' => $request->post,
        'get' => $request->get,
        'header' => $request->header,
    ];

    Swoole\Async::writefile(__DIR__.'/access.log', json_encode($content).PHP_EOL, function ($filename){
        echo 'write success' . $filename;
    }, FILE_APPEND);


    var_dump($request->get, $request->post);
    $response->header("Content-Type", "text/html; charset=utf-8");
    $response->end("<h1>Hello Swoole. #".rand(1000, 9999)."</h1>");
});

$http->start();