<?php
/**
 * Created by PhpStorm.
 * User: yuliang
 * Date: 2019/5/21
 * Time: 下午1:37
 */

class ws
{
    CONST HOST = '0.0.0.0';
    CONST PORT = 9502;

    public $server;

    public function __construct()
    {
        $this->server = new swoole_websocket_server('127.0.0.1', 9502);

        $this->server->set([
            'work_num' => 4,
            'task_worker_num' => 4,

            'enable_static_handler' => true,
            'document_root' => '/Users/yuliang/swooleTp/public/static',
        ]);

        //监听WebSocket连接打开事件
        $this->server->on('open', [$this, 'onOpen']);
        //监听websocket消息事件
        $this->server->on('message', [$this, 'onMessage']);

        $this->server->on('task', [$this, 'onTask']);
        $this->server->on('finish', [$this, 'onFinish']);
        //监听worker进程/task进程启动
        $this->server->on('workerstart', [$this, 'onWorkerStart']);
        //监听http请求
        $this->server->on('request', [$this, 'onRequest']);
        //监听websocket关闭
        $this->server->on('close', [$this, 'onClose']);
        $this->server->start();
    }

    public function onWorkerStart(swoole_websocket_server $server, $worker_id)
    {
        // 定义应用目录
        define('APP_PATH', __DIR__ . '/../application/');
        require  __DIR__ .   '/../thinkphp/base.php';  //start.php 或者  base
    }

    /**
     * 监听WebSocket连接打开事件
     * @param swoole_websocket_server $ws
     * @param $request
     */
    public function onOpen(swoole_websocket_server $ws, $request)
    {
//        var_dump($request->fd, $request->get, $request->server);
        $ws->push($request->fd, "hello, welcome\n" . date('Y-m-d H:i:s', time()));
    }

    /**
     * 监听http的请求
     * @param $request
     * @param $response
     */
    public function onRequest($request, $response)
    {
        // 接收http请求从get获取message参数的值，给用户推送
        // $this->server->connections 遍历所有websocket连接用户的fd，给所有用户推送
        foreach ($this->server->connections as $fd)
        {
            // 需要先判断是否是正确的websocket连接，否则有可能会push失败
            //查连接是否为有效的WebSocket客户端连接。此函数与exist方法不同，exist方法仅判断是否为TCP连接，无法判断是否为已完成握手的WebSocket客户端
            if ($this->server->isEstablished($fd))
            {
                $this->server->push($fd, $request->get['message']);
            }
        }

        //将swoole的请求获取参数转换为PHP原生的形式
        $_POST = [];
        if(isset($request->post))
        {
            foreach ($request->post as $k=>$v)
            {
                $_POST[$k] = $v;
            }
        }

        $_FILES = [];
        if(isset($request->files))
        {
            foreach ($request->files as $k=>$v)
            {
                $_FILES[$k] = $v;
            }
        }

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

        $_POSt['http_server'] = $this->server;
        ob_start();
        //执行应用
        try {
            think\App::run()->send();
        }catch (\Exception $e){
            echo 'Exception:' . $e->getCode() . $e->getMessage();
        }

        echo 'action-'.request()->action().PHP_EOL;
        $res = ob_get_contents();
        ob_end_clean();

        $response->cookie('sigma', 'xssss', time() + 1800);
        //end操作后将向客户端浏览器发送HTML内容,只能调用一次
        $response->end($res);
    }

    /**
     * 监听收到消息
     * @param swoole_websocket_server $ws
     * @param swoole_websocket_frame $frame
     */
    public function onMessage(swoole_websocket_server $ws, swoole_websocket_frame $frame)
    {
//        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
        $ws->push($frame->fd, "server-push: {$frame->data}" . date('Y-m-d H:i:s', time()));
    }

    /**
     * //监听websocket关闭
     * @param swoole_websocket_server $ws
     * @param $fd
     */
    public function onClose(swoole_websocket_server $ws, $fd)
    {
        echo "clientid-{$fd} is closed\n" . date('Y-m-d H:i:s', time());
    }

    public function onTask(swoole_server $serv, int $task_id, int $worker_id,  $data)
    {
        return 'on task finish' .date('Y-m-d H:i:s', time()) ; //告诉worker
    }

    //当worker进程投递的任务在task_worker中完成时，task进程会通过swoole_server->finish()方法将任务处理的结果发送给worker进程

    /**
     * @param swoole_websocket_server $serv (如果是tcp_server)
     * @param int $task_id
     * @param string $data
     */
    public function onFinish(swoole_websocket_server $serv, int $task_id, string $data)
    {
        echo "taskId:{$task_id}\n";
        echo "finish-data-success:{$data}\n" . date('Y-m-d H:i:s', time());
    }
}

new ws();