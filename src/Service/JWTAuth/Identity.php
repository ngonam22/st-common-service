<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2020-02-11
 * Time: 14:39
 */

namespace StCommonService\Service\JWTAuth;

use StCommonService\Service\ApiAcl\Acl;

/**
 * Class Identity
 *
 * Identity Model
 *
 * @package StCommonService\Service\JWTAuth
 */
class Identity
{
    /**
     * Doctrine device entity
     *
     * @var null
     */
    private $_device = null;

    /**
     * Doctrine user entity
     * @var null|mixed
     */
    private $_user = null;

    /**
     * Role
     *
     * @var string
     */
    private $_role = Acl::ANONYMOUS_ROLE;

    /**
     * @return mixed
     */
    public function getDevice()
    {
        return $this->_device;
    }

    /**
     * @param mixed $device
     */
    public function setDevice($device): void
    {
        $this->_device = $device;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->_user = $user;
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->_role;
    }

    /**
     * @param string $role
     */
    public function setRole(string $role): void
    {
        $this->_role = $role;
    }
}