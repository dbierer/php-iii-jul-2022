# Docker Compose Lab
* Create an empty directory and change to it
```
mkdir /path/to/docker_compose_test
cd /path/to/docker_compose_test
```
## Create Files to Build nginx
Create one for the web server, another for PHP-FPM
* Create `Dockerfile.NGINX` as follows:
```
FROM alpine:latest
RUN \
    echo "Installing basic utils ..." && \
    apk add bash
RUN \
    echo "Installing nginx ..." && \
    apk add nginx && \
    mv /etc/nginx/http.d/default.conf /etc/nginx/http.d/default.conf.old
RUN \
    echo "Setting up app base directory ..." && \
    mkdir -p /var/www
COPY compose.nginx.conf /etc/nginx/http.d/default.conf
COPY compose.startup.nginx.sh /usr/sbin/startup.sh
RUN chmod +x /usr/sbin/*.sh
ENTRYPOINT /usr/sbin/startup.sh
```
* Create nginx config file `compose.nginx.conf`
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
          fastcgi_pass      10.20.10.20:9000;
          fastcgi_index     index.php;
          include           fastcgi.conf;
    }
    location ~ ^(.*)$ { }
    location / {
      rewrite ^(.*)$ /index.php break;
    }
}
```
* Create nginx entrypoint startup script `compose.startup.nginx.sh`
```
#!/bin/bash

# Start the first process
/usr/sbin/nginx
status=$?
if [ $status -ne 0 ]; then
  echo "Failed to start nginx: $status"
  exit $status
fi
echo "Started nginx succesfully"

# Naive check runs checks once a minute to see if either of the processes exited.
# This illustrates part of the heavy lifting you need to do if you want to run
# more than one service in a container. The container exits with an error
# if it detects that either of the processes has exited.
# Otherwise it loops forever, waking up every 60 seconds

while sleep 60; do
  ps |grep nginx |grep -v grep
  PROCESS_2_STATUS=$?
  # If the greps above find anything, they exit with 0 status
  # If they are not both 0, then something is wrong
  if [ -f $PROCESS_1_STATUS -o -f $PROCESS_2_STATUS ]; then
    echo "NGINX has already exited."
    exit 1
  fi
done

```

## Create Files to Build php-fpm
* Create `Dockerfile.PHP-FPM` as follows:
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
    echo "Configuring PHP-FPM to listen to nginx ..." && \
    sed -i 's/listen = 127\.0\.0\.1:9000/listen = 0\.0\.0\.0:9000/g' /etc/php8/php-fpm.d/www.conf && \
    echo "listen.allowed_clients = 10.20.10.10" >> /etc/php8/php-fpm.d/www.conf
RUN \
    echo "Setting up app base directory ..." && \
    mkdir -p /var/www
COPY compose.startup.php-fpm.sh /usr/sbin/startup.sh
RUN chmod +x /usr/sbin/*.sh
ENTRYPOINT /usr/sbin/startup.sh
* Create nginx entrypoint startup script `compose.startup.php-fpm.sh`
```
* Create nginx entrypoint startup script `compose.startup.nginx.sh`
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

# Naive check runs checks once a minute to see if either of the processes exited.
# This illustrates part of the heavy lifting you need to do if you want to run
# more than one service in a container. The container exits with an error
# if it detects that either of the processes has exited.
# Otherwise it loops forever, waking up every 60 seconds

while sleep 60; do
  ps |grep php-fpm$VER |grep -v grep
  PROCESS_1_STATUS=$?
  # If the greps above find anything, they exit with 0 status
  # If they are not both 0, then something is wrong
  if [ -f $PROCESS_1_STATUS -o -f $PROCESS_2_STATUS ]; then
    echo "PHP-FPM has already exited."
    exit 1
  fi
done
```
## Create Docker Compose file
Create the file `docker-compose.yml`:
```
version: '3.7'
services:
  web-server:
    container_name: ws-compose
    image: php-iii/nginx-compose
    build:
      context: .
      dockerfile: Dockerfile.NGINX
    volumes:
     - ./html:/var/www/html
    restart: always
    working_dir: /var/www/html
    networks:
      httpd_net:
        ipv4_address: 10.20.10.10
  php-fpm:
    container_name: php-fpm-compose
    image: php-iii/php-fpm-compose
    build:
      context: .
      dockerfile: Dockerfile.PHP-FPM
    volumes:
     - ./html:/var/www/html
    restart: always
    working_dir: /var/www/html
    networks:
      httpd_net:
        ipv4_address: 10.20.10.20
networks:
  httpd_net:
    ipam:
      driver: default
      config:
        - subnet: "10.20.10.0/24"
```
## Download the sample app
The sample app accepts HTTP GET requests.
When you unzip it, it creates the `./html` directory used by both containers.
```
wget https://opensource.unlikelysource.com/post_code_test_app.zip
unzip post_code_test_app.zip
```
## Test
The app (currently) only supports city/states in the USA.
From a browser: `http://10.20.10.10/index.php?city=Potsdam&state=NY`
From the command line:
```
curl -X GET http://10.20.10.10/index.php?city=Potsdam&state=NY
```

## Stop Container
Just hit `CTL+C`
