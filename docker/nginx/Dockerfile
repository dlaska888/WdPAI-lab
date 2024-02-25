FROM nginx:1.25.3-alpine3.18

ARG USE_SSL
ARG SSL_CERT_PATH
ARG WEB_PORT

COPY ${SSL_CERT_PATH}/ /etc/nginx/cert/
COPY ./docker/nginx/php.conf /etc/nginx/

COPY ./docker/nginx/nginx${USE_SSL:+-ssl}.conf /etc/nginx/templates/default.conf.template
