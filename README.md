<p align="center">
    <a href="https://sylius.com" target="_blank">
        <picture>
          <source media="(prefers-color-scheme: dark)" srcset="https://media.sylius.com/sylius-logo-800-dark.png">
          <source media="(prefers-color-scheme: light)" srcset="https://media.sylius.com/sylius-logo-800.png">
          <img alt="Sylius Logo." src="https://media.sylius.com/sylius-logo-800.png">
        </picture>
    </a>
</p>


# Commerz SyliusSwishPlugin
----


# About Us 
---
Commerz is a Swedish tech partner that helps businesses unlock the full potential of their digital commerce. With decades of experience in retail, B2B, logistics, and ERP integrations, we specialize in delivering robust, scalable, and tailor-made digital solutions - from architecture and development to strategy and long-term partnerships.

We work with organizations that require more than just out-of-the-box functionality. Our clients typically operate in complex environments where integrations, business logic, and performance are key. That’s why we often recommend a headless architecture and Sylius has become a natural fit.

As an official Sylius partner, we’ve built advanced solutions including plugins and custom modules that solve real-world challenges. Our team of experienced developers combines deep platform knowledge with a strong focus on business outcomes.



## Table of Content

***

* [Overview](#overview)
* [Installation](#installation)
  * [Requirements](#requirements)
  * [Customization](#customization)
  * [Testing](#testing)
* [Functionalities](#functionalities)
* [Demo](#demo)
* [License](#license)

# Overview

***

This plugin allows you to integrate Przelewy24 payment with the Sylius platform app. It includes all Sylius and Przelewy24 payment features.

# Installation
---
The complete installation guide can be found **[here](doc/installation.md).**


## Requirements

We work on stable, supported, and up-to-date versions of packages. We recommend you to do the same.

| Package       | Version         |
|---------------|-----------------|
| PHP           | \>=8.0          |
| sylius/sylius | 1.12.x - 1.13.x |
| MySQL         | \>= 5.7         |
| NodeJS        | \>= 14.17.x     |

## Customization


Run the below command to see what Symfony services are shared with this plugin:

```
$ bin/console debug:container commerzsylius_swish_plugin
```

## Testing


```
$ composer install
$ cd tests/Application
$ yarn install
$ yarn encore dev
$ symfony console assets:install -e test
$ symfony console doctrine:database:create -e test
$ symfony console doctrine:schema:create -e test
$ symfony console server:start -d --port=8080 -e test
$ open http://localhost:8080
$ cd ../..
$ vendor/bin/behat
$ vendor/bin/phpspec run
```



# Functionalities
---

All main functionalities of the plugin are described **[here](doc/functionalities.md).**

---


# Demo 
---

A demo app with some useful use-cases of plugins! Visit http://demo.sylius.com/ to take a look at it.


# License

---

This plugin's source code is completely free and released under the terms of the MIT license.

---


<h1 align="center">Plugin Skeleton</h1>

<p align="center">Skeleton for starting Sylius plugins.</p>

## Documentation

For a comprehensive guide on Sylius Plugins development please go to Sylius documentation,
there you will find the <a href="https://docs.sylius.com/en/latest/plugin-development-guide/index.html">Plugin Development Guide</a>, that is full of examples.

## Quickstart Installation

Run `composer create-project sylius/plugin-skeleton ProjectName`.

### Traditional

1. From the plugin skeleton root directory, run the following commands:

    ```bash
    $ (cd tests/Application && yarn install)
    $ (cd tests/Application && yarn build)
    $ (cd tests/Application && APP_ENV=test bin/console assets:install public)

    $ (cd tests/Application && APP_ENV=test bin/console doctrine:database:create)
    $ (cd tests/Application && APP_ENV=test bin/console doctrine:schema:create)
    # Optionally load data fixtures
    $ (cd tests/Application && APP_ENV=test bin/console sylius:fixtures:load --no-interaction)
    ```

To be able to set up a plugin's database, remember to configure your database credentials in `tests/Application/.env` and `tests/Application/.env.test`.

2. Run your local server:

      ```bash
      symfony server:ca:install
      APP_ENV=test symfony server:start --dir=tests/Application/public --daemon
      ```

3. Open your browser and navigate to `https://localhost:8000`.

### Docker

1. Execute `docker compose up -d`

2. Initialize plugin `docker compose exec app make init`

3. See your browser `open localhost`

## Usage

### Running plugin tests

  - PHPUnit

    ```bash
    vendor/bin/phpunit
    ```

  - PHPSpec

    ```bash
    vendor/bin/phpspec run
    ```

  - Behat (non-JS scenarios)

    ```bash
    vendor/bin/behat --strict --tags="~@javascript&&~@mink:chromedriver"
    ```

  - Behat (JS scenarios)
 
    1. [Install Symfony CLI command](https://symfony.com/download).
 
    2. Start Headless Chrome:
    
      ```bash
      google-chrome-stable --enable-automation --disable-background-networking --no-default-browser-check --no-first-run --disable-popup-blocking --disable-default-apps --allow-insecure-localhost --disable-translate --disable-extensions --no-sandbox --enable-features=Metal --headless --remote-debugging-port=9222 --window-size=2880,1800 --proxy-server='direct://' --proxy-bypass-list='*' http://127.0.0.1
      ```
    
    3. Install SSL certificates (only once needed) and run test application's webserver on `127.0.0.1:8080`:
    
      ```bash
      symfony server:ca:install
      APP_ENV=test symfony server:start --port=8080 --dir=tests/Application/public --daemon
      ```
    
    4. Run Behat:
    
      ```bash
      vendor/bin/behat --strict --tags="@javascript,@mink:chromedriver"
      ```
    
  - Static Analysis
      
    - PHPStan
    
      ```bash
      vendor/bin/phpstan analyse -c phpstan.neon -l max src/  
      ```

  - Coding Standard
  
    ```bash
    vendor/bin/ecs check
    ```

### Opening Sylius with your plugin

- Using `test` environment:

    ```bash
    (cd tests/Application && APP_ENV=test bin/console sylius:fixtures:load)
    (cd tests/Application && APP_ENV=test bin/console server:run -d public)
    ```
    
- Using `dev` environment:

    ```bash
    (cd tests/Application && APP_ENV=dev bin/console sylius:fixtures:load)
    (cd tests/Application && APP_ENV=dev bin/console server:run -d public)
    ```
