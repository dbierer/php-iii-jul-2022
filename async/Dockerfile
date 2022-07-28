ARG OS=ubuntu
ARG OS_VERSION=20.04
ARG ZENDPHP_VERSION=8.1
ARG BASE_IMAGE=cli
FROM cr.zend.com/zendphp/${ZENDPHP_VERSION}:${OS}-${OS_VERSION}-${BASE_IMAGE}
ARG TIMEZONE=UTC
ARG INSTALL_COMPOSER=
ARG SYSTEM_PACKAGES=
ARG ZEND_EXTENSIONS_LIST=
ARG PECL_EXTENSIONS_LIST=
ARG POST_BUILD_BASH=/usr/local/sbin/finish_install.sh
ENV TZ=$TIMEZONE
ENV YUM_y='-y'
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
COPY finish_install.sh /usr/local/sbin/finish_install.sh
COPY startup.sh /usr/local/sbin/startup.sh
RUN chmod +x /usr/local/sbin/*.sh
RUN ZendPHPCustomizeWithBuildArgs.sh
ENTRYPOINT /usr/local/sbin/startup.sh
