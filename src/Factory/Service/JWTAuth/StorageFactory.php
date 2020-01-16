<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2020-01-16
 * Time: 16:59
 */

namespace StCommonService\Factory\Service\JWTAuth;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use StCommonService\Service\JWTAuth\Storage;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;

class StorageFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new Storage();
    }

}