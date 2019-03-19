<?php

$events = [
    'view'  => 'views',
    'click' => 'clicks',
    'play'  => 'plays',
];

function error(string $msg, string $code): void
{
    header("HTTP/1.0 $code $msg");
    exit;
}

if (!function_exists('getallheaders'))
{
    function getallheaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $name => $value)
        {
            if (substr($name, 0, 5) == 'HTTP_')
            {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

function allowMethods(array $methods): void
{
    if (!in_array($_SERVER['REQUEST_METHOD'], $methods))
    {
        error('Method not allowed', 405);
    }
}

function parseJsonInPost(): array
{
    $headers = getallheaders();
    if (isset($headers['Content-Type']) && $headers['Content-Type'] != 'application/json')
    {
        error('Request body should be in JSON', 404);
    }

    $result = @json_decode(@file_get_contents('php://input'), true);
    if (is_null($result))
    {
        error('Can not get or parse JSON in request body', 404);
    }
    return $result;
}
