# PHP Async

Confirm that the Swoole extension is installed:
```
php -m |grep swoole
```
If not: 
* Get list of PHP versions and make note of the path to the original PHP 8.1 version:
```
sudo update-alternatives --list php
```
* Set PHP to the original 8.1 (s/be `/usr/bin/php8.1`
```
sudo update-alternatives --set php /usr/bin/php8.1
```
* Install Swoole
```
sudo apt install php8.1-swoole
```

* Create a test program `/path/to/test/program/server.php`:
```
<?php
$server = new Swoole\HTTP\Server("127.0.0.1", 9501);

$server->on("start", function (Swoole\Http\Server $server) {
    echo "Swoole http server is started at http://127.0.0.1:9501\n";
});

$server->on("request", function (Swoole\Http\Request $request, Swoole\Http\Response $response) {
    $response->header("Content-Type", "text/plain");
    $response->end("Hello World\n");
});

$server->start();
```
* Open a new terminal window and run the test program
```
cd /path/to/test/program
php swoole_test.php
```
* From the VM browser: `http://localhost:9501`

NOTE: to switch between PHP alternatives (e.g. outside of ZendPHP):
```
sudo update-alternatives
```

## Other Examples
NOTE: these are a work-in-progress and still need tweaking

### Normal
```
php blocking_operations_normal.php
```

### Swoole
The Swoole examples require the `Swoole` extension to be installed.
Blocking operations solved using coroutines:
```
php blocking_operations_swoole_coroutines.php
```
Swoole Using Channels:
```
php blocking_operations_swoole_coroutines_with_channel.php
```
Swoole timers:
```
php swoole_timer_example.php
```
Swoole Server
```
php swoole_server.php
```
Then, from a browser: `http://localhost:9501`

### ReactPHP
Install ReactPHP
```
php ../composer.phar install
```
Run the example
```
php blocking_operations_react_php.php
```

### Fibers
The Fibers exmples requires PHP 8.1+
```
php blocking_operations_fibers.php
```

### Mezzio Swoole Lab
```
cd mezzio_swoole_lab
php ../../composer.phar install --ignore-platform-reqs
php -S localhost:8008 -t public
```
From a browser: `http://localhost:8008/weather`
