<?php

declare(strict_types=1);

define('BASE_DIR', __DIR__);

ini_set('soap.wsdl_cache_enabled', '1');
ini_set('soap.wsdl_cache_dir', __DIR__ . '/tmp');
ini_set('soap.wsdl_cache_ttl', '86400');
ini_set('soap.soap.wsdl_cache_limit', '5');


use Bramus\Router\Router;

require_once '../vendor/autoload.php';
$time = microtime(true);

$router = new Router();

$router->set404(function () {
    header('HTTP/1.1 404 Not Found');
    echo('404');
});
$router->setNamespace('cbInformer\Controllers');
$router->post('/get-currency-rate-diff', 'ApiController@getCurrencyRateDiff');
$router->post('/get-cross-currency-rate-diff', 'ApiController@getCrossCurrencyRateDiff');
$router->run();
$processTime = microtime(true) - $time;
echo(PHP_EOL . 'Process Time: ' . $processTime);
