FROM php:8.2-apache

# Install MySQL extension
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Install GD library for image processing
RUN apt-get update && apt-get install -y \
	libfreetype6-dev \
	libjpeg62-turbo-dev \
	libpng-dev \
	&& docker-php-ext-configure gd --with-freetype --with-jpeg \
	&& docker-php-ext-install -j$(nproc) gd

# Configure PHP upload limits
RUN echo "upload_max_filesize = 5M" > /usr/local/etc/php/conf.d/uploads.ini \
	&& echo "post_max_size = 10M" >> /usr/local/etc/php/conf.d/uploads.ini \
	&& echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy Apache configuration
COPY docker/apache-config.conf /etc/apache2/sites-available/000-default.conf

# Set permissions
RUN chown -R www-data:www-data /var/www/html