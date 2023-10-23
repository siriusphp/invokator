<?php

require_once(__DIR__ . '/../vendor/autoload.php');
require_once (__DIR__ . '/src/TestCase.php');
require_once (__DIR__ . '/src/Utilities/SimpleCallables.php');
require_once (__DIR__ . '/src/Utilities/DependencyClass.php');
require_once (__DIR__ . '/src/Utilities/DependentClass.php');
require_once (__DIR__ . '/src/CommandBus/SampleCommand.php');
require_once (__DIR__ . '/src/CommandBus/SampleHandler.php');
ini_set('xdebug.mode', 'coverage');
date_default_timezone_set('UTC');
