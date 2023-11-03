<?php

$testDir = getcwd();

if (!file_exists($testDir . '/phpunit.xml.dist')) {
    throw new Exception('You are not in a sample directory');
}

if (file_exists($testDir . '/composer.json')) {
    if (!file_exists($testDir . '/vendor/autoload.php')) {
        throw new Exception('You need to run "composer install" in your current directory');
    }
    require_once $testDir . '/vendor/autoload.php';
}
