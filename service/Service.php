<?php


namespace core\service;


use core\Config;
use core\service\services\ExceptionService;

class Service extends AbstractService {

    protected static $global = [];
    protected static $local  = [];

    //overrided class map
    protected static $classMap = [];

    const SERVICE = ['service' => []];
    const USE_INTERNAL_CLASS_MAP = ['service' => ['use_service_map']];

    public static function get($name, $options = null) {
        //получаем пользовательский и служебный конфиги для проверки на наличие classmap
        if (empty(self::$global)) self::$global = Config::global();
        if (empty(self::$local))  self::$local  = Config::local();
        if (!self::$global) {
            self::$global = [];
        }
        if (!self::$local)  {
            self::$local  = [];
        }

        if (!empty(self::$classMap)) {
            $instance = self::find($name, self::$classMap, $options);
            return $instance;
        }

        if (array_key_exists('class_map', self::$local)) {
            $instance = self::find($name, self::$local['class_map'], $options);
        }
        if (!$instance) {
            $instance = self::find($name, self::$global['class_map'], $options);
        }

        return $instance;

    }

    private static function find($name, $classMap, $options = null) {
        if (!is_null($options)) {
            $updateOnAction = false;
            /**
             * Если указаны дополнительные параметры для
             * сервисов - выполняем сперва их,
             * затем пытаемся найти необходимый сервис.
             *
             */
            foreach ($options['service'] as $actionType) {
                $action = self::getAction($actionType);
                if ($action) {
                    $updateOnAction = true;
                }
                self::doAction($action);
            }
            if ($updateOnAction) {
                $instance = self::get($name);
                if ($instance) {
                    self::clean();
                    return new $instance;
                }
            }
            $instance = self::locate($name, $classMap);
            if ($instance) {
                self::clean();
                return new $instance();
            }
        }

        /**
         * Пытаемся найти пользовательский сервис,
         * при его отсутствии выбрасываем ошибку
         */
        $instance = self::locate($name, $classMap);
        if ($instance) {
            self::clean();
            return new $instance;
        }

        self::throwServiceError($name);

    }

    private static function clean() {
        self::$classMap = [];
    }

    private static function throwServiceError($name) {
        /**
         * Запрашивается сервис обработки ошибок
         *
         * Флаг use_internal_class_map дает явное
         * указание использовать внутреннюю class map
         * сервиса вместо определенной в конфигурационном
         * файле.
         *
         * @var $exceptionService ExceptionService
         */
        $exceptionService = Service::get('handler_service', self::USE_INTERNAL_CLASS_MAP);
        $exceptionHandler = $exceptionService::get('handler');
        $exceptionHandler->throw(NOT_EXISTING_SERVICE, [
            'name' => $name
        ]);
    }

    private static function throwActionError($name) {
        $exceptionService = Service::get('handler_service', self::USE_INTERNAL_CLASS_MAP);
        $exceptionHandler = $exceptionService::get('handler');
        $exceptionHandler->throw(NOT_EXISTING_ACTION, [
            'name' => $name
        ]);
    }

    protected static function locate($name, $classmap) {
        if (is_array($classmap)) {
            if (array_key_exists($name, $classmap)) {
                $location = $classmap[$name];
                if (class_exists($location)) {
                    return $location;
                }
                return false;
            }
            return false;
        }
        return false;
    }

    protected static function doAction($action) {
        if (method_exists(self::class, $action)) {
            self::$action();
        } else {
            /**
             * Возможно не бросать исключение
             */
            self::throwActionError($action);
        }
    }

    protected static function getAction($actionType) {
        $action = [
            'use_service_map' => 'overrideClassmap'
        ];
        return $action[$actionType];
    }

    protected static function overrideClassmap() {
        self::$classMap = self::serviceClassmap();
    }

    protected static function serviceClassmap() {
        return [
            'handler_service' => 'core\service\services\ExceptionService'
        ];
    }

}