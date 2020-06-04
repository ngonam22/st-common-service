<?php
/**
 * Created by PhpStorm.
 * User: Nam Ngo
 * Date: 2020-02-05
 * Time: 11:02
 */

namespace StCommonService\Service\ApiAcl;

use Laminas\Permissions\Acl\Acl as AclBase;
use StCommonService\Service\JWTAuth\Identity;
use StCommonService\Helper\Arr;
use StCommonService\Helper\RouteMatch;

class Acl
{
    /*
     * When client gives the appropriate token, but not yet authenticated,
     * then its role is DEFAULT_ROLE
     */
    const DEFAULT_ROLE = 'guest';

    /*
     * When client cant give the token,
     * its role is ANONYMOUS_ROLE
     */
    const ANONYMOUS_ROLE = 'anonymous';

    private $_commands = [];

    /**
     * @var AclBase
     */
    private $_acl;

    /**
     * @var Identity
     */
    private $_identity = null;

    /**
     * @var array
     */
    private $_routeMatchParams = [];


    public function __construct()
    {
        $this->_acl = new AclBase();
    }

    /**
     * Authorize the current route
     * with matched role and resource in config
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $role = $this->getCurrentRole();

        if (empty($role))
            $role = self::ANONYMOUS_ROLE;

        if (empty($this->_routeMatchParams))
            return false;

        $resource = RouteMatch::getModuleName($this->_routeMatchParams) . ':' . RouteMatch::getControllerName($this->_routeMatchParams, true);

        return $this->_acl->hasRole($role) &&
            $this->_acl->hasResource($resource) &&
            $this->_acl->isAllowed($role, $resource, RouteMatch::getActionName($this->_routeMatchParams))
        ;
    }


    /**
     * Get current of this request
     *
     * @return string
     */
    public function getCurrentRole(): string
    {
        if (empty($this->_identity) || !is_object($this->_identity) || !method_exists($this->_identity, 'getRole'))
            return self::ANONYMOUS_ROLE;

        return $this->_identity->getRole();
    }

    /**
     * @param $identity
     */
    public function setIdentity($identity): void
    {
        $this->_identity = $identity;
    }

    /**
     * @return Identity
     */
    public function getIdentity()
    {
        return $this->_identity;
    }

    /**
     * Set params from RouteMatch instance to our local variable
     *
     * @param array $routeMatchParams
     */
    public function setRouteMatchParams(array $routeMatchParams): void
    {
        $this->_routeMatchParams = $routeMatchParams;
    }

    /**
     * Before storing the array command, format it and save it to ACL
     * @param array $command
     */
    public function setCommands(array $commands): void
    {
        $this->_commands = $this->_parseCommand($commands);
    }

    /**
     * Parse the instruction command group
     *
     * @param array $commands
     * @return array
     */
    private function _parseCommand(array $commands): array
    {
        $ruleGroup = $roleGroup = $resourceGroup = [];

        // validate the command group
        foreach ($commands as $group) {
            if (!Arr::exists('command', $group))
                continue;

            switch ($group['command']) {
                case AclCommand::RULE_GROUP_COMMAND:
                    (Arr::exists('rules', $group) && is_array($group['rules'])) ? array_push($ruleGroup, ...$group['rules']) : null;
                    break;
                case AclCommand::RESOURCE_GROUP_COMMAND:
                    (Arr::exists('resources', $group) && is_array($group['resources'])) ? array_push($resourceGroup, ...$group['resources']) : null;
                    break;

                case AclCommand::ROLE_GROUP_COMMAND:
                    (Arr::exists('roles', $group) && is_array($group['roles'])) ? array_push($roleGroup, ...$group['roles']) : null;
                    break;
            }
        }

        // set the value to _acl based on the validated group
        $this->_registerResource($resourceGroup);
        $this->_registerRole($roleGroup);
        $this->_registerRule($ruleGroup);

        // TODO: we should return the actual executed commands not the input again
        return $commands;
    }

    /**
     *
     * @param array $resources
     */
    private function _registerResource(array $resources): void
    {
        foreach ($resources as $resource) {
            if (
                $resource['command'] != AclCommand::RESOURCE_COMMAND ||
                !Arr::exists(['command', 'resource', 'parent'], $resource)
            )
                continue;

            $this->_acl->addResource($resource['resource'], $resource['parent']);
        }
    }

    /**
     * Register roles to _acl
     *
     * @param array $roles
     */
    private function _registerRole(array $roles): void
    {
        foreach ($roles as $role) {
            if (
                $role['command'] != AclCommand::ROLE_COMMAND ||
                !Arr::exists(['command', 'role', 'parents'], $role)
            )
                continue;

            $this->_acl->addRole($role['role'], $role['parents']);
        }
    }

    /**
     * Set rules from rule instruction groups to _acl
     *
     * @param array $rules
     */
    private function _registerRule(array $rules): void
    {
        foreach ($rules as $rule) {
            if (
                $rule['command'] != AclCommand::RULE_COMMAND ||
                !Arr::exists(['command', 'rule', 'roles', 'resources', 'privileges'], $rule)
            )
                continue;

            $this->_acl->setRule(
                AclBase::OP_ADD,
                $rule['rule'],
                $rule['roles'],
                $rule['resources'],
                $rule['privileges']
            );
        }
    }
}