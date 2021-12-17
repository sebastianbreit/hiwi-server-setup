FROM php:8.0-apache
# execute the following on the remote
# chmod -R 777 Your-project-directory
RUN chmod -R 777 /var/www/html
# The following does not work since www-data does not exist on the host system, but is the user executing php scripts
#RUN chown -R www-data:www-data /var/www && a2enmod rewrite
