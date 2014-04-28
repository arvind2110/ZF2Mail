<?php 

namespace ZF2Mail;

use Zend\Mvc\MvcEvent;

class Module{
    
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager   = $e->getApplication()->getEventManager();
        $serviceManager = $e->getApplication()->getServiceManager();
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
                )
            )
        );
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'ZF2Mail' => function ($serviceManager) {
                   return new Mail($serviceManager);
                }
            ),
        );
    }
}