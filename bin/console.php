#!/usr/bin/env php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Symfony\Component\Console\Application;
use App\Commands\AddCommand;
use App\Commands\SearchCommand;
use App\Commands\DeleteCommand;
use App\Commands\EditCommand;

$app = new Application();
$app->add(new AddCommand());
$app->add(new SearchCommand());
$app->add(new DeleteCommand());
$app->add(new EditCommand());

$app -> run();