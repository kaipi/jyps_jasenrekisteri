#!/usr/bin/env php
<?php
// application.php

require __DIR__.'/vendor/autoload.php';

use JYPS\RegisterBundle\Command\CloseUnpaidMembersCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new CloseUnpaidMembersCommand());
$application->run();

