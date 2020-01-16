<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2020-01-16
 * Time: 17:24
 */

namespace StCommonService\Factory\Service\JWTAuth;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Authentication\AuthenticationService;


class AuthenticationFactory implements FactoryInterface
{
    public $factories = [
        'StCommonService\JWTAdapter' => AdapterFactory::class,
        'StCommonService\JWTStorage' => StorageFactory::class
    ];

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var ServiceManager $container */
        $this->_configureFactories($container);


        return new AuthenticationService(
            $container->get('StCommonService\JWTStorage'),
            null
        );
    }

    private function _configureFactories(ServiceManager $serviceManager)
    {
        $serviceManager->configure([
            'factories' => $this->factories
        ]);
    }
}