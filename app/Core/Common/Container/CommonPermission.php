<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * CommonPermission.php
 *
 * User：YM
 * Date：2020/1/12
 * Time：上午10:56
 */


namespace Core\Common\Container;

use Core\Services\PermissionsService;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;


/**
 * CommonPermission
 * 后台管理权限
 * @package Core\Common\Container
 * User：YM
 * Date：2020/1/12
 * Time：上午10:56
 */
class CommonPermission
{
    /**
     * 超级管理员用户组id
     */
    const ROOT_ROLE_ID = 1;

    /**
     * @Inject()
     * @var PermissionsService
     */
    protected $permissionService;

    /**
     * checkPermissions
     * 检查权限
     * User：YM
     * Date：2020/1/12
     * Time：上午10:58
     * @param $userId 用户id
     * @param $permissionName 权限标识
     * @return bool
     */
    public function checkPermissions($userId, $permissionName)
    {
        $list = $this->getUserAllPermissions($userId);

        $tmp = explode('|',$permissionName);
        foreach ($tmp as $v) {
            if (in_array($v,$list)) {
                return true;
            }
        }

        return false;
    }

    /**
     * getUserAllPermissions
     * 获取用户对应所有权限
     * User：YM
     * Date：2020/1/12
     * Time：下午11:46
     * @param $userId
     * @return array
     *
     * @Cacheable(prefix="admin_user_permission",ttl=9000,listener="admin-user-permission")
     */
    public function getUserAllPermissions($userId)
    {
        //超级管理员拥有所有权限
        $isRoot = Db::table('system_roles_user')
            ->where("system_roles_user.user_id","=", $userId)
            ->where("system_roles_user.system_role_id","=", self::ROOT_ROLE_ID)
            ->first();
        $selectList = ['system_permissions.id', 'system_permissions.parent_id', 'system_permissions.name', 'system_permissions.display_name', 'system_permissions.order'];
        if(!is_null($isRoot)){
            //超级管理员拥有所有权限
            $list = Db::table('system_permissions')->select($selectList)->get()->toArray();

        }else{
            //查出所有权限
            $list = Db::table('system_permissions')->select($selectList)
                ->join('system_roles_permissions','system_permissions.id','=','system_roles_permissions.system_permission_id')
                ->join("system_roles_user","system_roles_permissions.system_role_id","=","system_roles_user.system_role_id")
                ->where('system_roles_user.user_id',"=", $userId)
                ->get()->toArray();
        }
        $list = array_pluck($list,'name');
        return $list;
    }

    /**
     * getPermissionsFromUri
     * 获取uri对应的权限标识
     * User：YM
     * Date：2020/3/4
     * Time：下午11:21
     * @param string $uri
     * @return array
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getPermissionsFromUri($uri = '')
    {
        if (!$uri) {
            return [];
        }
        $list = $this->permissionService->getPermissionsFromUri();

        return $list[$uri]??[];
    }

}