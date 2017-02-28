<?php
date_default_timezone_set('PRC');
define('START_TIME', time());
define('START_MEMORY', memory_get_usage());
define('ROOT_PATH', dirname(__DIR__));  //根目录
require ROOT_PATH . '/vendor/autoload.php';

use Swock\Framework\Core\Control;
Control::setPidFile(ROOT_PATH . '/pid');

Control::start(function(){

});
