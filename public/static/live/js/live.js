var ws_url = "ws://127.0.0.1:9502";

var web_socket = new WebSocket(ws_url);

//实例化对象的onOpen属性
web_socket.onopen = function (ev) {
    web_socket.send('hello-world');
    console.log("connected-swoole-success");
};

//实例化对象的onMessage
web_socket.onmessage = function (ev) {
    console.log("web_socket_return_data:"+ev.data);
};

//实例化onclose
web_socket.onclose = function (ev) {
    console.log("web_socket_close");
};


//实例化onerror
web_socket.onerror = function (ev, e) {
    console.log("error" + ev.data);
}