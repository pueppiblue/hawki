FROM php:8.3-apache-bookworm

RUN apt-get update && apt-get install --no-install-recommends -y \
    libldap2-dev

RUN \
    docker-php-ext-configure ldap  && \
    docker-php-ext-install ldap  && \
    docker-php-ext-enable  ldap


RUN a2enmod ssl && a2enmod rewrite
RUN mkdir -p /etc/apache2/ssl
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

#COPY ./ssl/*.pem /etc/apache2/ssl/
COPY .docker/etc/apache2/sites-available /etc/apache2/sites-available

EXPOSE 80
EXPOSE 443