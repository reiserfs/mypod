FROM ghcr.io/reiserfs/mypod/custom-8.4-fpm-alpine:latest

WORKDIR /app

# Copia o código da aplicação
COPY ./src/ ./

# Instala dependências do projeto
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Ajusta permissões
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 9000
CMD ["/bin/sh", "-c", "if [ -z \"$(ls -A /var/www/html)\" ]; then mv /app/* /var/www/html/; fi && php-fpm"]
