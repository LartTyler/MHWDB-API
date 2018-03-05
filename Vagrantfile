# -*- mode: ruby -*-
# vi: set ft=ruby :

unless Vagrant.has_plugin? "vagrant-vbguest"
    raise "The plugin 'vagrant-vbguest' is required to use this configuration. Please run 'vagrant plugin install vagrant-vbguest', and then try again."
end

Vagrant.configure(2) do |config|
  config.vm.box = "centos/7"

  config.vm.synced_folder ".", "/vagrant", "type": "virtualbox"

  config.vm.network "forwarded_port", guest: 8000, host: 8000

  config.vm.provision "shell", inline: <<-SHELL
    sudo yum update -y
    sudo yum install -y centos-release-scl.noarch vim wget

    sudo yum install -y rh-mariadb102-mariadb rh-mariadb102-mariadb-server
    sudo sh -c 'echo "source scl_source enable rh-mariadb102" >> /etc/profile.d/scl.sh'

    sudo systemctl start rh-mariadb102-mariadb
    sudo systemctl enable rh-mariadb102-mariadb

    sh -lc 'mysql -u root -e "CREATE SCHEMA IF NOT EXISTS application;"'

    sudo sh -c 'echo "[client]" >> ~/.my.cnf'
    sudo sh -c 'echo "user=root" >> ~/.my.cnf'

    sudo yum install -y rh-php71 rh-php71-php rh-php71-php-mysqlnd rh-php71-php-xml
    sudo sh -c 'echo "source scl_source enable rh-php71" >> /etc/profile.d/scl.sh'

    wget https://gist.githubusercontent.com/LartTyler/56966b744b9f60ab050e64091d6296dd/raw/e9192ed8149eeb8698b5c1fc862bb9872fc6faf3/install-composer.sh

    chmod +x install-composer.sh

    mkdir /home/vagrant/bin

    sh -lc './install-composer.sh --install-dir=/home/vagrant/bin --filename=composer'

    echo
    echo "Your box has been provisioned, but there is a few final setup steps you may still need to perform."
    echo
    echo "1. Install the project dependencies using 'composer install -d /vagrant'"
    echo "2. Synchronize the database state using 'php /vagrant/bin/console doctrine:migrations:migrate'"
    echo "3. Start the webserver using 'php /vagrant/bin/console server:start 0.0.0.0'"
    echo
    echo "Once the above steps are complete, you can access the site by visiting 0.0.0.0:8000 on the host machine."
  SHELL
end
