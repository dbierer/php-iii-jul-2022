<?php
include __DIR__ . '/vendor/autoload.php';
use App\Geonames\City;
use Swoole\Table;
use Swoole\HTTP\Server;
use Swoole\Http\ {Request,Response};
use Swoole\Coroutine as Co;
use Swoole\Coroutine\Channel;
use App\Weather\Forecast;

$cities = City::getNames();

ob_start();
include __DIR__ . '/includes/index.phtml';
$contents = ob_get_contents();
ob_end_clean();

$server = new Server("127.0.0.1", 9501);
$server->on("start", function (Swoole\Http\Server $server) {
    echo "Swoole http server is started at http://127.0.0.1:9501\n";
});

$server->on("request", function (Request $request, Response $response)  use ($contents, $cities) {
    $output = $contents;
    $name = $request->get['city_search']
            ?? $request->get['city_select']
            ?? '';
    if ($name) {
        $lat = $cities[$name][0] ?? NULL;
        $lon = $cities[$name][1] ?? NULL;
        if (!empty($lat) && !empty($lon)) {
            $forecast = (new Forecast())->getForecast((float) $lat, (float) $lon);
            $split    = explode('<!-- FORECAST -->', $contents);
            $output   = $split[0] . $forecast . $split[2];
        }
    }
    $response->header("Content-Type", "text/html");
    $response->write($output);
});

$server->start();
