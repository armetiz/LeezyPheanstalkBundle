<?php

$baseDir = dirname(__DIR__);

$loader = require __DIR__.'/../vendor/autoload.php';
$loader->add('Leezy\\PheanstalkBundl', array($baseDir));
$loader->register();