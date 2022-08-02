<?php
echo '<a href="index.html">BACK</a>';
echo "<pre>\n";
$start = microtime(TRUE);
$key   = 'prime';
$ttl   = 300;   // 5 minutes
$data  = NULL;
$cache = $argv[1] ?? TRUE;
if (!function_exists('apcu_exists')) {
    exit("APCU extension not installed\n");
}
if (apcu_exists($key)) {
    echo "Key $key exists ... fetching data\n";
    $data = apcu_fetch($key);
} else {
    echo "Key $key does not exist\n";
    echo "Generating prime numbers from 1 to 100\n";
    include __DIR__ . '/../async/src/App/Number/Prime.php';
    $data = '';
    foreach (\App\Number\Prime::generate(1000, 9999) as $num) $data .= $num . ' ';
    if (apcu_store($key, $data, $ttl)) {
        echo "Key $key stored in cache\n";
    } else {
        echo "Unable to store key $key stored in cache\n";
    }
}
echo "\n";
$diff = microtime(TRUE) - $start;
printf("Elapsed Time: %8.12f seconds\n", $diff);
echo "Test Data\n";
echo $data . "\n";
echo "APCU Info\n";
var_dump(apcu_cache_info());
echo "</pre>\n";
echo '<a href="index.html">BACK</a>';

