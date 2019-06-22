<?php
/**
 * ThinCore is part of Alxarafe
 * Copyright (C) 2019 Alxarafe <info@alxarafe.com>
 */

define('BASE_PATH', __DIR__);
define('ALXARAFE_DIR', '/alxarafe');
define('DEBUG', true);

$autoloadFile = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoloadFile)) {
    die('<h1>Composer autoload not found!</h1><p>You need to run: composer install</p>');
}
require_once $autoloadFile;

(new \Alxarafe\Helpers\Dispatcher())->run();

die(DEBUG ? 'End of execution!' : '');