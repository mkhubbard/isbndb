<?php

if (!$loader = @include __DIR__ . '/../vendor/autoload.php') {
    echo <<<LOADER_EOM

You must set up the project dependencies by running the following commands:

    curl -s http://getcomposer.org/installer | php
    php composer.phar install

LOADER_EOM;

    exit(1);
}

$loader->add('IsbnDb\Test', __DIR__);
