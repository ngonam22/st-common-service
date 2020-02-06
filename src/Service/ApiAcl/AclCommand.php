<?php
/**
 * Created by PhpStorm.
 * User: Nam Ngo
 * Date: 2020-02-05
 * Time: 11:00
 */

namespace StCommonService\Service\ApiAcl;

use Laminas\Permissions\Acl\Acl as AclBase;

class AclCommand
{
    // group's command
    const RULE_GROUP_COMMAND     = 'rule_group_command';
    const RESOURCE_GROUP_COMMAND = 'resource_group_command';
    const ROLE_GROUP_COMMAND     = 'role_group_command';

    // action's command
    const RULE_COMMAND     = 'rule_command';
    const RESOURCE_COMMAND = 'resource_command';
    const ROLE_COMMAND     = 'role_command';


    static public function rules(...$rules)
    {
        return [
            'command' => self::RULE_GROUP_COMMAND,
            'rules'   => $rules,
        ];
    }

    static public function resources(...$resources)
    {
        return [
            'command'   => self::RESOURCE_GROUP_COMMAND,
            'resources' => $resources,
        ];
    }

    static public function roles(...$roles)
    {
        return [
            'command' => self::ROLE_GROUP_COMMAND,
            'roles'   => $roles,
        ];
    }

    /**
     * Return formatted instruction of an "allow" rule to the ACL
     *
     * @param      $roles
     * @param null $resources
     * @param null $privileges
     * @return array
     */
    static public function allow($roles, $resources = null, $privileges = null)
    {
        return [
            'command'    => self::RULE_COMMAND,
            'rule'       => AclBase::TYPE_ALLOW,
            'roles'      => $roles,
            'resources'  => $resources,
            'privileges' => $privileges,
        ];
    }

    /**
     * Returns formatted instruction of a "deny" rule to the ACL
     *
     * @param  string|array $roles
     * @param  string|array $resources
     * @param  string|array $privileges
     * @return array A formatted instruction
     */
    static public function deny($roles, $resources = null, $privileges = null)
    {
        return [
            'command'    => self::RULE_COMMAND,
            'rule'       => AclBase::TYPE_DENY,
            'roles'      => $roles,
            'resources'  => $resources,
            'privileges' => $privileges,
        ];
    }

    /**
     * Returns an instruction of a Resource having an identifier unique to the ACL
     *
     * The $parent parameter may be a reference to, or the string identifier for,
     * the existing Resource from which the newly added Resource will inherit.
     *
     * @param  string $resource
     * @param  string $parent
     * @return array A formatted instruction
     */
    static public function addResource($resource, $parent = null)
    {
        return [
            'command'  => self::RESOURCE_COMMAND,
            'resource' => $resource,
            'parent'   => $parent,
        ];
    }

    /**
     * Returns an instruction array of a Role having an identifier unique to the registry
     *
     * The $parents parameter may be a reference to, or the string identifier for,
     * a Role existing in the registry, or $parents may be passed as an array of
     * these - mixing string identifiers and objects is ok - to indicate the Roles
     * from which the newly added Role will directly inherit.
     *
     * In order to resolve potential ambiguities with conflicting rules inherited
     * from different parents, the most recently added parent takes precedence over
     * parents that were previously added. In other words, the first parent added
     * will have the least priority, and the last parent added will have the
     * highest priority.
     *
     * @param  string       $role
     * @param  string|array $parents
     * @return array A formatted instruction
     */
    static public function addRole($role, $parents = null)
    {
        return [
            'command' => self::ROLE_COMMAND,
            'role'    => $role,
            'parents' => $parents,
        ];
    }
}