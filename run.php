<?php
require_once 'vendor/autoload.php';
require_once 'bootstrap.php';
require_once 'specs/ItWasRun.php';

use Peridot\Core\SpecResult;
use Peridot\Runner\Runner;

$specs = __DIR__ . '/specs/';
$files = ['spec.spec.php', 'suite.spec.php', 'runner.spec.php'];
$result = new SpecResult();
$runner = new Runner($result);
foreach ($files as $file) {
    $runner->runSpec($specs . $file);
}
$result = $runner->getResult();
print $result->getSummary() . "\n";
