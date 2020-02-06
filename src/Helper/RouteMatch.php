<?php
/**
 * Created by PhpStorm.
 * User: Nam Ngo
 * Date: 2020-02-06
 * Time: 11:33
 */

namespace StCommonService\Helper;

/**
 * Class RouteMatch
 *
 * Helper class to quickly parse Route info from RouteMatch's params
 *
 * @package StCommonService\Helper
 */
class RouteMatch
{
    /**
     * @param array $params Params from RouteMatch
     * @return string
     */
    static public function getModuleName(array $params = []): string
    {
        $moduleName = 'no-module';

        if (empty($params['controller']))
            return $moduleName;

        $moduleName = (string) $params['controller'];
        $moduleName = explode('\\', $moduleName);

        return strtolower($moduleName[0]);
    }

    /**
     * @param array $params
     * @return string
     */
    static public function getControllerName(array $params = []): string
    {
        $controllerName = 'no-controller';

        if (empty($params['controller']))
            return $controllerName;

        $controllerName = (string) $params['controller'];
        $controllerName = explode('\\', $controllerName);
        $controllerName = end($controllerName);

        return str_replace('controller','',strtolower($controllerName));
    }

    /**
     * @param array $params
     * @return string
     */
    static public function getActionName(array $params = []): string
    {
        $actionName = 'no-action';

        return (empty($params['action'])) ? $actionName : $params['action'];
    }
}