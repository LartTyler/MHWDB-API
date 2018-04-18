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
    echo "source scl_source enable rh-mariadb102" > /etc/profile.d/scl.sh

    systemctl start rh-mariadb102-mariadb
    systemctl enable rh-mariadb102-mariadb

    echo "[client]" > /etc/.my.cnf
    echo "user=root" >> /etc/.my.cnf
    echo "database=application" >> /etc/.my.cnf

    yum install -y memcached rh-php71 rh-php71-php rh-php71-php-mysqlnd rh-php71-php-xml rh-php71-php-process sclo-php71-php-pecl-memcached
    echo "source scl_source enable rh-php71" >> /etc/profile.d/scl.sh
  SHELL

  config.vm.provision "shell", name: 'user-init', privileged: false, file: './provision.sh'
end