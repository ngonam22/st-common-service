<?php
/**
 * Created by PhpStorm.
 * User: Nam Ngo
 * Date: 2020-02-05
 * Time: 11:02
 */

namespace StCommonService\Service\ApiAcl;

use Laminas\Permissions\Acl\Acl as AclBase;
use StCommonService\Helper\Arr;

class Acl
{
    private $_commands = [];

    /**
     * @var AclBase
     */
    private $_acl;

    public function __construct(array $commands)
    {
        $this->_acl = new AclBase();
        $this->setCommands($commands);
    }

    /**
     * @param array $command
     */
    public function setCommands(array $commands): void
    {
        $this->_commands = $this->_formatCommand($commands);
    }

    private function _formatCommand(array $commands): array
    {
        $ruleGroup = $roleGroup = $resourceGroup = [];

        foreach ($commands as $group) {
            if (!array_key_exists('command', $group))
                continue;

            switch ($group['command']) {
                case AclCommand::RULE_GROUP_COMMAND:
                    (array_key_exists('rules', $group) && is_array($group['rules'])) ? array_push($ruleGroup, ...$group['rules']) : null;
                    break;
                case AclCommand::RESOURCE_GROUP_COMMAND:
                    (array_key_exists('resources', $group) && is_array($group['resources'])) ? array_push($resourceGroup, ...$group['resources']) : null;
                    break;

                case AclCommand::ROLE_GROUP_COMMAND:
                    (array_key_exists('roles', $group) && is_array($group['roles'])) ? array_push($roleGroup, ...$group['roles']) : null;
                    break;
            }
        }

        $this->_registerResource($resourceGroup);
        $this->_registerRole($roleGroup);
        $this->_registerRule($ruleGroup);

        return $commands;
    }

    private function _registerResource(array $resources): void
    {
        foreach ($resources as $resource) {
            if (
                !Arr::exists(['command', 'resource', 'parent'], $resource) || $resource['command'] != AclCommand::RESOURCE_COMMAND
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
                !Arr::exists(['command', 'role', 'parents'], $role) || $role['command'] != AclCommand::ROLE_COMMAND
            )
                continue;

            $this->_acl->addRole($role['role'], $role['parents']);
        }
    }

    private function _registerRule(array $rules): void
    {
        foreach ($rules as $rule) {
            if (
                !Arr::exists(['command', 'rule', 'roles', 'resources', 'privileges'], $rule) ||
                $rule['command'] != AclCommand::RULE_COMMAND
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