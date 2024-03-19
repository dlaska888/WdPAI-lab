FROM nginx:1.25.3-alpine3.18

ARG WEB_PORT
ARG MODE
ARG SSL_CERT_PATH

COPY ${SSL_CERT_PATH}/*.key /etc/nginx/cert/default.key
COPY ${SSL_CERT_PATH}/*.crt /etc/nginx/cert/default.crt

COPY ./docker/nginx/php.conf /etc/nginx/

COPY ./docker/nginx/nginx-${MODE}.conf /etc/nginx/templates/default.conf.template
