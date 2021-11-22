FROM php:8.0-apache
COPY ./ /usr/local/apache2/htdocs/
# Following line should not be needed, saving should be done to database
# RUN chmod 777 /usr/local/apache2/htdocs/
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN apt-get update && apt-get upgrade -y