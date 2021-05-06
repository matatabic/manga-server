<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * IntroController.php
 *
 * User：Eric
 * Date：2020/5/19
 * Time：上午11:32
 */


namespace App\Controller\Manga;


use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

/**
 * BriefController
 * 漫画管理
 * @package App\Controller\Manga
 * User：Eric
 * Date：2020/5/19
 * Time：上午11:32
 *
 * @Controller(prefix="app_api/v1/intro")
 *
 * @property \Core\Services\IntroService $introService
 */
class IntroController extends BaseController
{
    /**
     * getIntro
     * 漫画推荐列表
     * User：Eric
     * Date：2020/10/13
     * Time：上午11:34
     * @return \Psr\Http\Message\ResponseInterface
     * 
     * @GetMapping(path="getList")
     */
    public function getList()
    {
        $data = $this->introService->getIntro();
        
        return $this->success($data);
    }


}