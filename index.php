<?php

error_reporting(E_ALL);

$filename = $_SERVER['DOCUMENT_ROOT'] . preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

$folder = substr($_SERVER['DOCUMENT_ROOT'], strlen(__DIR__));
$folder = ltrim($folder, '/');

$autoloader = require_once ('./vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

$template_file = substr($_SERVER['REQUEST_URI'], 1);

$loader = new Twig_Loader_Filesystem('./' . $folder . '/templates');
$twig = new Twig_Environment($loader);

echo $twig->render($template_file, array());
