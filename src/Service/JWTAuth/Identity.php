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
    private $_stDevice = null;

    /**
     * Doctrine user entity
     * @var null
     */
    private $_stUser = null;

    /**
     * Role
     *
     * @var string
     */
    private $_role = 'anonymous';

    /**
     * @return null
     */
    public function getStDevice()
    {
        return $this->_stDevice;
    }

    /**
     * @param null $stDevice
     */
    public function setStDevice($stDevice): void
    {
        $this->_stDevice = $stDevice;
    }

    /**
     * @return null
     */
    public function getStUser()
    {
        return $this->_stUser;
    }

    /**
     * @param null $stUser
     */
    public function setStUser($stUser): void
    {
        $this->_stUser = $stUser;
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