<?php

namespace core\event;


abstract class AbstractEvent {

    abstract public function init();

    abstract public function trigger();

}