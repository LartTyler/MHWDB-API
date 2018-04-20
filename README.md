# Getting Started
Check out the  [contributing guide](.github/CONTRIBUTING.md) for information on contributing to the project.

```sh
$ git clone https://github.com/LartTyler/MHWDB-API.git
```

## Using Vagrant
You can use the included Vagrant configuration to run your own development environment. If you're not familiar with
Vagrant, check out their [getting started](https://www.vagrantup.com/intro/index.html) guide.

In the project root, run:

```sh
$ vagrant up
```

Once the box is done provisioning, use `vagrant ssh` to access the box and run:

```sh
$ cd /vagrant
$ composer install
$ ./db-reset.sh latest
$ ./server-start.sh
```

The commands, in order, will perform the following tasks:
- 1 and 2: Navigate to the project root and install dependencies
- 3: Sync the boxes database with the most recent SQL file in the `snapshots/` directory
- 4: Start the webserver, which will make the API available on `127.0.0.1:8000`

## Manual Setup
Support is not provided for any development environments that are set up manually. If you open an issue, I'll try to
help out as much as possible, but I can't promise anything. If you'd like your devleopment environment to be supported,
please use the [Vagrant configuration](#using-vagrant).

The project requires the following software in order to run:

- PHP 7.1 or higher
- Composer
- Memcached
- MySQL 5.7 or higher OR MariaDB 10.2 or higher

After ensuring that the above packages are available, copy the `.env.dist` file to `.env` and modify it to match your
machine's configuration. Once you've done that, run the following commands to install the project requirements and start
the web server.

```sh
$ ./db-reset.sh latest <dbname>
$ composer install
$ php bin/console server:start
```

Replace `<dbname>`  with the name of the database you'll be using for the project.