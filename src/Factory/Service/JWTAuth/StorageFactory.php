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
use Doctrine\ORM\EntityManager;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\Http\Request;

class StorageFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var Request $request */
        $request = $container->get('request');

        /** @var EntityManager $em */
        $em = $container->get('doctrine.entitymanager.orm_default');

        return new Storage($request, $em);
    }

}