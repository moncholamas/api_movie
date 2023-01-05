FROM php:8.2.0-apache
LABEL maintainer="moncholamas"

RUN a2enmod rewrite


ENV APACHE_DOCUMENT_ROOT /var/www/html/public

RUN  apt-get update && apt-get install -y ca-certificates gnupg
RUN curl -fsSL https://deb.nodesource.com/setup_16.x | bash -

RUN apt-get update && apt-get upgrade -y && apt-get install -y git nodejs

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

RUN /usr/sbin/a2enmod rewrite && /usr/sbin/a2enmod headers && /usr/sbin/a2enmod expires

RUN apt-get update && apt-get install -y libzip-dev zip && docker-php-ext-install zip

RUN docker-php-ext-install pdo pdo_mysql mysqli

RUN apt-get install -y libtidy-dev && docker-php-ext-install tidy && docker-php-ext-enable tidy

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN pecl install xdebug && docker-php-ext-enable xdebug
RUN echo 'zend_extension=xdebug' >> /usr/local/etc/php/php.ini
RUN echo 'xdebug.mode=develop,debug' >> /usr/local/etc/php/php.ini
RUN echo 'xdebug.client_host=host.docker.internal' >> /usr/local/etc/php/php.ini
RUN echo 'xdebug.start_with_request=yes' >> /usr/local/etc/php/php.ini
RUN echo 'session.save_path = "/tmp"' >> /usr/local/etc/php/php.ini


RUN curl -sL https://deb.nodesource.com/setup_16.x | bash -- \
    && apt-get install -y nodejs \
    && apt-get autoremove -y


COPY --from=composer /usr/bin/composer /usr/bin/composer


ENV COMPOSER_ALLOW_SUPERUSER 1

COPY . /var/www/html
COPY docker/.env.prod /var/www/html/.env
WORKDIR /var/www/html/

RUN chown -R www-data:www-data /var/www/html && composer install
USER www-data