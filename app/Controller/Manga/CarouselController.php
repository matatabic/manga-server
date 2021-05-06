<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * CarouselController.php
 *
 * User：Eric
 * Date：2020/10/13
 * Time：下午5:37
 */


namespace App\Controller\Manga;


use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\GetMapping;

/**
 * CarouselController
 * 轮播图控制器
 * @package App\Controller\Manga
 * User：getList
 * Date：2020/2/9
 * Time：下午5:37
 *
 * @Controller(prefix="app_api/v1/carousel")
 *
 *
 * @property \Core\Services\CarouselService $carouselService
 */
class CarouselController extends BaseController
{
    /**
     * getList
     * 轮播图列表
     * User：Eric
     * Date：2020/10/13
     * Time：下午5:41
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @GetMapping(path="getList")
     */
    public function getList()
    {
        $inputData = $this->request->all();
        
        $reqParam = [
            'page_size'    => $inputData['page_size'] ?? 5,
            'current_page' => $inputData['current_page'] ?? 1,
        ];

        $data = $this->carouselService->getCarouselList($reqParam);

        return $this->success($data);
    }


}