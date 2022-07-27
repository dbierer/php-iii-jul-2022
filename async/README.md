# PHP Async

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
php -S localhost:8008 -t public
```
From a browser: `http://localhost:8008/`
