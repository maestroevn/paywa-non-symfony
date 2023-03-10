<?php

declare(strict_types=1);

use Paywa\CommissionTask\App;

if ('cli' !== php_sapi_name()) {
    die('ERROR: Not in CLI mode' . PHP_EOL);
}

if (false === is_array($argv) || 2 !== count($argv)) {
    die('ERROR: Invalid parameters set, CSV input file path is expected' . PHP_EOL);
}

require __DIR__ . '/../vendor/autoload.php';

$filePath = $argv['1'];

$app = new App();
echo $app->run($filePath);
