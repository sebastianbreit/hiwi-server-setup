FROM php:8.0-apache
COPY ./ /usr/local/apache2/htdocs/
COPY ./ /var/www/html/
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN apt-get update && apt-get upgrade -y