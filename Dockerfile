# Builds this fork on top of the official stable image, so the runtime (nginx,
# php-fpm, entrypoint, vendor/) is exactly what upstream ships and only the
# source tree and the compiled front-end assets are replaced.
#
# Keep FROM in sync with config/firefly.php 'version'.

FROM node:24-alpine AS assets

WORKDIR /build
COPY package.json package-lock.json ./
COPY resources/assets/v3 ./resources/assets/v3
RUN npm ci
RUN npm run build --workspace=v3

FROM fireflyiii/core:version-6.7.2

COPY --chown=www-data:www-data . /var/www/html
COPY --chown=www-data:www-data --from=assets /build/public/build /var/www/html/public/build
