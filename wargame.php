#!/usr/bin/env php
<?php

if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    fwrite(STDERR, 'You must set up the project dependencies.' . PHP_EOL);
    exit(1);
}

require_once __DIR__ . '/vendor/autoload.php';

$application = new Symfony\Component\Console\Application();
$application->add(new \WarGame\Application\Command\PlayWarGameCommand());
$application->run();
