FROM wodby/drupal-php:$PHP_TAG
WORKDIR /var/www/html

RUN apt-get update && \
  apt-get upgrade -y &&
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | -E bash
RUN apt install symfony-cli
