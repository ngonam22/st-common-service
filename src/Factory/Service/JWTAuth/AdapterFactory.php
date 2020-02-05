<?php
/**
 * Created by PhpStorm.
 * User: Nam Ngo
 * Date: 2020-01-16
 * Time: 14:41
 */

namespace StCommonService\Factory\Service\JWTAuth;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use StCommonService\Service\JWTAuth\JWTAuthAdapter;
use Doctrine\ORM\EntityManager;

class AdapterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /** @var EntityManager $em */
        $em = $container->get('doctrine.entitymanager.orm_default');

        return new JWTAuthAdapter($em);
    }

}