<?php

namespace core\exception;

class ExceptionHandler {

    public function throw($type, $options) {
        $result = $this->obtain($type, $options);
        $this->render($result);
        die;
    }

    private function obtain($source, $options) {
        $result = $source;
        foreach ($options as $key => $value) {
            $search_key = '%' . $key . '%';
            $result = str_replace($search_key, $value, $result);
        }
        return $result;
    }

    private function render($result) {
        $view = '<b>' . $result . '<b>';
        echo $view;
    }

}