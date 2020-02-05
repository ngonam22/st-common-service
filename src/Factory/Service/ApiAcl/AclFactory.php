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

class AclFactory implements FactoryInterface
{
    protected $aclConfigKey = 'api_acl';

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('Config')[$this->aclConfigKey];

        return new Acl($config);
    }

}