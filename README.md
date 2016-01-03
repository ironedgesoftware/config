# Config

[![Build Status](https://travis-ci.org/ironedgesoftware/config.svg?branch=master)](https://travis-ci.org/ironedgesoftware/config)

This component provides a simple API to handle configuration parameters. It
can handle different **readers** and **writers** to read from and write to any
storage.

Currently supported readers and writers:

* **files**: Using component [**ironedge/file-utils**](https://github.com/ironedgesoftware/file-utils).
* **array**: Useful for testing purposes.

## Usage

``` php
<?php

namespace Your\Namespace;

use IronEdge\Component\Config\Config;

// Create a config instance. You can set initial data and options
// using its constructor.

$data = [
    'user'          => [
        'username'      => 'admin',
        'groups'        => [
            'primary'       => 'administrator',
            'secondary'     => ['development']
        ]
    ]
];

$config = new Config($data);



```