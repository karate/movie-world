# Dockerfile
FROM bitnami/laravel:latest

USER root

# Install SQLite PDO for PHP-FPM
RUN install_packages php-sqlite3

USER 1001
