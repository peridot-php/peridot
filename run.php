<?php
require_once 'vendor/autoload.php';
require_once 'bootstrap.php';

use Peridot\Core\SpecResult;
use Peridot\Runner\Runner;
use Peridot\Runner\SuiteLoader;

$loader = new SuiteLoader();
$specs = $loader->load(__DIR__ . '/specs/');
$result = new SpecResult();
$runner = new Runner($result);
foreach ($specs as $file) {
    $runner->runSpec($file);
}
$result = $runner->getResult();
print $result->getSummary() . "\n";