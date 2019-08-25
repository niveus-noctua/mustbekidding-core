<?php

namespace core\controller\routes;

use core\controller\AbstractController;

class IndexController extends AbstractController {

    public function indexAction() {
        echo 'Привет из служебного тестового контроллера';
    }

}