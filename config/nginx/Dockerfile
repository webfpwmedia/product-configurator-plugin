FROM nginx:1.14

LABEL maintainer="Kevin DeCapite"

WORKDIR /srv/app/

RUN mkdir logs

COPY /config/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY ./webroot /srv/app/webroot
