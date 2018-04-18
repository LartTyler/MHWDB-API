#!/usr/bin/env bash

echo "[client]" > "${HOME}/.my.cnf"
echo "user=root" >> "${HOME}/.my.cnf"
echo "[mysql]" >> "${HOME}/.my.cnf"
echo "database=application" >> "${HOME}/.my.cnf"

wget -qO "${HOME}/install-composer.sh" https://gist.githubusercontent.com/LartTyler/56966b744b9f60ab050e64091d6296dd/raw/e9192ed8149eeb8698b5c1fc862bb9872fc6faf3/install-composer.sh
chmod +x "${HOME}/install-composer.sh"

mkdir -p "${HOME}/bin"

"${HOME}/install-composer.sh" --install-dir="${HOME}/bin" --filename=composer
rm "${HOME}/install-composer.sh"

mysql -e "CREATE SCHEMA IF NOT EXISTS application;"

cp /vagrant/.env.dist /vagrant/.env

composer install -qd /vagrant
/vagrant/db-reset.sh latest

echo
echo "Your box has been provisioned. In order to start the webserver, please run 'php /vagrant/bin/console server:start 0.0.0.0'."
echo
echo "Please keep in mind that this configuration is NOT suitable for production, and is not secure."