<?php

namespace core;

use core\router\Router as Router;


class App {

    private $options = null;

    /**
     * @var Router $router
     */
    private $router  = null;

    public function init($options = null) {
        $this->options = $options;
        $this->router  = new Router();
        $this->loadConfigs();
        return $this;
    }

    public function loadConfigs() {
        /**
         * services
         */
        require_once 'service/config/service.exceptions.php';

        /**
         * events
         */
        require_once 'event/config/event.exceptions.php';
    }

    public function run() {
        $this->router->process();
    }

}