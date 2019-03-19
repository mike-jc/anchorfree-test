<?php

require_once '../conf/conf.php';
require_once '../inc/general.php';
require_once '../inc/mysql.php';

allowMethods(['GET']);

$format = isset($_GET['format']) ? $_GET['format'] : 'json';
if (!in_array($format, $configuration['output']['formats']))
{
    error('Invalid output format. Should be one of '. implode(', ', $configuration['output']['formats']), 400);
}

$result = $mysqli->query(
    "SELECT `events`.`country`, `events`.`type`, SUM(`events`.`counter`) as summary
    FROM `events`
    INNER JOIN (
        SELECT `country` FROM `countries`
        ORDER BY `counter` DESC
        LIMIT {$configuration['output']['topCountries']}
    ) as `top_countries`
    ON `events`.`country` = `top_countries`.`country`
    GROUP BY `events`.`country`, `events`.`type`"
);

if ($format == 'json')
{
    $data = [];
    if ($result)
    {
        while($row = $result->fetch_assoc())
        {
            if ($row['summary'] > 0)
            {
                $data[$row['country']][$row['type']] = $row['summary'];
            }
        }
    }
    header('Content-Type: application/json');

    echo json_encode($data);
    exit;
} elseif ($format == 'csv')
{
    header("Content-type: text/csv");
    header("Content-Disposition: attachment; filename=top.csv");

    $output = fopen("php://output", "wb");
    fputcsv($output, [
        'country'   => 'Country ISO code',
        'type'      => 'Event type',
        'summary'   => 'Event sum',
    ], ';');
    if ($result)
    {
        while ($row = $result->fetch_assoc())
        {
            if ($row['summary'] > 0)
            {
                fputcsv($output, $row, ';');
            }
        }
    }
    fclose($output);
    exit;
}
