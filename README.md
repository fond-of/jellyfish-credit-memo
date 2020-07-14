# JellyfishCreditMemo Extension Module
[![Build Status](https://travis-ci.org/fond-of/spryker-jellyfish-credit-memo.svg?branch=master)](https://travis-ci.org/fond-of/spryker-jellyfish-credit-memo)
[![PHP from Travis config](https://img.shields.io/travis/php-v/symfony/symfony.svg)](https://php.net/)
[![license](https://img.shields.io/github/license/mashape/apistatus.svg)](https://packagist.org/packages/fond-of-spryker/jellyfish-credit-memo)

## Installation

```
composer require fond-of-spryker/jellyfish-credit-memo
```

## Configuration

Inject JellyfishCreditMemo command and condition into OMS. Add in config_default.php

```
$config[KernelConstants::DEPENDENCY_INJECTOR_ZED] = [
    'Payment' => [
        ...
    ],
    'Oms' => [
        ...
        'JellyfishCreditMemo',
    ],
];
```
