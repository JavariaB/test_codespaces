

FROM php:7.3-apache

RUN docker-php-ext-install mysqli


RUN apt-get update && apt-get install -y default-mysql-client

COPY . /var/www/html

EXPOSE 80

ENV DB_HOST=host.docker.internal \
    DB_USER=root \
    DB_PASSWORD=  \
    DB_NAME=krankencare

CMD ["apache2-foreground"]

