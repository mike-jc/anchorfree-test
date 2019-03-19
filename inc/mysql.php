<?php

require_once '../conf/conf.php';
require_once 'general.php';

if (empty($configuration['database']))
{
    // TODO: add some logging
    error('No database configuration', 500);
}
$dbConf = $configuration['database'];

if (empty($dbConf['user']) || empty($dbConf['name']))
{
    error('No database configuration', 500);
}

$mysqli = new mysqli($dbConf['host'] ?: 'localhost', $dbConf['user'], $dbConf['password'], $dbConf['name']);
