<?php


namespace core\service;


class ServiceManager {

    // используется для вызова служебных сервисов
    // (например обработка ошибок)
    const SERVICE = 'service';

    private const USE_INTERNAL_CLASS_MAP = 'use_service_map';

    public function get($serviceName, $flags = null) {
        if (!is_null($flags) && is_array($flags)) {
            foreach ($flags as $flag) {
                $serviceParameters[] = $flag;
            }
            $options = ['service' => $serviceParameters];
            $service = Service::get($serviceName, $options);
            return $service;
        }
        $service = Service::get($serviceName);
        return $service;
    }

    public function getExceptionService() {
        return $this->get('handler_service', [self::USE_INTERNAL_CLASS_MAP]);
    }

}