# To Run:
From a terminal window (command prompt) running from this directory:

* Install zend-stratigility:
```
composer install
```
* Enter this command
```
php -S localhost:9999 -t public
```
* From the browser:
```
http://localhost:9999
```
* If running in the course VM, you an also go to the `http://localhost` page and click on the `stratigility` link

* Composer Update
  * If you're running PHP 8 or above, use this syntax to update:
```
composer update --ignore-platform-reqs
```
