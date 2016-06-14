<?php

error_reporting(E_ALL);

function rm_directory($dir) {
	$it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
	$files = new RecursiveIteratorIterator($it,
	             RecursiveIteratorIterator::CHILD_FIRST);
	foreach($files as $file) {
	    if ($file->isDir()){
	        rmdir($file->getRealPath());
	    } else {
	        unlink($file->getRealPath());
	    }
	}
	rmdir($dir);
}

function recurse_copy($src, $dst) { 
    $dir = opendir($src); 
    mkdir($dst); 
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                recurse_copy($src . '/' . $file, $dst . '/' . $file); 
            } 
            else { 
                copy($src . '/' . $file, $dst . '/' . $file); 
            } 
        } 
    } 
    closedir($dir); 
} 

// Autoload
$autoloader = require_once ('./vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

if (!isset($argv[1])) {
	echo "\n--- Please input template folder ---\n\n";
	exit;
}

$folder = $argv[1];
$relative_asset_url = isset($argv[2]) ? $argv[2] : 'a';
$template_dir = $folder . '/templates';

if (!is_dir($template_dir)) {
	echo "\n--- Template folder is not found ---\n\n";
	exit;
}

$loader = new Twig_Loader_Filesystem($template_dir);
$twig = new Twig_Environment($loader);

echo "\n----- Start !!! ------\n";

// Clean dist folder
echo "\n--- Cleaning dist folder ---";
$dist_folder = $folder . '/dist';
if (is_dir($dist_folder)) {
    rm_directory($dist_folder);    
}
mkdir($dist_folder);

// Copy entire assets folder
echo "\n--- Copying assets folder ---";
recurse_copy($folder . '/assets', $dist_folder . '/assets');

// Render template files
echo "\n--- Rendering Template ---";
$dir = opendir($template_dir); 
while(false !== ( $file = readdir($dir)) ) { 
    if (( $file != '.' ) && ( $file != '..' )) { 
        if (is_file($template_dir . '/' . $file) 
            && preg_match('/^[a-zA-Z0-9]+\.html$/', $file)) {
        	echo "\n\t- Rendering " . $file . " ...";
        	$content = $twig->render($file, array());
        	if ($relative_asset_url == 'r') {
        		$content = str_replace('="/assets', '="./assets', $content);
        		$content = str_replace("='/assets", "='./assets", $content);
        	}
        	$f = fopen($dist_folder . '/' . $file, 'w');
        	fwrite($f, $content);
        	fclose($f);
        } 
    } 
} 
closedir($dir);

echo "\n\n----- Completed !!! ------\n\n";