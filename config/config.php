<?php

return [
    'host'    => $_SERVER['SERVER_NAME'],
    'request' => $_SERVER['REQUEST_URI'],
    'root'    => $_SERVER['DOCUMENT_ROOT'] . '/../',
    'class_map' => [
        'exception_service' => 'core\service\services\ExceptionService',
        'validator_service' => 'core\service\services\ValidatorService'
    ],
    'events' => [
        'route' => 'core\event\events\RouteEvent'
    ]
];
