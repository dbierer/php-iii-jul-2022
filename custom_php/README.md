# Custom PHP Labs

Lab: Install Extension from Source
* Get list of PHP versions and make note of the path to the original PHP 8.1 version:
```
sudo update-alternatives --list php
```
* Set PHP to the original 8.1 (s/be `/usr/bin/php8.1`
```
sudo update-alternatives --set php /usr/bin/php8.1
```

* Install the pre-requisite packages
```
sudo apt install -y php8.1-dev libcurl4-openssl-dev
```
* Find the latest version of Swoole: `http://pecl.php.net/package/openswoole` and assign it to `VER`
```
# example: 4.11.1
VER=4.11.1
```
* Install the Swoole extension from source:
```
cd /tmp
wget http://pecl.php.net/get/openswoole-$VER.tgz
tar xvf openswoole-$VER.tgz
cd openswoole-$VER
phpize
./configure \
    --enable-openswoole \
    --enable-openssl \
    --enable-sockets \
    --enable-swoole-curl \
    --enable-swoole-json
make
sudo make install
```
* Create this entry:
```
sudo gedit /etc/php/8.1/cli/conf.d/60-swoole.ini
```
  * With this contents:
```
extension=openswoole.so
```
* Confirm:
```
php -m |grep swoole
```

Lab: Install PHP using ZendPHP
* Follow the installation instructions here:
  * https://help.zend.com/zendphp/current/content/installation/linux_installation_zendphpctl.htm
* Make `zendphpctl` available as a command:
```
chmod +x ./zendphpctl
sudo mv ./zendphpctl /usr/sbin/zendphpctl
```
* Install the PHP version of your choice (e.g. 8.1):
```
sudo zendphpctl repo-install
sudo zendphpctl php-install 8.1
```

Lab: Install an Extension Using ZendPHP
* Install the `apcu` extension using `zendphpctl`
```
sudo zendphpctl ext-install apcu
```
* Confirm installation:
```
php -m |grep apcu
```
* Make sure that `apcu` is enabled:
```
cat /etc/php/8.1-zend/mods-available/apcu.ini
```
  * You should see something like this:
```
extension=apcu.so
apc.enabled=1
apc.shm_size=32M
apc.ttl=7200
apc.enable_cli=1
```
* Run the test server:
```
php -S localhost:8008 -t /path/to/repo/custom_php
```
* From the browser: `http://localhost:8008`
* Be sure to run *With APCU* twice to see the caching effect!

Lab: OpCache and JIT
* Change the last line of code to output not using scientific notation!
```
printf("%8.12f\n", microtime(TRUE) - $start);
```
* Don't forget to set this parameter in the CLI `php.ini` file!
```
opcache.enable_cli=1
```
* JIT demo video: https://studio.youtube.com/video/eJHEpZZtc0c/edit
  * Source code referenced in the video:
  * https://github.com/dbierer/PHP-8-Programming-Tips-Tricks-and-Best-Practices.git

