#!/usr/bin/env bash

# Repository to pull from
REPO=https://github.com/BCLibraries/downtime-notices

# Application directory
APP_BASE=/apps/downtime-notices

# The new release
TODAY=`date +%Y-%m-%d-%H%M%S`
NEW_RELEASE=${APP_BASE}/releases/${TODAY}

# Shared directories
SHARED_DIR=${APP_BASE}/shared
LOG_DIR=${SHARED_DIR}/log

# Pull the latest commit from master
git clone ${REPO} ${NEW_RELEASE}
cd ${NEW_RELEASE}
find .git -type f -exec chmod 644 {} \;

# Load the local environment variables
cp ${SHARED_DIR}/.env.local ${NEW_RELEASE}

# Install
APP_ENV=prod composer install --no-dev --optimize-autoloader

# Build assets (javascript, CSS, images, etc.)
yarn install
yarn build

# Re-use existing log
rm -r ${NEW_RELEASE}/var/log
ln -s ${LOG_DIR} ${NEW_RELEASE}/var/log

# Run database migrations
php bin/console doctrine:migrations:migrate

# Replace old version with new version
unlink ${APP_BASE}/current
ln -s ${NEW_RELEASE} ${APP_BASE}/current