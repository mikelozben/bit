<?php
    $config = require(__DIR__ . '/../config/main.php');
    
    if (file_exists(__DIR__ . '/../config/main.local.php')) {
        $config = array_merge($config, require(__DIR__ . '/../config/main.local.php'));
    }
    require($config['baseDir'] . '/vendor/autoload.php');
    
    $strRoute = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
    if (!is_string($strRoute)) {
        $strRoute = null;
    }
    
    session_start();
    $objMysqlConnector = Components\MysqlConnector::get($config);
    $objController = new Controllers\IndexController($config);
    
    try {
        
        echo $objController->processRoute($strRoute);
        
    } catch (Exception $exception) {
        echo $objController->renderView('error', ['exception' => $exception] + $objController->viewCommonData);
    }
    
    if (PHP_SESSION_ACTIVE === session_status()) {
        session_write_close();
    }
