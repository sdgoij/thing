#!/usr/bin/env bash

echo "deb http://archive.ubuntu.com/ubuntu trusty main universe" > /etc/apt/sources.list && \
	apt-get update && \
	apt-get -y dist-upgrade

echo "deb http://ppa.launchpad.net/ondrej/php5-5.6/ubuntu trusty main" >> /etc/apt/sources.list && \
	apt-key adv --keyserver keyserver.ubuntu.com --recv-key E5267A6C && \
	apt-get update

apt-get -y install git php5-cli php5-sqlite php5-mcrypt
apt-get clean && rm -rf /var/lib/apt/lists/*

EXEC_CMD="php -S 0.0.0.0:80 -t /vagrant/public /vagrant/public/index.php"

test -f /vagrant/composer.phar || php -r "readfile('https://getcomposer.org/installer');"|php

cd /vagrant; php composer.phar self-update && php composer.phar install

test -d /vagrant/data || mkdir /vagrant/data
test -f /vagrant/config/autoload/default.local.php || \
	cp /vagrant/config/autoload/default.local.php.dist /vagrant/config/autoload/default.local.php
test -f /vagrant/data/thing.db || /vagrant/vendor/bin/doctrine-module orm:schema-tool:create

echo "start on started networking" > /etc/init/thing.conf
echo "stop on runlevel [016]"     >> /etc/init/thing.conf
echo "respawn"                    >> /etc/init/thing.conf
echo "respawn limit 5 30"         >> /etc/init/thing.conf
echo "exec ${EXEC_CMD}"           >> /etc/init/thing.conf

start thing
