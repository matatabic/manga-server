<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * PassportController.php
 *
 * 通行证相关
 *
 * User：YM
 * Date：2020/1/7
 * Time：下午6:43
 */


namespace App\Controller\Manga;

use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\Controller;
use App\Exception\BusinessException;
use App\Constants\StatusCode;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\Di\Annotation\Inject;
use Core\Common\Container\Auth;


/**
 * PassportController
 * 通行证相关
 * @package App\Controller\Manga
 * User：YM
 * Date：2020/1/7
 * Time：下午6:43
 *
 * @Controller(prefix="app_api/v1/passport")
 *
 * @property \Core\Services\UserService $userService
 */
class PassportController extends BaseController
{
    /**
     * @Inject()
     * @var Auth
     */
    protected $auth;

    /**
     * login
     * 处理登录
     * User：YM
     * Date：2020/1/8
     * Time：上午11:36
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="login")
     */
    public function login()
    {
        $inputData = $this->request->all();
        $validator = $this->validation->make(
            $inputData,
            [
                'account' => 'required',
                'password' => 'required',
            ],
            [
                'account.required'  => '账号不能为空',
                'password.required' => '密码不能为空',
            ]
        );
        if ($validator->fails()){
            $errorMessage = $validator->errors()->first();
            throw new BusinessException(StatusCode::ERR_EXCEPTION_USER,$errorMessage);
        }
        
        $reqParam = [
            'account'  => $inputData['account'],
            'password' => $inputData['password'],
        ];
        
        $result = $this->auth->handleLogin($reqParam);
      
        return $this->success($result,'登录成功');
    }
    
    /**
     * register
     * 注册登录
     * User：Eric
     * Date：2020/5/15
     * Time：下午6:36
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="register")
     */
    public function register()
    {
        $inputData = $this->request->all();
        $validator = $this->validation->make(
            $inputData,
            [
                'username' => 'required|unique:user',
                'mobile'   => 'unique:user',
                'password' => 'required',
            ],
            [
                'username.unique'   => '该用户名已被注册',
                'mobile.unique'     => '该手机号已被注册',
                'username.required' => '该用户名不能为空',
                'password.required' => '密码不能为空',
            ]
        );
        
        if ($validator->fails()){
            $errorMessage = $validator->errors()->first();
            throw new BusinessException(StatusCode::ERR_EXCEPTION,$errorMessage);
        }
        
        $reqParam = [
            'username'   => $inputData['username'],
            'password'   => $inputData['password'],
            'mobile'     => $inputData['mobile'],
            'user_roles' => [3]
        ];

        $id = $this->userService->addUser($reqParam,true);
        
        $result = $this->auth->handleLogin(['account'=>$reqParam['username'],'password'=>$reqParam['password']]);
        
        return $this->success($result,'注册成功');
    }
    
    /**
     * logout
     * 函数的含义说明
     * User：YM
     * Date：2020/3/8
     * Time：下午11:35
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="logout")
     */
    public function Logout()
    {
        $this->auth->logout();

        return $this->success('ok','退出登录成功');
    }
}