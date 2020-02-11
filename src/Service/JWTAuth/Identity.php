<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2020-02-11
 * Time: 14:39
 */

namespace StCommonService\Service\JWTAuth;

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
     * @var null
     */
    private $_user = null;

    /**
     * Role
     *
     * @var string
     */
    private $_role = 'anonymous';

    /**
     * @return null
     */
    public function getDevice()
    {
        return $this->_device;
    }

    /**
     * @param null $device
     */
    public function setDevice($device): void
    {
        $this->_device = $device;
    }

    /**
     * @return null
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * @param null $user
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