<?php
require_once 'vendor/autoload.php';
require_once 'bootstrap.php';

use Peridot\Core\SpecResult;
use Peridot\Runner\Runner;
use Peridot\Runner\SuiteLoader;

$loader = new SuiteLoader();
$loader->load(__DIR__ . '/specs/');
$result = new SpecResult();
$runner = new Runner(\Peridot\Runner\Context::getInstance()->getCurrentSuite());
$runner->run($result);
print $result->getSummary() . "\n";
