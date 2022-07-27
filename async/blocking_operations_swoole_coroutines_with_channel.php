<?php
include __DIR__ . '/vendor/autoload.php';
use Swoole\Coroutine as Co;
use Swoole\Coroutine\Channel;
use App\Weather\Forecast;
use App\Geonames\Random;

Co\run(function () {

    $chan = new Channel(1);

    go(function() use ($chan) {
        // Pick random city
        $city = Random::pickCity();
        echo "Random City Info:\n";
        $chan->push(['city' => $city]);
    });

    go(function() use ($chan) {
        $data = $chan->pop();
        $city = $data['city'] ?? [];
        if (!empty($city[2])) {
            $name = $city[2];
            $lat  = $city[3];
            $lon  = $city[4];
            echo "Weather forecast for $name\n";
            echo (new Forecast())->getForecast($lat, $lon);
        }
    });
});
