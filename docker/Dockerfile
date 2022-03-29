FROM ubuntu:20.04

LABEL maintainer="nikozeel@icloud.com"

RUN export DEBIAN_FRONTEND="noninteractive" \
    && apt-get update \
    && apt-get upgrade -y \
    && apt-get install -y locales && locale-gen en_US.UTF-8 \
    && apt-get install git nginx -y \
    && apt-get install vim ca-certificates -y \
    && apt-get install php7.4-amqp php7.4-fpm php7.4-curl php7.4-dom php7.4-pgsql php7.4-zip php7.4-intl php7.4-mbstring -y \
    && apt-get install curl gnupg2 unzip supervisor -y

ENV LANG en_US.UTF-8
ENV LANGUAGE en_US:en
ENV LC_ALL en_US.UTF-8

RUN ln -snf /usr/share/zoneinfo/Europe/Moscow /etc/localtime && echo "Europe/Moscow"  > /etc/timezone

RUN adduser --disabled-password --gecos '' project
RUN mkdir /var/www/BookProject
RUN echo "cd /var/www/BookProject" >> /home/project/.bashrc

# RUN echo 'deb [trusted=yes] https://repo.symfony.com/apt/ /' | tee /etc/apt/sources.list.d/symfony-cli.list

# RUN apt update && apt install symfony-cli

WORKDIR /tmp
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === '906a84df04cea2aa72f40b5f787e49f22d4c2f19492ac310e8cba5b96ac8b64115ac402c8cd292b8a03482574915d1a8') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

USER root
COPY entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
RUN touch /CONTAINER_FIRST_STARTUP

CMD ["/usr/local/bin/entrypoint.sh"]