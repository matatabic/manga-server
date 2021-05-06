<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * RolesService.php
 *
 * User：YM
 * Date：2020/2/4
 * Time：下午9:50
 */


namespace Core\Services;

use App\Constants\StatusCode;
use App\Exception\BusinessException;

/**
 * RolesService
 * 角色服务
 * @package Core\Services
 * User：YM
 * Date：2020/2/4
 * Time：下午9:50
 *
 * @property \App\Models\SystemRole $systemRoleModel
 * @property \App\Models\SystemRolesUser $systemRolesUserModel
 * @property \App\Models\SystemRolesPermission $systemRolesPermissionModel
 * @property \Core\Services\PermissionsService $permissionsService
 * @property \Core\Services\UserService $userService
 */
class RolesService extends BaseService
{
    /**
     * getRolesList
     * 获取列表
     * User：YM
     * Date：2020/2/4
     * Time：下午10:02
     * @param $inputData
     * @return array
     */
    public function getRolesList($inputData)
    {
        $pagesInfo = $this->getPagesInfo($inputData);

        $list = $this->getList([],[],$pagesInfo['offset'],$pagesInfo['page_size']);

        $data = [
            'pages' => $pagesInfo,
            'data' => $list
        ];

        return $data;
    }
    
    /**
     * getList
     * 条件获取角色列表
     * User：YM
     * Date：2020/2/4
     * Time：下午10:13
     * @param array $where 查询条件
     * @param array $order 排序条件
     * @param int $offset 偏移
     * @param int $limit 条数
     * @return mixed
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0)
    {

        $list = $this->systemRoleModel->getList($where,$order,$offset,$limit);

        return $list;
    }
    
    /**
     * getPagesInfo
     * 获取分页信息
     * User：YM
     * Date：2020/2/4
     * Time：下午10:13
     * @param array $where
     * @return mixed
     */
    public function getPagesInfo($where = [])
    {
        $pageInfo = $this->systemRoleModel->getPagesInfo($where);

        return $pageInfo;
    }

    /**
     * saveRoles
     * 保存角色，构造数据，防止注入
     * 不接收数据库字段以外数据
     * User：YM
     * Date：2020/2/4
     * Time：下午10:57
     * @param $inputData
     * @return mixed
     */
    public function saveRoles($inputData)
    {
        $saveData = [];
        if (isset($inputData['id']) && $inputData['id']){
            $saveData['id'] = $inputData['id'];
        }

        if (isset($inputData['display_name']) && $inputData['display_name']){
            $saveData['display_name'] = $inputData['display_name'];
        }

        if (isset($inputData['name'])){
            $saveData['name'] = $inputData['name'];
        }

        if (isset($inputData['description'])){
            $saveData['description'] = $inputData['description'];
        }

        $id = $this->systemRoleModel->saveInfo($saveData);

        return $id;
    }

    /**
     * getInfo
     * 根据id获取信息
     * User：YM
     * Date：2020/2/4
     * Time：下午10:58
     * @param $id
     * @return mixed
     */
    public function getInfo($id)
    {
        $info = $this->systemRoleModel->getInfo($id);

        return $info;
    }
    
    /**
     * deleteUserInfo
     * 删除信息
     * User：YM
     * Date：2020/2/4
     * Time：下午10:03
     * @param $id
     * @return mixed
     */
    public function deleteUserInfo($id)
    {
        // 删除用户组下对应用户的缓存权限，缓存菜单
        $ids = $this->rolesService->getRoleUsers($id);
        if ($ids) {
            flushAnnotationCache('admin-user-permission',$ids);
            flushAnnotationCache('admin-user-menu',$ids);
        }

        $info = $this->rolesService->deleteInfo($id);
        return $info;
    }
    
    /**
     * deleteInfo
     * 根据id删除信息
     * User：YM
     * Date：2020/2/4
     * Time：下午10:58
     * @param $id
     * @return mixed
     */
    public function deleteInfo($id)
    {
        $info = $this->systemRoleModel->deleteInfo($id);

        return $info;
    }
    
    /**
     * getAllPermissions
     * 获取所有权限treelist
     * User：YM
     * Date：2020/2/4
     * Time：下午10:05
     * @return array
     */
    public function getAllPermissions()
    {
        $list = $this->permissionsService->getPermissionsTreeList();

        return $list;
    }

    /**
     * getUserRolePermissions
     * 获取角色对应权限list
     * User：YM
     * Date：2020/2/4
     * Time：下午10:06
     * @param $id 角色id
     * @return array
     */
    public function getUserRolePermissions($id)
    {
        if (!$id) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION, '参数不正确！');
        }
        $list = $this->getRolePermissions($id);
        $allList = $this->permissionsService->getPermissionsTreeList();
        foreach ($allList as $v) {
            if (!isset($list[$v['id']])) {
                $list[$v['id']] = [];
            }
        }

        $data = [
            'permissions_list' => $allList,
            'role_permissions' => $list,
        ];
        return $data;
    }
    
    /**
     * getRolePermissions
     * 获取角色的权限集合
     * User：YM
     * Date：2020/2/5
     * Time：上午11:07
     * @param $ids
     * @return array
     */
    public function getRolePermissions($ids)
    {
        $list = $this->systemRolesPermissionModel->getPermissionsByRolesIds($ids);
        $list = $this->handelPermissionsGroup($list);
        return $list;
    }

    /**
     * handelPermissionsGroup
     * 将角色权限列表，按着权限父级分组
     * User：YM
     * Date：2020/2/5
     * Time：上午11:05
     * @param array $list
     * @return array
     */
    public function handelPermissionsGroup($list = [])
    {
        if (!$list) {
            return [];
        }

        $tmp = [];
        foreach ($list as $v) {
            $tmp[$v['parent_id']][] = $v['system_permission_id'];
        }

        return $tmp;
    }

    /**
     * deleteRolesPermissions
     * 根据roleid删除对应的信息。
     * 由于角色权限一对多，所以每一次角色权限修改后会先做删除操作
     * User：YM
     * Date：2020/2/5
     * Time：上午11:12
     * @param $id
     * @return mixed
     */
    public function deleteRolesPermissions($id)
    {
        $info = $this->systemRolesPermissionModel->deleteRolesPermissions($id);

        return $info;
    }
    
    /**
     * saveUserRolesPermissions
     * 保存角色权限
     * User：YM
     * Date：2020/2/4
     * Time：下午10:06
     * @param $data
     * @return mixed
     */
    public function saveUserRolesPermissions($data)
    {
        $saveData = [];
        $roleId = $data['role_id'];
        $tmp = $data['permissions_id'];
        $time = date('Y-m-d H:i:s',time());

        $this->deleteRolesPermissions($roleId);
        foreach ($tmp as $v) {
            foreach ($v as $v1) {
                $saveData[] = ['system_role_id' => $roleId,'system_permission_id' => $v1,'created_at' => $time,'updated_at' => $time];
            }
        }
        $status = $this->saveRolesPermissions($saveData);
        // 删除用户组下对应用户的缓存权限，缓存菜单
        $ids = $this->getRoleUsers($roleId);
        if ($ids) {
            flushAnnotationCache('admin-user-permission',$ids);
            flushAnnotationCache('admin-user-menu',$ids);
        }

        return $status;
    }
    
    /**
     * saveRolesPermissions
     * 保存角色权限
     * User：YM
     * Date：2020/2/5
     * Time：上午11:13
     * @param array $data
     * @return mixed
     */
    public function saveRolesPermissions($data = [])
    {
        $info = $this->systemRolesPermissionModel->saveRolesPermissions($data);

        return $info;
    }

    /**
     * saveRolesUser
     * 保存用户对应角色
     * User：YM
     * Date：2020/2/5
     * Time：下午2:47
     * @param $data
     * @return mixed
     */
    public function saveRolesUser($data)
    {
        $info = $this->systemRolesUserModel->saveInfo($data);
        // 删除用户权限缓存
        flushAnnotationCache('admin-user-permission',$data['user_id']);
        // 删除用户菜单缓存
        flushAnnotationCache('admin-user-menu',$data['user_id']);
        return $info;
    }

    /**
     * saveUserRoles
     * 保存用户对应的角色，一对多
     * User：YM
     * Date：2020/2/5
     * Time：下午5:22
     * @param $data
     * @return mixed
     */
    public function saveUserRoles($data)
    {
        return $this->systemRolesUserModel->saveUserRoles($data);
    }

    /**
     * deleteRolesUser
     * 根据规则删除角色用户
     * User：YM
     * Date：2020/2/5
     * Time：下午2:41
     * @param $where
     * @return mixed
     */
    public function deleteRolesUser($where)
    {
        $info = $this->systemRolesUserModel->deleteRolesUser($where);
        // 删除用户权限缓存
        flushAnnotationCache('admin-user-permission',$where['user_id']);
        // 删除用户菜单缓存
        flushAnnotationCache('admin-user-menu',$where['user_id']);
        return $info;
    }
    
    /**
     * getUsers
     * 获取角色关联的用户list
     * User：YM
     * Date：2020/2/4
     * Time：下午10:06
     * @param $inputData
     * @return array
     */
    public function getUsers($inputData)
    {
        if (isset($inputData['role_id'])) {
            $inputData['system_role_id'] = $inputData['role_id'];
            unset($inputData['role_id']);
        }
        $pagesInfo = $this->getRolesUserPagesInfo($inputData);
        if (isset($inputData['page_size'])) {
            unset($inputData['page_size']);
        }
        if (isset($inputData['current_page'])) {
            unset($inputData['current_page']);
        }

        $list = $this->getRolesUserList($inputData,$pagesInfo['offset'],$pagesInfo['page_size']);
        $list = $this->handleRolesUserList($list);
        $data = [
            'pages' => $pagesInfo,
            'data' => $list
        ];

        return $data;
    }
    
    /**
     * handleRolesUserList
     * 处理角色用户的列表
     * User：YM
     * Date：2020/2/4
     * Time：下午10:06
     * @param array $list
     * @return array
     */
    public function handleRolesUserList($list = [])
    {
        $tmp = [];
        foreach ($list as $v) {
            $tmp[] = $this->userService->getInfo($v['user_id']);
        }

        return $tmp;
    }
    
    /**
     * getRolesUserPagesInfo
     * 获取角色关联用户的分页信息
     * User：YM
     * Date：2020/2/5
     * Time：下午12:05
     * @param array $where
     * @return array
     */
    public function getRolesUserPagesInfo($where = [])
    {
        $pageInfo = $this->systemRolesUserModel->getPagesInfo($where);

        return $pageInfo;
    }

    /**
     * getRolesUserList
     * 获取角色关联用户的列表信息
     * User：YM
     * Date：2020/2/4
     * Time：下午11:31
     * @param array $where 条件
     * @param int $offset 偏移
     * @param int $limit 取值数量
     * @return mixed
     */
    public function getRolesUserList($where = [],$offset = 0, $limit = 0)
    {
        $list = $this->systemRolesUserModel->getList($where,$offset, $limit);

        return $list;
    }

    /**
     * getRoleUsers
     * 获取角色对应的用户的id集合
     * User：YM
     * Date：2020/2/4
     * Time：下午11:32
     * @param $roleId
     * @return array
     */
    public function getRoleUsers($roleId)
    {
        $where = ['system_role_id' => $roleId];

        $list = $this->getRolesUserList($where);
        $ids = array_pluck($list,'user_id');
        return $ids;
    }


    /**
     * getUserRoles
     * 获取用户对应的角色的id集合
     * User：YM
     * Date：2020/2/5
     * Time：下午4:35
     * @param $userId
     * @return array
     */
    public function getUserRoles($userId)
    {
        $where = ['user_id' => $userId];

        $list = $this->getRolesUserList($where);
        $ids = array_pluck($list,'system_role_id');
        return $ids;
    }
    
    /**
     * searchUser
     * 角色添加用户，用户搜索
     * User：YM
     * Date：2020/2/4
     * Time：下午10:06
     * @param $inputData
     * @return array
     */
    public function searchUser($inputData)
    {
        $data = [];
        if (isset($inputData['search']) && $inputData['search']) {
            $ids = $this->getRoleUsers($inputData['role_id']);
            $data = $this->userService->searchUserList($inputData['search'],[],$ids);
        }

        return $data;
    }
    
    /**
     * addUserRoles
     * 为角色添加用户
     * User：YM
     * Date：2020/2/4
     * Time：下午10:07
     * @param $inputData
     * @return bool
     */
    public function addUserRoles($inputData)
    {
        $saveData = [];
        if (isset($inputData['user_id']) && isset($inputData['role_id'])) {
            $saveData['system_role_id'] = $inputData['role_id'];
            $saveData['user_id'] = $inputData['user_id'];
            $this->saveRolesUser($saveData);
        }

        return true;
    }
    
    /**
     * removeRolesUser
     * 为角色移除用户
     * User：YM
     * Date：2020/2/4
     * Time：下午10:07
     * @param $inputData
     * @return bool
     */
    public function removeRolesUser($inputData)
    {
        $where = [];
        if (isset($inputData['user_id']) && isset($inputData['role_id'])) {
            $where['system_role_id'] = $inputData['role_id'];
            $where['user_id'] = $inputData['user_id'];
            $this->deleteRolesUser($where);
        } else {
            throw new BusinessException(StatusCode::ERR_EXCEPTION, '请求参数错误！');
        }

        return true;
    }
}