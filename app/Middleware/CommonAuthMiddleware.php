<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use Core\Common\Container\CommonPermission;
use Core\Common\Container\Auth;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * CommonAuthMiddleware
 * 验证用户是否有路由请求权限
 * @package App\Middleware
 * User：YM
 * Date：2020/3/4
 * Time：下午11:01
 */
class CommonAuthMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject()
     * @var RequestInterface
     */
    private $request;

    /**
     * @Inject()
     * @var Auth
     */
    private $auth;

    /**
     * @Inject()
     * @var CommonPermission
     */
    private $commonPermission;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uid = $this->auth->check(false);
        if ( $uid != config('super_admin') ) {
            $uri = $this->request->getRequestUri();
            $userPermissions = $this->commonPermission->getUserAllPermissions($uid);
            $uriPermissions = $this->commonPermission->getPermissionsFromUri($uri);
            if ( count(array_intersect($userPermissions,$uriPermissions)) == 0 ) {
                throw new BusinessException(StatusCode::ERR_NOT_ACCESS);
            }
        }
        return $handler->handle($request);
    }
}