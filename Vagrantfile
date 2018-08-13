# -*- mode: ruby -*-
# vi: set ft=ruby :

unless Vagrant.has_plugin? "vagrant-vbguest"
    raise "The plugin 'vagrant-vbguest' is required to use this configuration. Please run 'vagrant plugin install vagrant-vbguest', and then try again."
end

Vagrant.configure(2) do |config|
  config.vm.box = "centos/7"

  config.vm.provider "virtualbox" do |vb|
    vb.memory = "2048"
  end

  config.vm.synced_folder ".", "/vagrant", "type": "virtualbox"

  config.vm.network "forwarded_port", guest: 8000, host: 8000

  config.vm.provision "shell", privileged: true, name: 'system-init', inline: <<-SHELL
    yum update -y
    yum install -y centos-release-scl.noarch vim wget telnet ntpd

    systemctl enable ntpd
    ntpdate pool.ntp.org

    yum install -y rh-mariadb102-mariadb rh-mariadb102-mariadb-server
    echo "source scl_source enable rh-mariadb102" > /etc/profile.d/scl.sh

    systemctl start rh-mariadb102-mariadb
    systemctl enable rh-mariadb102-mariadb

    yum install -y memcached rh-php71 rh-php71-php rh-php71-php-mysqlnd rh-php71-php-xml rh-php71-php-process sclo-php71-php-pecl-memcached sclo-php71-php-pecl-xdebug
    echo "source scl_source enable rh-php71" >> /etc/profile.d/scl.sh

    if if grep -Fqvx "xdebug.remote_enable" /etc/opt/rh/rh-php71/php.d/15-xdebug.ini; then
      echo "xdebug.remote_enable = on" >> /etc/opt/rh/rh-php71/php.d/15-xdebug.ini
      echo "xdebug.remote_connect_back = on" >> /etc/opt/rh/rh-php71/php.d/15-xdebug.ini
      echo "xdebug.idekey = application" >> /etc/opt/rh/rh-php71/php.d/15-xdebug.ini
      echo "xdebug.remote_autostart = on" >> /etc/opt/rh/rh-php71/php.d/15-xdebug.ini
      echo "xdebug.remote_host = 10.0.2.2" >> /etc/opt/rh/rh-php71/php.d/15-xdebug.ini
    fi
  SHELL

  config.vm.provision "shell", name: 'user-init', privileged: false, path: './vagrant/provision.sh'
end