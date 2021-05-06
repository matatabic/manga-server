<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * StatusControllerStatusController.php
 *
 * User：Eric
 * Date：2020/10/16
 * Time：上午9:57
 */


namespace App\Controller\Manga;


use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

/**
 * StatusController
 * 漫画状态控制器
 * @package App\Controller\Manga
 * User：Eric
 * Date：2020/10/16
 * Time：上午9:57
 *
 * @Controller(prefix="app_api/v1/status")
 *
 * @property \Core\Services\StatusService $statusService
 */
class StatusController extends BaseController
{
    /**
     * getList
     * 漫画状态列表
     * User：Eric
     * Date：2020/10/16
     * Time：上午9:57
     * @return \Psr\Http\Message\ResponseInterface
     * 
     * @GetMapping(path="getList")
     */
    public function getList()
    {
        $data = $this->statusService->getList();
        
        return $this->success($data);
    }


}