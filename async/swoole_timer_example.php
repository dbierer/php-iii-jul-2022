<?php
include __DIR__ . '/vendor/autoload.php';
use App\Ntp\Client;

// NTP request
$client = new Client();
$callback = function() use ($client) {
    $time = $client->getTime();
    var_dump($time);
};
// make an NTP request every 3 seconds
Swoole\Timer::tick(3000, $callback);
