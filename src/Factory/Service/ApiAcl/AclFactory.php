<?php
/**
 * Created by PhpStorm.
 * User: Nam Ngo
 * Date: 2020-02-05
 * Time: 15:54
 */

namespace StCommonService\Factory\Service\ApiAcl;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Factory\FactoryInterface;
use StCommonService\Service\ApiAcl\Acl;
use Lcobucci\JWT\Token;

class AclFactory implements FactoryInterface
{
    protected $aclConfigKey = 'api_acl';

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $cpManager = $container->get('ControllerPluginManager');
        $identity = $cpManager->has('Identity') ? $cpManager->get('Identity')() : null;

        $config = $container->get('Config')[$this->aclConfigKey];

        unset($cpManager);

        $acl = new Acl();
        $acl->setCommands($config);
        $acl->setIdentity($identity);
        $acl->setRouteMatchParams(
            $container->get('Application')->getMvcEvent()->getRouteMatch()
        );

        return $acl;
    }

}