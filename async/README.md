# PHP Async

Lab: Swoole Extension
* If you haven't yet installed ZendPHP, follow the installation instructions here:
  * https://help.zend.com/zendphp/current/content/installation/linux_installation_zendphpctl.htm
  * Make `zendphpctl` available as a command:
```
chmod +x ./zendphpctl
sudo mv ./zendphpctl /usr/sbin/zendphpctl
```
  * Install the PHP 8.1
Now you can install the Swoole extension:
* Install the `swoole` extension using `zendphpctl`
```
zendphpctl ext-install swoole
```
* Confirm installation:
```
php -m |grep swoole
```
* Create a test program `server.php`:
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


## Blocking Operations
Try each of these in turn to see the effect of async on PHP code.
The Swoole examples require the `Swoole` extension to be installed.
The Fibers exmples requires PHP 8.1.

### Normal
```
php blocking_operations_normal.php
```

### Swoole
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

### Fibers
```
php blocking_operations_fibers.php
```

### Mezzio Swoole Lab
```
cd mezzio_swoole_lab
composer install --ignore-platform-reqs
php -S localhost:8008 -t public
```
From a browser: `http://localhost:8008/weather`
