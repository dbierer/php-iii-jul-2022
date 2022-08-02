<?php

declare(strict_types=1);

namespace Weather\Handler;

use Weather\Service\ {Geo,Forecast};
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\HtmlResponse;
use Mezzio\Template\TemplateRendererInterface;

class Main implements RequestHandlerInterface
{
    /**
     * @var TemplateRendererInterface
     */
    private $renderer;

    public function __construct(TemplateRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        // init vars
        $start = microtime(TRUE);
        $output = 'Please select a city';

        // grab list of cities
        $cities = Geo::getNames();

        // grab name if city (if any)
        $params = $request->getQueryParams();
        if (!empty($params['city_search'])) {
            $name = $params['city_search'];
        } elseif (!empty($params['city_select'])) {
            $name = $params['city_select'];
        } else {
            $name = '';
        }
        $output = var_export($params, TRUE);

        // if we have a name, make API call to weather service using lat/lon
        if (!empty($name)) {
            $lat = $cities[$name][0] ?? NULL;
            $lon = $cities[$name][1] ?? NULL;
            if (!empty($lat) && !empty($lon)) {
                $output = (new Forecast())->getForecast((float) $lat, (float) $lon);
            }
        }

        // calculate elapsed time
        $elapsed = microtime(TRUE) - $start;
        // Render and return a response:
        return new HtmlResponse($this->renderer->render(
            'weather::main',
            ['cities' => $cities, 'output' => $output, 'elapsed' => $elapsed]
        ));
    }
}
