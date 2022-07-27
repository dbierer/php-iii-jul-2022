<?php
include __DIR__ . '/vendor/autoload.php';
use App\Ntp\Client;
use App\Lorem\Ipsum;
use App\Geonames\Random;

// record start time
$start = microtime(TRUE);

$fibers = [
    new Fiber(function () {
        // NTP request
        $error  = [];
        $client = new Client();
        echo "NTP Time:\n";
        var_dump($client->getTime($error));
    }),
    new Fiber(function () {
        // Lorem Ipsum
        echo "Lorem Ipsum:\n";
        echo Ipsum::getHtml();
    }),
    new Fiber(function () {
        // Pick random city
        $city = Random::pickCity();
        echo "Random City Info:\n";
        var_dump($city);
    }),
];

foreach ($fibers as $func) $func->start();

// report elapsed time
echo "\nElapsed Time: " . (microtime(TRUE) - $start) . "\n";
