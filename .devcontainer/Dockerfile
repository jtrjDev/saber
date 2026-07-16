FROM mcr.microsoft.com/devcontainers/php:1-8.3-bookworm

USER root

RUN rm -f /etc/apt/sources.list.d/yarn.list \
    && apt-get update \
    && apt-get install -y --no-install-recommends \
        default-mysql-client \
        git-lfs \
    && docker-php-ext-install pdo_mysql \
    && git lfs install --system \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*