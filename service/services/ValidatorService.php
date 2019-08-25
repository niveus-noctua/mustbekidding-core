<?php

namespace core\service\services;

use core\service\Service;

class ValidatorService extends Service {

    public static function get($name, $options = null) {
        /**
         * service является признаком, что запрашивается
         * служебный сервис, а не пользовательский
         */
        $options = array_merge($options, self::SERVICE);
        self::overrideClassmap();
        return parent::get($name, $options);
    }

    protected static function overrideClassmap(){
        parent::$classMap = self::serviceClassmap();
    }

    protected static function serviceClassmap() {
        return [
            'param'   => 'core\validation\validators\ParamValidator',
            'request' => 'core\validation\validators\RequestValidator'
        ];
    }

}