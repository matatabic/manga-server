<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * CategoryController.php
 *
 * User：Eric
 * Date：2020/2/9
 * Time：下午5:37
 */


namespace App\Controller\Manga;


use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

/**
 * CategoryController
 * 分类控制器
 * @package App\Controller\Manga
 * User：Eric
 * Date：2020/2/9
 * Time：下午5:37
 *
 * @Controller(prefix="app_api/v1/category")
 *
 *
 * @property \Core\Services\CategoryService $categoryService
 */
class CategoryController extends BaseController
{
    /**
     * getList
     * 分类列表
     * User：Eric
     * Date：2020/9/11
     * Time：下午16:38
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @GetMapping(path="getList")
     */
    public function getList()
    {
        $data = $this->categoryService->getCategory($reqParam);

        return $this->success($data);
    }
    
    /**
     * getTreeList
     * 分类树列表
     * User：Eric
     * Date：2020/9/11
     * Time：下午16:38
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @GetMapping(path="getTreeList")
     */
    public function getTreeList()
    {
        $data = $this->categoryService->getTreeList();

        return $this->success($data);
    }

}