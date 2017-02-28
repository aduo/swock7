<?php
namespace Swock\Framework\Core;

use cli\Arguments;
use Swock\Framework\Core\Servers\SocketServer;

class Control {
    private static $_tree = [];
    private static $_server;
    private static $_config = [];
    private static $_instance;
    private static $_pidFile;
    static $_flags = [
        [
            "flag" => ["daemon", "d"],
            "description" => "启用守护进程模式"
        ], [
            "flag" => ["force", "f"],
            "description" => "强制关闭server"
        ], [
            "flag" => ["host", "H"],
            "description" => "指定监听地址"
        ], [
            "flag" => ["port", "p"],
            "description" => "指定监听端口"
        ], [
            "flag" => ["help", "h"],
            "description" => "显示帮助界面"
        ], [
            "flag" => ["base", "b"],
            "description" => "使用BASE模式启动"
        ], [
            "flag" => "start",
            "description" => "启动server"
        ], [
            "flag" => "stop",
            "description" => "关闭server"
        ], [
            "flag" => "reload",
            "description" => "重新加载代码"
        ]
    ];

    static $_options = [
        [
            "option" => ["worker", "w"],
            "description" => "设置Worker进程的数量"
        ], [
            "option" => ["thread", "r"],
            "description" => "设置Reactor线程的数量"
        ], [
            "option" => ["tasker", "t"],
            "description" => "设置Task进程的数量"
        ]
    ];

    static $pidFile;
    private function __construct() {
    }

    static function setPidFile(string $pidFile) {
        self::$_pidFile = $pidFile;
    }

    static function start(callable $callback) {
        if(!self::$_pidFile) {
            throw new \Exception("缺少pid file");
        }

        $pid_file = self::$_pidFile;
        if(is_file($pid_file)) {
            $server_pid = file_get_contents($pid_file);
        } else {
            $server_pid = 0;
        }
        $arguments = new Arguments();

        foreach(self::$_flags as $flag) {
            $arguments->addFlag($flag['flag'], $flag['description']);
        }

        foreach(self::$_options as $option) {
            $arguments->addOption($option['option'], $option['description']);
        }
        $arguments->parse();

        if($arguments['help']) {
            echo $arguments->getHelpScreen();
            die;
        }

        if($arguments['start']) {
            self::run(!!$arguments['daemon']);
            die;
        }

        if($arguments['stop']) {
            if($arguments['force']) {
                echo "stop -f";
            }
            die;
        }


    }


    private static function run(bool $daemon = false) {
        //读取所有配置文件.
        $server_config = config('server');
        $server = new SocketServer();
        $server->initServer($server_config);
//        self::setServer($server);
//        $server->run();
    }


    /**
     * @return array
     */
    public static function getTree(): array {
        return self::$_tree;
    }

    /**
     * @return mixed
     */
    public static function getServer() {
        return self::$_server;
    }

    /**
     * @param mixed $server
     */
    public static function setServer($server) {
        self::$_server = $server;
    }

    /**
     * @param null $item
     *
     * @return array
     */
    public static function getConfig($item) {
        $itemArray = explode(".", $item);
        if(empty($itemArray)) {
            return false;
        }
        $fileName = ucwords(array_shift($itemArray));
        if(self::$_config[$fileName] == null) {
            $config = require ROOT_PATH . '/Config/' . $fileName . '.php';
            self::$_config[$fileName] = $config;
        }
        $tempConfig = self::$_config[$fileName];
        while(!empty($itemArray)) {
            $tempItem = array_shift($itemArray);
            if($tempConfig[$tempItem] == null) {
                return false;
            }
            $tempConfig = $tempConfig[$tempItem];
        }
        return $tempConfig;
    }

    /**
     * @param array $config
     */
    public static function setConfig(array $config) {
        self::$_config = $config;
    }

    /**
     * @return mixed
     */
    public static function getInstance() {
        if(!(self::$_instance instanceof self)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }


    public function __clone() {
    }
}