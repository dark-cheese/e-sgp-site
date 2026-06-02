FROM php:8.2-apache

# Instalar extensão PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar projeto para o Apache
COPY . /var/www/html/

# Configurar Apache para permitir .htaccess
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/project.conf \
    && a2enconf project

# Permissões
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
