<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * UserController.php
 *
 * User：YM
 * Date：2020/2/5
 * Time：下午4:04
 */


namespace App\Controller\Admin;


use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\LoginAuthMiddleware;
use App\Middleware\CommonAuthMiddleware;

/**
 * UserController
 * 用户管理
 * @package App\Controller\Admin
 * User：YM
 * Date：2020/2/5
 * Time：下午4:04
 *
 * @Controller(prefix="app_api/user")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(CommonAuthMiddleware::class)
 * })
 *
 * @property \Core\Services\UserService $userService
 * @property \Core\Services\RolesService $rolesService
 * 
 */
class UserController extends BaseController
{
    /**
     * index
     * 用户列表，用户管理
     * User：YM
     * Date：2020/2/5
     * Time：下午4:05
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="getList")
     */
    public function getList()
    {
        $reqParam = $this->request->all();

        $list = $this->userService->getList($reqParam);
      
        return $this->success($data);
    }


    /**
     * store
     * 保存，新建、编辑都用该方法，区别是否有主键id
     * User：YM
     * Date：2020/2/5
     * Time：下午5:01
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Exception
     *
     * @PostMapping(path="store")
     */
    public function store()
    {
        $reqParam = $this->request->all();

        $id = $this->userService->addUser($reqParam);

        return $this->success($id);
    }

    /**
     * getInfo
     * 根据id获取单条记录信息
     * User：YM
     * Date：2020/2/5
     * Time：下午4:25
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="get_info")
     */
    public function getInfo()
    {
        $reqParam = $this->request->all();
        $info = $this->userService->getUserInfo($reqParam['id']);
        $data = [
            'info' => $info,
        ];

        return $this->success($data);
    }

    /**
     * destroy
     * 删除用户
     * User：YM
     * Date：2020/2/5
     * Time：下午4:26
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="delete")
     */
    public function destroy()
    {
        $reqParam = $this->request->all();
        $this->userService->deleteUserInfo($reqParam['id']);

        return $this->success('ok');
    }

    /**
     * getRoles
     * 获取绑定角色信息
     * User：YM
     * Date：2020/2/5
     * Time：下午4:26
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="get_roles")
     */
    public function getRoles()
    {
        $data = $this->rolesService->getList();

        return $this->success($data);
    }
}