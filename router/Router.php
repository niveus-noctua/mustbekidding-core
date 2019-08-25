<?php

namespace core\router;


use core\event\EventManager;

class Router {

    private $config = [
        'name' => 'route',
        'service' => 'true'
    ];

    public function __construct() {

    }

    public function process() {
        $routeManager = new EventManager();
        $routeManager->getEvent($this->config)->trigger();
    }

}