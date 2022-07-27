<?php
include __DIR__ . '/vendor/autoload.php';
use App\Ntp\Client;
use App\Lorem\Ipsum;
use App\Weather\Forecast;
use App\Geonames\Random;

// record start time
$start = microtime(TRUE);

// NTP request
$error  = [];
$client = new Client();
echo "NTP Time:\n";
var_dump($client->getTime($error));

// Lorem Ipsum
echo "Lorem Ipsum:\n";
echo Ipsum::getHtml();

// Pick random city
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
$city = Random::pickCity();
echo "Random City Info:\n";
var_dump($city);

// Weather Forecast for Random City
if (!empty($city[2])) {
    $name = $city[2];
    $lat  = $city[3];
    $lon  = $city[4];
    echo "Weather forecast for $name\n";
    echo (new Forecast())->getForecast($lat, $lon);
}

// report elapsed time
echo "\nElapsed Time: " . (microtime(TRUE) - $start) . "\n";
