<?php
namespace core\event\events;
use core\Config;
use core\controller\AbstractController;
use core\event\Event as Event;
use core\service\Service;
use core\service\services\ValidatorService;


class RouteEvent extends Event {

    private $cfg     = null;
    private $host    = null;
    private $request = null;

    private $requestValidator = null;

    private $module = null;
    private $object = null;
    private $action = null;


    public function init() {
        $this->cfg     = $this->getConfig();
        $this->host    = $this->cfg['host'];
        $this->request = $this->cfg['request'];

        $proceed = $this->getRequestValidator()
                        ->init($this->request)
                        ->validate();
        if ($proceed['result']) {
            $request      = $this->trim($this->request);
            $request      = $this->prepare($request);

            $this->module = $request['module'];
            $this->object = $request['object'];
            $this->action = $request['action'];

            $this->object = new $this->object;
        } else {
            $userConfig = Config::local();
            if (array_key_exists('router', $userConfig)) {
                $userRouterOptions = $userConfig['router'];
                if (array_key_exists('defaults', $userRouterOptions)) {
                    $defaults = $userRouterOptions['defaults'];
                    $module = $defaults['module'];
                    $object = $defaults['controller'];
                    $action = $defaults['action'];
                    if (!is_null($module) && !is_null($object) && !is_null($action)) {
                        $className = 'module\\' . $module . '\\controller\\' . ucfirst($object) . 'Controller';
                        $action    = $action . 'Action';
                        if (class_exists($className)) {
                            $object = new $className;
                            if (method_exists($object, $action) && $object instanceof AbstractController) {
                                $this->object = $object;
                                $this->action = $action;
                            }
                        }
                    }

                }
            } else {
                $object = 'core\\controller\\routes\\' . ucfirst($this->cfg['router']['defaults']['controller']) . 'Controller';
                $action = $this->cfg['router']['defaults']['action'] . 'Action';

                $this->object = new $object;
                $this->action = $action;
            }

        }
    }

    private function getConfig() {
        return Config::global();
    }

    private function getRequestValidator() {
        if (is_null($this->requestValidator)) {
            $this->requestValidator = ValidatorService::get('request', Service::SERVICE);
            return $this->requestValidator;
        }
        return $this->requestValidator;
    }

    private function trim($request) {
        $request = strtok($request, '?');
        $request = ltrim($request, '/');
        $request = rtrim($request, '/');
        return $request;
    }

    private function prepare($request) {
        $request = explode('/', $request);
        $request_prepared['module'] = $request[0];
        $request_prepared['object'] = 'module\\'
            . $request_prepared['module']
            . '\\controller\\'
            . ucfirst($request[1]) .  'Controller';
        $request_prepared['action'] = $request[2] . 'Action';
        return $request_prepared;
    }

    private function getModule($request) {

    }

    public function trigger() {

        $object = $this->object;
        $action = $this->action;

        $object->$action();

        return parent::trigger();
    }

}