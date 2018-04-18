# -*- mode: ruby -*-
# vi: set ft=ruby :

unless Vagrant.has_plugin? "vagrant-vbguest"
    raise "The plugin 'vagrant-vbguest' is required to use this configuration. Please run 'vagrant plugin install vagrant-vbguest', and then try again."
end

Vagrant.configure(2) do |config|
  config.vm.box = "centos/7"

  config.vm.synced_folder ".", "/vagrant", "type": "virtualbox"

  config.vm.network "forwarded_port", guest: 8000, host: 8000

  config.vm.provision "shell", privileged: true, name: 'system-init', inline: <<-SHELL
    yum update -y
    yum install -y centos-release-scl.noarch vim wget telnet

    yum install -y rh-mariadb102-mariadb rh-mariadb102-mariadb-server
    echo "source scl_source enable rh-mariadb102" > /etc/profile.d/scl.sh'

    systemctl start rh-mariadb102-mariadb
    systemctl enable rh-mariadb102-mariadb

    echo "[client]" > /etc/.my.cnf
    echo "user=root" >> /etc/.my.cnf
    echo "database=application" >> /etc/.my.cnf

    yum install -y memcached rh-php71 rh-php71-php rh-php71-php-mysqlnd rh-php71-php-xml rh-php71-php-process sclo-php71-php-pecl-memcached
    echo "source scl_source enable rh-php71" >> /etc/profile.d/scl.sh

    source "${HOME}/.bashrc"

    wget -q https://gist.githubusercontent.com/LartTyler/56966b744b9f60ab050e64091d6296dd/raw/e9192ed8149eeb8698b5c1fc862bb9872fc6faf3/install-composer.sh -O "${HOME}/install-composer.sh"

    chmod +x "${HOME}/install-composer.sh"

    scl enable rh-php71 '"${HOME}/install-composer.sh" --install-dir=/usr/local/bin --filename=composer'

    rm "${HOME}/install-composer.sh"
  SHELL

  config.vm.provision "shell", name: 'user-init', privileged: false, inline: <<-SHELL
    mysql -e "CREATE SCHEMA IF NOT EXISTS application;"

    cp /vagrant/.env.dist /vagrant/.env

    composer install -qd /vagrant
    /vagrant/db-reset.sh latest

    echo
    echo "Your box has been provisioned. In order to start the webserver, please run 'php /vagrant/bin/console server:start 0.0.0.0'."
    echo
    echo "Please keep in mind that this configuration is NOT suitable for production, and is not secure."
  SHELL
end