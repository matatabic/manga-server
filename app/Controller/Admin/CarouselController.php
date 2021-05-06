<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * CarouselController.php
 *
 * User：YM
 * Date：2020/2/9
 * Time：下午5:37
 */


namespace App\Controller\Admin;


use App\Controller\BaseController;
use App\Middleware\LoginAuthMiddleware;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PostMapping;
use App\Middleware\CommonAuthMiddleware;

/**
 * CarouselController
 * 轮播图控制器
 * @package App\Controller\Admin
 * User：YM
 * Date：2020/2/9
 * Time：下午5:37
 *
 * @Controller(prefix="admin_api/v1/carousel")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(CommonAuthMiddleware::class)
 * })
 *
 * @property \Core\Services\CarouselService $carouselService
 */
class CarouselController extends BaseController
{
    /**
     * index
     * 轮播图列表
     * User：YM
     * Date：2020/2/9
     * Time：下午5:41
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="list")
     */
    public function index()
    {
        $reqParam = $this->request->all();
        $list = $this->carouselService->getCarouselList($reqParam);

        $data = [
            'pages' => $list['pages'],
            'list' => $list['data'],
        ];

        return $this->success($data);
    }

    /**
     * store
     * 轮播图保存
     * User：YM
     * Date：2020/2/9
     * Time：下午5:41
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="store")
     */
    public function store()
    {
        $reqParam = $this->request->all();
        $id = $this->carouselService->addCarousel($reqParam);

        return $this->success($id);
    }

    /**
     * getInfo
     * 获取轮播图详情
     * User：YM
     * Date：2020/2/9
     * Time：下午5:43
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="get_info")
     */
    public function getInfo()
    {
        $reqParam = $this->request->all();
        $info = $this->carouselService->getInfo($reqParam['id']);

        $data = [
            'info' => $info,
        ];

        return $this->success($data);
    }

    /**
     * destroy
     * 删除轮播图
     * User：YM
     * Date：2020/2/9
     * Time：下午5:43
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="delete")
     */
    public function destroy()
    {
        $reqParam = $this->request->all();
        $this->carouselService->deleteInfo($reqParam['id']);

        return $this->success('ok');
    }

    /**
     * orderCarousel
     * 轮播图拖拽排序
     * User：YM
     * Date：2020/2/9
     * Time：下午5:44
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="order")
     */
    public function orderCarousel()
    {
        $reqParam = $this->request->all();
        $this->carouselService->orderCarousel($reqParam['ids']);

        return $this->success('ok');
    }

    /**
     * typeList
     * 获取类型列表
     * User：YM
     * Date：2020/2/9
     * Time：下午9:39
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="type_list")
     */
    public function typeList()
    {
        $list = $this->carouselService->typeList();
        return $this->success($list);
    }
}