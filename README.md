# Opotager - API / Back Office

This project is a collaborative platform centered around gardening. It enables two users to connect, one to offer their land, and the other to cultivate crops.

## Introduction

Welcome to the back-end part, entirely built using the PHP programming language and its Symfony 5.4 framework. It includes an API intended to be consumed by the front-end and a back office to manage the data.

## Table of contents

- [Opotager - API / Back Office](#opotager---api--back-office)
  - [Introduction](#introduction)
  - [Table of contents](#table-of-contents)
  - [Prerequisites](#prerequisites)
  - [Getting Started](#getting-started)
    - [Installing Composer (if not installed on your machine)](#installing-composer-if-not-installed-on-your-machine)
    - [Installation](#installation)
    - [Configuration](#configuration)
      - [Database (BDD)](#database-bdd)
      - [Unsplash API Key](#unsplash-api-key)
    - [Creating the Database](#creating-the-database)
      - [Creating Tables and Fields](#creating-tables-and-fields)
      - [Generating Fixtures](#generating-fixtures)
    - [Launching the Server](#launching-the-server)
  - [Learn More](#learn-more)
    - [Creating an Unsplash Account](#creating-an-unsplash-account)
    - [Documentation](#documentation)

## Prerequisites

- MariaDB
- PHP 7.4 ([https://www.php.net/](https://www.php.net/))
- Composer ([https://getcomposer.org/download/](https://getcomposer.org/download/))
- UNSPLASH Account ([https://unsplash.com/fr](https://unsplash.com/fr))

## Getting Started

### Installing Composer (if not installed on your machine)

To install Composer, follow the [documentation](https://getcomposer.org/download/).

### Installation

1. Clone the repository.
2. Navigate to the project's root.
3. Run the command composer update.

### Configuration

Create a `.env.local` file at the root of the project.

#### Database (BDD)

In this file, copy the following line:

```php
###> doctrine/doctrine-bundle ###
DATABASE_URL="mysql://monNom:monMdp@127.0.0.1:3306/opotager?serverVersion=mariadb-10.3.38&charset=utf8mb4"
###< doctrine/doctrine-bundle ###
```

Replace the following with your information:

- myUsername -> your database username
- myPassword -> your database password
- Check your MariaDB version. If it's not 10.3.38, replace it with your version.

> To check your MariaDB version, you can use the command `mariadb --version` in your terminal.

#### Unsplash API Key

To generate images via the Unsplash API, obtain your API key and follow these steps. (If you don't have an account, follow these [steps](#creating-an-unsplash-account)).

```php
###> unsplashApiService ###
unsplash_KEY=unsplash_KEY= maCléApi
###< unsplashApiService ###
```

### Creating the Database

If everything is configured correctly, you can run the following command:

```shell
php bin/console doctrine:database:create
```

#### Creating Tables and Fields

To create the tables, execute the following commands:

```shell
php bin/console doctrine:make:migration

php bin/console doctrine:migrations:migrate
```

#### Generating Fixtures

Generating fixtures (false data) provides a development environment with a similar setup to production.

To generate them, use the following command:

```shell
php bin/console doctrine:fixtures:load
```

### Launching the Server

Enter the following command in your terminal at the project's root:

```shell
php -S localhost:8000 -t public
```

Open your localhost: `localhost:8000/` if you're on port 8000.

## Learn More

### Creating an Unsplash Account

Create your account on the [Unsplash](https://unsplash.com/fr/join) website and provide your information.

Once logged in:

- Click on the menu, then in the product section.
- Click on 'Developers / API'.
- Go to 'Your Apps'.
- 'New Application' -> Enter all the requested information and validate.
- You should find your API key lower on the page under Keys -> Access Key.

### Documentation

You can find the Symfony documentation [here](https://symfony.com/).
