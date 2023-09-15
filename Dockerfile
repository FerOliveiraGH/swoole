FROM php:8.0-cli

WORKDIR /var/www

RUN apt update -y && apt install git -y && apt install libssl-dev -y
RUN printf "no\nyes\nno\nno\n" | pecl install swoole && docker-php-ext-enable swoole
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer

#EXPOSE 8080

#ENTRYPOINT ["php", "/var/www/index.php", "start"]