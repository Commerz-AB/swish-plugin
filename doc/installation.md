<<# Installation

## Overview:
GENERAL
- [Requirements](#requirements)
- [Composer](#composer)
- [Basic configuration](#basic-configuration)
---
ADDITIONAL
- [Known Issues](#known-issues)
---

## Requirements:
We work on stable, supported and up-to-date versions of packages. We recommend you to do the same.

| Package       | Version         |
|---------------|-----------------|
| PHP           | \>=8.0          |
| sylius/sylius | 1.12.x - 1.13.x |
| MySQL         | \>= 5.7         |
| NodeJS        | \>= 14.17.x     |

## Composer:
```bash
composer require commerz/swish-plugin
```

## Basic configuration:
Add plugin dependencies to your `config/bundles.php` file:

```php
# config/bundles.php

return [
    ...
    Commerz\SyliusSwishPlugin\CommerzSwishPlugin::class => ['all' => true],
];
```

Add route in your `config/routes/sylius_shop.yaml` file:
```yaml
...
# Commerzsylius\SwishPlugin\CommerzsyliusSwishPlugin
commerz_swish_shop:
    resource: "@CommerzsyliusSwishPlugin/Resources/config/routing/swish_shop.yaml"
```

## Known issues
### Translations not displaying correctly
For incorrectly displayed translations, execute the command:
```bash
bin/console cache:clear
```>>