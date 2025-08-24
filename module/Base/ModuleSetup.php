<?php

namespace Base;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\EventManager\EventInterface;
use Zend\Validator\AbstractValidator;

class ModuleSetup implements AutoloaderProviderInterface, ConfigProviderInterface, BootstrapListenerInterface
{

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function onBootstrap(EventInterface $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();

        /* Check database */

        $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
        $dbConnection = $dbAdapter->getDriver()->getConnection();

        try {
            $dbConnection->connect();
        } catch (\RuntimeException $e) {
            include 'Charon.php';

            Charon::carry('application', 'configuration', 1);
        }

        /* Skip translator setup during initial setup - not needed for database setup */
        // This is the line that was causing the i18n config error
        // $translator = $serviceManager->get('Translator');
        // AbstractValidator::setDefaultTranslator($translator);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

}
