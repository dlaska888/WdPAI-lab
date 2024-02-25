FROM nginx:1.25.3-alpine3.18

ARG SSL_CERT_PATH
COPY ${SSL_CERT_PATH} /etc/nginx/cert/

COPY ./docker/nginx/nginx.conf /etc/nginx/conf.d/default.conf
