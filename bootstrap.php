<?php

require_once __DIR__ . '/function.php';
$config = require_once __DIR__ . DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php';
$config_account = require_once __DIR__ . DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config_account.php';
$config_btn = require_once __DIR__ . DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'btn.conf';
require __DIR__.DIRECTORY_SEPARATOR."vendor".DIRECTORY_SEPARATOR."lincanbin".DIRECTORY_SEPARATOR."php-pdo-mysql-class".DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."PDO.class.php";

function autoload($classname) {
    $filename = __DIR__ . DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR. strtolower($classname) .".php";
    require_once($filename);
}

spl_autoload_register("autoload");
require_once __DIR__ . '/vendor/autoload.php';
