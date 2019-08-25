<?php


namespace core\validation;


class Validator extends AbstractValidator {

    private $result = null;
    private $value  = null;

    public function init($value) {
        $this->value = $value;
        return $this;
    }

    public function validate() {
        return $this->result;
    }

    protected function setResult($result) {
        $this->result = $result;
    }

    /**
     * returns value for validation
     *
     * @return |null
     */
    protected function getValue() {
        return $this->value;
    }

}