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

<b>The Swish Payment Plugin by Commerz</b> provides seamless Swish payment integration for your Sylius eCommerce platform, enabling merchants to accept real-time mobile payments directly from customers’ banking apps. With a direct connection to Swish, no intermediaries the plugin ensures fast, secure, and reliable transactions. Designed for simplicity and performance, this plugin is easy to install, manage, and maintain, making it an ideal solution for merchants targeting the Swedish market.

# Installation

The complete installation guide can be found **[here](doc/installation.md).**

## Requirements

To use the Swish Payment Plugin by Commerz, you must have:

* An active Swish for Merchants agreement with your bank
* Issued Swish certificates for production use (provided by Swish upon agreement setup)

These credentials are mandatory for enabling the connection between your Sylius store and Swish's payment infrastructure.

To get started with Swish, please contact your bank or visit Swish's official website for more information:<br />
**https://www.getswish.se/foretag/** (in Swedish)

A quick rundown on using/creating Swish-certificates **<a href="https://www.commerz.se/artiklar/hur-du-skapar-och-installerar-swish-certifikat-for-produktion/" target="_blank">is published here</a>** (in Swedish).


We work on stable, supported, and up-to-date versions of packages. We recommend you to do the same.

| Package       | Version         |
|---------------|-----------------|
| PHP           | \>=8.0          |
| Sylius        | 1.12.x - 1.13.x |
| MySQL         | \>= 5.7         |
| NodeJS        | \>= 14.17.x     |

## Customization

Run the below command to see what Symfony services are shared with this plugin:

```
$ bin/console debug:container commerzsylius_swish_plugin
```

Run the below command to see custom routes created for this plugin:

```
$ bin/console debug:route commerzsylius_swish_plugin
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
