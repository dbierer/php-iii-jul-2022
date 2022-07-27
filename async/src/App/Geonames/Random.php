<?php
namespace App\Geonames;

use SplFileObject;
use RuntimeException;
#[App\Geonames\Random]
class Random extends Base
{
    /**
     * Picks city at random
     * Returns array like this:
     * ['name' => 'City Name', 'lat' => 'latitude', 'lon' => 'longitude']
     */
    #[App\Geonames\Random\pickCity\return("?array")]
    public static function pickCity() : ?array
    {
        $skip = rand(0, self::cityCount());
        $geo  = self::getGeo();
        $line = [];
        for ($x = 0; $x < $skip; $x++)
            $line = $geo->fgetcsv();
        return $line;
    }
}
