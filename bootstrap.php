<?php
use \Slim\Http\Request;
use \Slim\Http\Response;

require_once('vendor/autoload.php');

$config = [];
$configFile = dirname(__FILE__) . '/config/config.php';
if (is_readable($configFile)) {
    require_once($configFile);
} else {
    exit('Config not found');
}