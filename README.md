# Getting Started
```sh
$ git clone https://github.com/LartTyler/MHWDB-API.git
```

## Using Vagrant
You can use the included Vagrant configuration to run your own development environment. If you're not familiar with
Vagrant, check out their [getting started](https://www.vagrantup.com/intro/index.html) guide.

Simply navigate to the project root and run `vagrant up`. Once the virtual machine has been created and has booted,
you can use `vagrant ssh` to SSH into the box and run `/vagrant/server-start.sh` to start the web server (which will
listen on `0.0.0.0:8000` by default).

## Manual Setup
Support is not provided for any development environments that are set up manually. If you open an issue, I'll try to
help out as much as possible, but I can't promise anything. If you'd like your devleopment environment to be supported,
please use the [Vagrant configuration](#using-vagrant).

The project requires the following software in order to run:

- PHP 7.1 or higher
- Composer
- Memcached
- MySQL 5.7 or higher OR MariaDB 10.2 or higher

After ensuring that the above packages are available, copy the `.env.dist` file to `.env` and modify it to your
machine's configuration. Once you've done that, run the following commands to install the project requirements and start
the web server.

```sh
$ ./db-reset.sh latest <dbname>
$ composer install
$ php bin/console server:start
```

The `<dbname>` placeholder should be replaced with the name of the database you'll be using for the project.