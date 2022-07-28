#!/bin/bash
apt-get update
apt-get install -y git net-tools iputils-ping
cd /tmp
curl -L https://repos.zend.com/zendphp/zendphpctl -o zendphpctl
chmod +x /tmp/zendphpctl
mv /tmp/zendphpctl /usr/sbin/zendphpctl
/usr/sbin/zendphpctl ext-install swoole curl
cd /tmp
curl -L https://getcomposer.org/download/latest-2.x/composer.phar -o composer.phar
mv /tmp/composer.phar /usr/sbin/composer
chmod +x /usr/sbin/composer

