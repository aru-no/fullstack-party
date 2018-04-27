<?php

require_once('../bootstrap.php');

session_start();

/** @var \Slim\App $app */
$app = (new \App\App($config))->getApp();
$app->run();