FROM nginx:1.27.2-alpine

RUN apk update && apk upgrade
RUN apk add bash; \
    apk add vim; \
    apk add nano; \
    apk add htop; \
    apk add curl


# --------------------------------
# PHP
# --------------------------------
RUN apk add php83; \
    apk add php83-fpm; \
    apk add php83-phar; \
    apk add php83-iconv; \
    apk add php83-mbstring; \
    apk add php83-openssl; \
    apk add php83-session; \
    apk add php83-curl; \
    apk add php83-dom; \
    apk add php83-tokenizer; \
    apk add php83-xml; \
    apk add php83-simplexml; \
    apk add php83-sodium; \
    apk add php83-xmlreader; \
    apk add php83-xmlwriter; \
    apk add php83-fileinfo; \
    apk add php83-gd; \
    apk add php83-zip; \
    apk add php83-pdo_mysql

RUN curl -s https://getcomposer.org/composer.phar > /usr/local/bin/composer && chmod a+x /usr/local/bin/composer
# --------------------------------


# --------------------------------
# NGINX
# --------------------------------
COPY docker/services/nginx/default.conf /etc/nginx/conf.d/default.conf
RUN sed -i "s|user  nginx;||g" /etc/nginx/nginx.conf
# --------------------------------


# --------------------------------
# www-data USER
# --------------------------------
RUN mkdir -p /var/www/html; \
    adduser -u 82 -D -S -G www-data www-data

COPY --chown=www-data:www-data . /var/www/html/

WORKDIR /var/www/html
# --------------------------------


# --------------------------------
# COMPOSER
# --------------------------------
RUN composer install -vvv --prefer-dist --no-progress
# --------------------------------


# --------------------------------
# PERMISSIONS AND FILES
# --------------------------------
COPY docker/docker-entrypoint.sh /docker-entrypoint.d/40-docker-entrypoint.sh
RUN ["chmod", "+x", "/docker-entrypoint.d/40-docker-entrypoint.sh"]
RUN chown www-data:www-data /docker-entrypoint.d/ /var/log/php83 /var/log/nginx /var/run; \
    chown -R www-data:www-data /var/www/ /var/cache/nginx/
# --------------------------------


USER www-data
