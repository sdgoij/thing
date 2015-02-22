FROM ubuntu:14.04

ENV DEBIAN_FRONTEND noninteractive

RUN echo "deb http://archive.ubuntu.com/ubuntu trusty main universe" > /etc/apt/sources.list && \
    apt-get update && \
    apt-get -y dist-upgrade

RUN echo "deb http://ppa.launchpad.net/ondrej/php5-5.6/ubuntu trusty main" >> /etc/apt/sources.list && \
    apt-key adv --keyserver keyserver.ubuntu.com --recv-key E5267A6C && \
    apt-get update

RUN apt-get -y install git php5-cli php5-sqlite php5-mcrypt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

ADD . /src

RUN cd /src; php -r "readfile('https://getcomposer.org/installer');"|php
RUN cd /src; php composer.phar self-update && php composer.phar install && \
    cp config/autoload/default.local.php.dist config/autoload/default.local.php && \
    mkdir data && ./vendor/bin/doctrine-module orm:schema-tool:create

CMD ["php", "-S", "0.0.0.0:80", "-t", "/src/public/", "/src/public/index.php"]

EXPOSE 80
