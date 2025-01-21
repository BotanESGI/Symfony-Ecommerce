FROM chialab/php-dev:8.3-fpm-alpine

# Install symfony cli and composer

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN apk update && apk add bash

RUN wget https://get.symfony.com/cli/installer -O - | bash && \
    mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

# Run the server

WORKDIR /app

CMD symfony server:start --port=8000 --dir=./public ----listen-ip=0.0.0.0

# Expose the port

EXPOSE 8000
