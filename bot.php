<?php
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . DIRECTORY_SEPARATOR.'bootstrap.php';

$bot = new Bot($config);
$bot->setCommands($config, $config_account, $config_btn);
$bot->run();