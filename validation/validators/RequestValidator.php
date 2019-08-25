<?php

namespace core\validation\validators;


use core\controller\AbstractController;
use core\service\ServiceManager;
use core\service\services\ExceptionService;
use core\validation\Validator;

class RequestValidator extends Validator {

    public function init($value) {
        return parent::init($value);
    }

    public function validate() {
        $sm = new ServiceManager();
        /** @var ExceptionService $exceptionService */
        $exceptionService = $sm->get('exception_service');

        $request = $this->trim($this->getValue());
        $request_prepared = $this->prepare($request);

        if (class_exists($request_prepared['object']) && new $request_prepared['object'] instanceof AbstractController) {
            if (method_exists($request_prepared['object'], $request_prepared['action'])) {
                $this->setResult(['result' => true]);
                $exceptionService->log();
            } else {
                $this->setResult(['result' => false]);
                $exceptionService->log();
            }

        } else {
            $this->setResult(['result' => false]);
            $exceptionService->log();
        }


        return parent::validate();
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



}