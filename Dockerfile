FROM git.silvain.eu/silvain.eu/monsieurpatatephp:latest AS composer
WORKDIR /app

COPY bin/ /app/bin/
COPY config/ /app/config/
COPY public/ /app/public/
COPY .env /app/.env
COPY var/ /app/var/
COPY src/ /app/src/
COPY composer.json /app/composer.json
COPY composer.lock /app/composer.lock

RUN composer install --no-dev --optimize-autoloader


FROM node:14-alpine AS yarn
WORKDIR /app

COPY .env /app/.env
COPY vite.config.js /app/vite.config.js
COPY assets/ /app/assets/
COPY package.json /app/package.json
COPY yarn.lock /app/yarn.lock
COPY public/ /app/public/
COPY --from=composer /app/vendor/ /app/vendor/

RUN yarn install && yarn build

FROM git.silvain.eu/silvain.eu/monsieurpatatephp:latest
#abe08be895985ea57157967c8a6faa963c9d4d47
WORKDIR /app

COPY bin/ /app/bin/
COPY config/ /app/config/
COPY --from=composer /app/public/ /app/public/
COPY src/ /app/src/
COPY templates/ /app/templates/
COPY translations/ /app/translations/
COPY var/ /app/var/
COPY --from=composer /app/vendor/ /app/vendor/
COPY --from=yarn /app/public/build /app/public/build/
COPY .env /app/.env

RUN chmod 777 -R /app/var
RUN touch /app/composer.json

ENTRYPOINT ["/usr/local/bin/docker-php-entrypoint", "php-fpm"]