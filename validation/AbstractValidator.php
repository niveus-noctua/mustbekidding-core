<?php

namespace core\validation;


abstract class AbstractValidator {

    /**
     * @param $value
     * @return $this
     */
    abstract public function init($value);

    abstract public function validate();

}