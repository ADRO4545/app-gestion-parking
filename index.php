<?php

use App\Controllers\AdminController;
use App\Controllers\AccountController;

session_start();
date_default_timezone_set('Europe/Paris');
require_once __DIR__ . '/config/env.php';
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/src/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

$router = new App\Services\Router();
$router->handleRequest();