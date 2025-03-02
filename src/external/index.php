<?php

use ChurchCRM\dto\SystemConfig;

require '../Include/Config.php';
//require '../Include/Functions.php';

// This file is generated by Composer
require_once __DIR__.'/../vendor/autoload.php';

// Instantiate the app
$app = new \Slim\App();
$container = $app->getContainer();
if (SystemConfig::debugEnabled()) {
    $container['settings']['displayErrorDetails'] = true;
}

// Set up
require __DIR__.'/../Include/slim/error-handler.php';

// routes
require __DIR__.'/routes/register.php';
require __DIR__.'/routes/verify.php';
require __DIR__.'/routes/calendar.php';

// Run app
$app->run();
