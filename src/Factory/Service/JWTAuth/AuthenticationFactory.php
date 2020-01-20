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
use StCommonService\Config\JWTConfig;
use StCommonService\Service\JWTAuth\Storage;
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
        // get the jwt config and generate the Config class
        $config = $container->get('config');
        $config = new JWTConfig($config['jwt'] ?? []);


        /** @var ServiceManager $container */
        $this->_configureFactories($container);

        /** @var Storage $storage */
        $storage = $container->get('StCommonService\JWTStorage');
        $storage->setConfig($config);

        return new AuthenticationService(
            $storage,
//            $container->get('StCommonService\JWTAdapter')
            null
        );
    }

    /**
     * Append factory classes for our services into ServiceManager
     *
     * @param ServiceManager $serviceManager
     */
    private function _configureFactories(ServiceManager $serviceManager)
    {
        $serviceManager->configure([
            'factories' => $this->factories
        ]);
    }
}