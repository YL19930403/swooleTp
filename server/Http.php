<?php
/**
 * Created by PhpStorm.
 * User: yuliang
 * Date: 2019/5/20
 * Time: 下午4:18
 */

class Http
{
    CONST HOST = '0.0.0.0';
    CONST PORT = 9501;

    public $http = null;

    public function __construct()
    {
        $this->http = new swoole_http_server(self::HOST, self::PORT);

        $this->http->set([
            'enable_static_handler' => true,
            'document_root' => '/Users/yuliang/swooleTp/public/static',
            'worker_num' => 2,
            'task_worker_num' => 2,
        ]);

        $this->http->on('workerstart', [$this, 'onWorkerStart']);
        $this->http->on('request', [$this, 'onRequest']);
        $this->http->on('task', [$this, 'onTask']);
        $this->http->on('finish', [$this, 'onFinish']);
        $this->http->on('close', [$this, 'onClose']);
        $this->http->start();
    }

    public function onWorkerStart(swoole_http_server $server, $worker_id)
    {
        // 定义应用目录
        define('APP_PATH', __DIR__ . '/../application/');
        require_once  __DIR__ .   '/../thinkphp/base.php';;
    }

    public function onRequest(swoole_http_request $request, swoole_http_response $response)
    {
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
    }

    public function onTask(swoole_server $serv, int $task_id, int $worker_id,  $data)
    {
        print_r($data);
        sleep(10);
        return 'on task finish' .date('Y-m-d H:i:s', time()) ; //告诉worker
    }

    public function onFinish(swoole_http_server $serv, int $task_id, string $data)
    {
        echo "taskId:{$task_id}\n";
        echo "finish-data-success:{$data}\n" . date('Y-m-d H:i:s', time());
    }

    public function onClose(swoole_http_server $ws, $fd)
    {
        echo "clientid-{$fd} is closed\n" . date('Y-m-d H:i:s', time());
    }

}

new Http();