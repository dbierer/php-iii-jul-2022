# Docker Build Lab
* Create an empty directory and change to it
```
mkdir /path/to/docker_test
cd /path/to/docker_test
```
* Create `Dockerfile` as follows:
```
FROM alpine:latest
RUN \
    echo "Installing basic utils ..." && \
    apk add bash
RUN \
    echo "Installing PHP + PHP-FPM ..." && \
    apk add php && \
    apk add php-fpm
RUN \
    echo "Installing nginx ..." && \
    apk add nginx && \
    mv /etc/nginx/http.d/default.conf /etc/nginx/http.d/default.conf.old && \
    mkdir /var/www/html && \
    chown -R nginx /var/www
COPY default.conf /etc/nginx/http.d/default.conf
COPY index.php /var/www/html/index.php
COPY startup.sh /usr/sbin/startup.sh
RUN chmod +x /usr/sbin/*.sh
ENTRYPOINT /usr/sbin/startup.sh
```
* Create nginx config file `default.conf` :
```
server {
    listen                  80;
    root                    /var/www/html;
    index                   index.php;
    server_name             _;
    client_max_body_size    32m;
    error_page              500 502 503 504  /50x.html;
    location = /50x.html {
          root              /var/lib/nginx/html;
    }
    location ~ \.php$ {
          fastcgi_pass      127.0.0.1:9000;
          fastcgi_index     index.php;
          include           fastcgi.conf;
    }
}
```
* Create entry point startup script `startup.sh`
  * Naive check runs checks once a minute to see if either of the processes exited. This illustrates part of the heavy lifting you need to do if you want to run more than one service in a container. The container exits with an error if it detects that either of the processes has exited. Otherwise it loops forever, waking up every 60 seconds.
```
#!/bin/bash

export VER=8
# Start the first process
/usr/sbin/php-fpm$VER
status=$?
if [ $status -ne 0 ]; then
  echo "Failed to start php-fpm: $status"
  exit $status
fi
echo "Started php-fpm succesfully"

# Start the second process
/usr/sbin/nginx
status=$?
if [ $status -ne 0 ]; then
  echo "Failed to start nginx: $status"
  exit $status
fi
echo "Started nginx succesfully"

while sleep 60; do
  ps |grep php-fpm$VER |grep -v grep
  PROCESS_1_STATUS=$?
  ps |grep nginx |grep -v grep
  PROCESS_2_STATUS=$?
  # If the greps above find anything, they exit with 0 status
  # If they are not both 0, then something is wrong
  if [ -f $PROCESS_1_STATUS -o -f $PROCESS_2_STATUS ]; then
    echo "One of the processes has already exited."
    exit 1
  fi
done
```
* Create test PHP script `index.php`:
```
<?php
phpinfo();
```
* Build the image:
```
docker build -t docker_test .
```
* Run the image:
```
docker run -d -p 8008:80 --name docker_test_run docker_test
```
* Confirm it's running:
```
docker container ls
```
* Test from browser: `http://localhost:8008/index.php`
* Shell into the image and run a few commands:
```
docker exec -it docker_test_run /bin/bash
# php -v
# ps
# exit
```
* Stop the container:
```
docker container stop docker_test_run
```
IMPORTANT: 
If you run the same container twice and use the `--name`, you will get this message:
```
docker: Error response from daemon: Conflict. The container name "/docker_test_run" is already in use by container
```
Do the following to resolve the conflict:
```
docker container stop docker_test_run
docker container rm docker_test_run
```

