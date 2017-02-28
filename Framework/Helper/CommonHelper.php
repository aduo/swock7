<?php

if(!function_exists('config')) {
    function config($item) {
        return \Swock\Framework\Core\Control::getConfig($item);
    }
}