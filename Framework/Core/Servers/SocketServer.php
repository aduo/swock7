<?php

namespace Swock\Framework\Core\Servers;


class SocketServer extends HttpServer {
    public function __construct() {
        parent::__construct();
    }


    public function initServer($config) {
        $server = new \swoole_websocket_server($config['host'], $config['port'], $config['mode'], $config['sock_type']);
        $this->server = $server;
    }


    public function run() {
        parent::run();

    }

}