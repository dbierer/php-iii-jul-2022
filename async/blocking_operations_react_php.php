<?php
include __DIR__ . '/vendor/autoload.php';
use React\Async;
use App\Number\Prime;
use App\Ntp\Client;
use App\Lorem\Ipsum;
use App\Geonames\Random;

// record start time
$start = microtime(TRUE);

// NTP
$promise = [
    'ntp' => Async\coroutine(function () {
        // NTP request
        $error  = [];
        $client = new Client();
        return var_export($client->getTime($error), TRUE);
    }),
    'ipsum' => Async\coroutine(function () {
        // Lorem Ipsum
        return Ipsum::getHtml();
    }),
    'random' => Async\coroutine(function () {
        // Pick random city
        return var_export(Random::pickCity(), TRUE);
    }),
    'prime' => Async\coroutine(function () {
        // Generate prime numbers
        $str = '';
        foreach (Prime::generate(9_000_000, 9_000_999) as $num) $str .= $num . ' ';
        return $str;
    }),
];

$expected = count($promise);
$actual   = 0;

// NOTE: this needs to be rewritten
//       as-is doesn't make full use of ReactPHP
foreach ($promise as $key => $promise) {
    $promise->then(function (string $result) use ($actual, $key) {
        echo strtoupper($key) . ":\n" . $result . "\n";
        $actual++;
    }, function (Exception $e) {
        echo strtoupper($key) . "\n";
        echo 'Error: ' . $e->getMessage() . PHP_EOL;
        $actual++;
    });
}

// report elapsed time
echo "\nElapsed Time: " . (microtime(TRUE) - $start) . "\n";
