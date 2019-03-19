<?php

require_once '../conf/conf.php';
require_once '../inc/general.php';
require_once '../inc/mysql.php';

allowMethods(['POST']);

$request = parseJsonInPost();

if (empty($request['country']))
{
    error('Invalid country', 404);
}
if (empty($request['event']) || !isset($events[$request['event']]))
{
    error('Invalid event', 404);
}

$today = date('Y-m-d');
$oldest = date('Y-m-d', strtotime("-{$configuration['store']['days']} days"));

// TODO: this DELETE can be ommited here to process request faster if
// we will run some scheduled job (e.g. cron) at midnight
// to delete the oldest date in events to keep always only last N dates
// (see cron/init_new_date.php)
$mysqli->query("DELETE FROM `events` WHERE `date` <=  '$oldest'");

$country = strtoupper($request['country']);
$type = strtolower($request['event']);

// TODO: this query can be replaced with faster UPDATE if
// we will run some scheduled job (e.g. cron) at midnight to fill
// new date with zero counters for all possible countries/events
// (see cron/init_new_date.php)
$mysqli->query(
    "INSERT INTO `events`
      (`date`, `country`, `type`, `counter`)
    VALUES
      ('$today', '$country', '$type', 1)
    ON DUPLICATE KEY UPDATE
      `counter` = `counter` + 1"
);

header('OK');
exit;

