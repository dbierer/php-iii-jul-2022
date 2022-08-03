# To Run:
From a terminal window (command prompt) running from this directory:
* If you have not already installed ZendPHP:
  * Follow the installation instructions here:
    * https://help.zend.com/zendphp/current/content/installation/linux_installation_zendphpctl.htm
  * Make `zendphpctl` available as a command:
```
chmod +x ./zendphpctl
sudo mv ./zendphpctl /usr/sbin/zendphpctl
```
* Install the PDO extension (if not already installed)
```
sudo zendphpctl ext-install pdo mysql
```
* Install zend-stratigility:
```
php composer.phar install
```
* Enter this command
```
php -S localhost:8008 -t public
```
* From the browser:
```
http://localhost:8008
```
* Proceed with the lab as outlined in course module 8
