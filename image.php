<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
$config = require __DIR__ . DIRECTORY_SEPARATOR . 'config.php';

/** @var \classes\Application $application */
$application = new classes\Application($config);
$application->run();
