# Imagen base oficial con Apache y PHP 8.4
FROM php:8.4-apache

# Instalar extensiones necesarias para la base de datos
RUN apt-get update
RUN apt-get install -y libpng-dev git libjpeg-dev libfreetype6-dev libzip-dev zip unzip
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd mysqli pdo pdo_mysql
RUN docker-php-ext-enable mysqli pdo pdo_mysql
RUN a2enmod rewrite

# Copiar la aplicación Draftosaurus al contenedor
COPY --chown=www-data:www-data ./app /var/www/html/app
COPY --chown=www-data:www-data ./public /var/www/html/public
COPY ./000-default.conf /etc/apache2/sites-available/000-default.conf
# Establecer permisos correctos

# Directorio de trabajo de Apache
WORKDIR /var/www/html

# Exponer el puerto HTTP

EXPOSE 80
CMD ["apache2-foreground"]
