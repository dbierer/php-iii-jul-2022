<?php
include __DIR__ . '/vendor/autoload.php';
use Swoole\Coroutine as Co;
use App\Ntp\Client;
use App\Lorem\Ipsum;
use App\Geonames\Random;

// record start time
$start = microtime(TRUE);

Co\run(function () {

    go(function() {
        // NTP request
        $error  = [];
        $client = new Client();
        echo "NTP Time:\n";
        var_dump($client->getTime($error));
    });

    go(function() {
        // Lorem Ipsum
        echo "Lorem Ipsum:\n";
        echo Ipsum::getHtml();
    });

    go(function() {
        // Pick random city
        $city = Random::pickCity();
        echo "Random City Info:\n";
        var_dump($city);
    });

});

// report elapsed time
echo "\nElapsed Time: " . (microtime(TRUE) - $start) . "\n";

/*
$geonamesFile = __DIR__ . '/data/' . Random::GEONAMES_FILTERED;
if (!file_exists($geonamesFile)) {
    echo "\nShort Geonames file doesn't exist\n"
         . "To build the file, prceed as follows:\n"
         . "wget " . Build::GEONAMES_URL . "\n"
         . "unzip -o data/" . Build::GEONAMES_SHORT . "\n"
         . "App\Geonames\Build::buildShort()\n"
         . "App\Geonames\Build::filterByCountry('US', \$src, \$dest)\n";
    echo "\nYou need to filter by US because the (free) US weather service only provides weather for the USA\n";
    exit;
}
 */
