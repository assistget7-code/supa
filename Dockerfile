FROM php:8.2-apache

# Install cURL for Supabase API calls
RUN apt-get update && apt-get install -y curl libcurl4-openssl-dev && docker-php-ext-install curl

# Copy all project files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html/ && chmod -R 755 /var/www/html/

# Enable Apache mod_rewrite (optional)
RUN a2enmod rewrite

EXPOSE 80
