FROM php:8.2-apache

# Instalar extensão PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Corrigir conflito de MPM: desabilitar mpm_event, habilitar mpm_prefork
RUN a2dismod mpm_event || true \
    && a2enmod mpm_prefork \
    && a2enmod rewrite

# Configurar Apache para permitir .htaccess
RUN echo '<Directory /var/www/html>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>' > /etc/apache2/conf-available/project.conf \
    && a2enconf project

# Copiar projeto para o Apache
COPY . /var/www/html/

# Permissões
RUN chown -R www-data:www-data /var/www/html

# Configure Apache to listen on Railway's PORT (default 80 for local)
RUN sed -i 's/Listen 80/Listen ${PORT}/g' /etc/apache2/ports.conf \
    && sed -i 's/:80/:${PORT}/g' /etc/apache2/sites-available/000-default.conf

CMD ["sh", "-c", "exec apache2-foreground"]
