<?php


namespace core\service\services;


use core\service\Service;

class ExceptionService extends Service {

    public static function get($name, $options = []) {
        /**
         * service является признаком, что запрашивается
         * служебный сервис, а не пользовательский
         */
        $options = array_merge($options, self::SERVICE);
        self::overrideClassmap();
        return parent::get($name, $options);
    }

    protected static function overrideClassmap(){
        parent::$global['class_map'] = self::serviceClassmap();
    }

    protected static function serviceClassmap() {
        return [
            'handler' => 'core\exception\ExceptionHandler'
        ];
    }

    public function log() {

    }

}