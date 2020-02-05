<?php
/**
 * Created by PhpStorm.
 * User: Nam Ngo
 * Date: 2020-02-05
 * Time: 11:02
 */

namespace StCommonService\Service\ApiAcl;


class Acl
{
    private $_commands = [];

    public function __construct(array $commands)
    {
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
                    (array_key_exists('rules', $group) && is_array($group['rules'])) ? array_push($ruleGroup, $group['rules']) : null;
                    break;
                case AclCommand::RESOURCE_GROUP_COMMAND:
                    (array_key_exists('resources', $group) && is_array($group['resources'])) ? array_push($ruleGroup, $group['resources']) : null;
                    break;

                case AclCommand::ROLE_GROUP_COMMAND:
                    (array_key_exists('roles', $group) && is_array($group['roles'])) ? array_push($ruleGroup, $group['roles']) : null;
                    break;
            }
        }

        return [];
    }
}