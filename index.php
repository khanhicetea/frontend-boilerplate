<?php

error_reporting(0);

$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

$autoloader = require_once ('./vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

$template_file = substr($_SERVER['REQUEST_URI'], 1);

$loader = new Twig_Loader_Filesystem('./templates');
$twig = new Twig_Environment($loader);

echo $twig->render($template_file, array());
