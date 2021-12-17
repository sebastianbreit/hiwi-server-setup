FROM php:8.0-apache
RUN chmod -R 777 /var/www/html
# The following does not work since www-data does not exist on the host system, but is the user executing php scripts
#RUN chown -R www-data:www-data /var/www && a2enmod rewrite
