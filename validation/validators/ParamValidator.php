<?php

namespace core\validation\validators;

use core\Config;
use core\event\Event;
use core\service\ServiceManager;
use core\service\services\ExceptionService;
use core\validation\Validator;

class ParamValidator extends Validator {

    private $isService = true;

    public function init($value) {

        return parent::init($value);
    }

    public function validate() {

        $sm = new ServiceManager();
        /** @var ExceptionService $exceptionService */
        $exceptionService = $sm->get('exception_service');

        $value = $this->getValue();

        if (array_key_exists('name', $value)) {

            $eventName = $value['name'];

            $isService = false;
            if (array_key_exists('service', $value)) {
                $isService = $value['service'];
            }

            $this->isService = $isService;

            if (!$this->eventExists($eventName, $isService)) {
                $this->setResult(['result' => false]);
                $exceptionService->log();
            }

            $this->setResult(['result' => true]);
        } else {
            $exceptionService->log();
            $this->setResult(['result' => false]);
        }

        return parent::validate();
    }

    private function eventExists($eventName, $isService) {

        if ($isService) {
            $config = Config::global();
            if (array_key_exists($eventName, $config['events'])) {
                //получаем нэймспейс ивента
                $event_namespace = $config['events'][$eventName];
                if (class_exists($event_namespace) && new $event_namespace instanceof Event) {
                    return true;
                }
                return false;
            }
        }

        $config = Config::local();
        if (array_key_exists($eventName, $config['events'])) {
            $event_namespace = $config['events'][$eventName];
            if (class_exists($event_namespace) && new $event_namespace instanceof Event) {
                return true;
            }
        }

        $eventDefault = 'events\\' . ucfirst($eventName) . 'Event';
        if (class_exists($eventDefault) && new $eventDefault instanceof Event) {
            return true;
        }

        return false;
    }

    /**
     * Возвращает true если ивент является
     * служебным
     *
     * @return bool
     */
    public function isService() {
        return $this->isService;
    }

}