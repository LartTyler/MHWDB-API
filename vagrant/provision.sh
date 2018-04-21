#!/usr/bin/env bash

echo "[client]" > "${HOME}/.my.cnf"
echo "user=root" >> "${HOME}/.my.cnf"

mysql -e "CREATE SCHEMA IF NOT EXISTS application;"

echo "[mysql]" >> "${HOME}/.my.cnf"
echo "database=application" >> "${HOME}/.my.cnf"

wget -qO "${HOME}/install-composer.sh" https://gist.githubusercontent.com/LartTyler/56966b744b9f60ab050e64091d6296dd/raw/e9192ed8149eeb8698b5c1fc862bb9872fc6faf3/install-composer.sh
chmod +x "${HOME}/install-composer.sh"

mkdir -p "${HOME}/bin"

"${HOME}/install-composer.sh" --install-dir="${HOME}/bin" --filename=composer
rm "${HOME}/install-composer.sh"

cp /vagrant/.env.dist /vagrant/.env

echo
echo "Your box has been provisioned. Please refer to the README for any remaining setup instructions."
echo
echo "Please keep in mind that this configuration is NOT suitable for production."