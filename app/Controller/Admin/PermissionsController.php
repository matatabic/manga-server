<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * PermissionsController.php
 *
 * User：YM
 * Date：2020/1/11
 * Time：下午2:43
 */


namespace App\Controller\Admin;


use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use App\Constants\StatusCode;
use Throwable;
use App\Exception\BusinessException;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\LoginAuthMiddleware;
use App\Middleware\CommonAuthMiddleware;
use Hyperf\Di\Annotation\Inject;
use Core\Common\Container\Auth;
use Core\Common\Container\CommonPermission;

/**
 * PermissionsController
 * 权限控制器
 * @package App\Controller\Admin
 * User：YM
 * Date：2020/1/11
 * Time：下午2:43
 *
 * @Controller(prefix="admin_api/v1/permissions")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(CommonAuthMiddleware::class)
 * })
 *
 */
class PermissionsController extends BaseController
{
    /**
     * @Inject()
     * @var Auth
     */
    protected $auth;
    /**
     * @Inject()
     * @var CommonPermission
     */
    protected $commonPermission;
    
    /**
     * getUserPermissions
     * 获取当前用户拥有的权限
     * User：YM
     * Date：2020/1/11
     * Time：下午2:47
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="user_permissions")
     */
    public function getUserPermissions()
    {
        $userInfo = $this->auth->check();
        if (!isset($userInfo['id']) || !$userInfo['id']) {
            throw new BusinessException(StatusCode::ERR_NOT_LOGIN);
        }
        
        $list = $this->commonPermission->getUserAllPermissions($userInfo['id']);

        $data = [
            'list' => $list
        ];

        return $this->success($data);
    }

    /**
     * index
     * 权限列表，权限管理
     * User：YM
     * Date：2020/2/4
     * Time：下午8:23
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="list")
     */
    public function index()
    {

        $list = $this->permissionsService->getPermissionsTreeList();

        $data = [
            'list' => $list
        ];

        return $this->success($data);
    }

    /**
     * store
     * User：YM
     * Date：2020/2/4
     * Time：下午9:05
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @PostMapping(path="store")
     */
    public function store()
    {
        $inputData = $this->request->all();
        
        $reqParam['parent_id']            = $inputData['parent_id'] ?? null;
        $reqParam['system_permission_id'] = $inputData['system_permission_id'] ?? null;
        $reqParam['display_name']         = $inputData['display_name'] ?? null;
        $reqParam['icon']                 = $inputData['icon'] ?? null;
        $reqParam['order']                = $inputData['order'] ?? null;
        $reqParam['url']                  = $inputData['url'] ?? null;
        $reqParam['description']          = $inputData['description'] ?? null;
        $reqParam['additional']           = $inputData['additional'] ?? null;
        
        try {
            if ( !(isset($inputData['id']) && $inputData['id']) ) {
                $inputData['order'] = $this->permissionsService->getPermissionsCount(['parent_id' => $inputData['parent_id']]);
            }
            $id = $this->permissionsService->savePermissions($inputData);

            return $this->success($id);
        } catch (Throwable $throwable) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION_USER,$throwable->getMessage());
        }
    }

    /**
     * getInfo
     * 根据id获取单条记录信息
     * User：YM
     * Date：2020/2/4
     * Time：下午9:04
     * @return \Psr\Http\Message\ResponseInterface
     *  
     * @PostMapping(path="get_info")
     */
    public function getInfo()
    {
        $inputData = $this->request->all();
        $validator = $this->validation->make(
            $inputData,
            [
                'id' => 'required',
            ],
            [
                'id.required' => 'id不能为空',
            ]
        );
        if ($validator->fails()){
            $errorMessage = $validator->errors()->first();
            throw new BusinessException(StatusCode::ERR_EXCEPTION,$errorMessage);
        }
        $info = $this->permissionsService->getInfo($inputData['id']);
        
        $data = [
            'info' => $info,
        ];

        return $this->success($data);
    }

    /**
     * orderPermissions
     * 权限的拖拽排序
     * User：YM
     * Date：2020/2/4
     * Time：下午9:03
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @PostMapping(path="order")
     */
    public function orderPermissions()
    {
        $inputData = $this->request->all();
        $validator = $this->validation->make(
            $inputData,
            [
                'ids' => 'required',
            ],
            [
                'ids.required' => 'ids不能为空',
            ]
        );
        if ($validator->fails()){
            $errorMessage = $validator->errors()->first();
            throw new BusinessException(StatusCode::ERR_EXCEPTION,$errorMessage);
        }
        
        $this->permissionsService->orderPermissions($inputData['ids']);

        return $this->success('ok');
    }

    /**
     * destroy
     * 删除权限
     * User：YM
     * Date：2020/2/4
     * Time：下午9:02
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @PostMapping(path="delete")
     */
    public function destroy()
    {
        $inputData = $this->request->all();
        $validator = $this->validation->make(
            $inputData,
            [
                'id' => 'required',
            ],
            [
                'id.required' => 'id不能为空',
            ]
        );
        if ($validator->fails()){
            $errorMessage = $validator->errors()->first();
            throw new BusinessException(StatusCode::ERR_EXCEPTION,$errorMessage);
        }
        
        $this->permissionsService->deleteInfo($inputData['id']);

        return $this->success('ok');
    }



}