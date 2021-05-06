<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * StatisticsController.php
 *
 * User：YM
 * Date：2020/2/18
 * Time：下午4:58
 */


namespace App\Controller\Admin;

use App\Constants\StatusCode;
use App\Exception\BusinessException;
use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\LoginAuthMiddleware;
use App\Middleware\CommonAuthMiddleware;

/**
 * StatisticsController
 * 数据统计
 * @package App\Controller\Admin
 * User：YM
 * Date：2020/2/18
 * Time：下午4:58
 *
 * @Controller(prefix="admin_api/v1/statistics")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(CommonAuthMiddleware::class)
 * })
 *
 * @property \Core\Services\StatisticsService $statisticsService
 */
class StatisticsController extends BaseController
{
    /**
     * flowData
     * 流量统计
     * User：YM
     * Date：2020/2/18
     * Time：下午9:27
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="flow_data")
     */
    public function flowData()
    {
        $reqParam = $this->request->all();
        
        if(!isset($reqParam['start_time']) || empty($reqParam['start_time'])){
            throw new BusinessException(StatusCode::ERR_EXCEPTION,'开始时间为空');
        }
        if(!isset($reqParam['end_time']) || empty($reqParam['end_time'])){
            throw new BusinessException(StatusCode::ERR_EXCEPTION,'结束时间为空');
        }
        if ( (strtotime($reqParam['end_time'])-strtotime($reqParam['start_time']))/86400 > 30 ) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION,'最大查询30天');
        }
        
        $list = $this->statisticsService->getFlowData($reqParam);

        return $this->success($list);
    }

    /**
     * regionData
     * 地域统计
     * User：YM
     * Date：2020/2/19
     * Time：下午9:12
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="region_data")
     */
    public function regionData()
    {
        $reqParam = $this->request->all();
        
        if(!isset($reqParam['start_time']) || empty($reqParam['start_time'])){
            throw new BusinessException(StatusCode::ERR_EXCEPTION,'开始时间为空');
        }
        if(!isset($reqParam['end_time']) || empty($reqParam['end_time'])){
            throw new BusinessException(StatusCode::ERR_EXCEPTION,'结束时间为空');
        }
        if ( (strtotime($reqParam['end_time'])-strtotime($reqParam['start_time']))/86400 > 30 ) {
            throw new BusinessException(StatusCode::ERR_EXCEPTION,'最大查询30天');
        }
        
        $reqParam['start_time'] = strtotime($reqParam['start_time'].' 00:00:00');
        $reqParam['end_time'] = strtotime($reqParam['end_time']. '23:59:59');
        
        $list = $this->statisticsService->getRegionData($reqParam);

        return $this->success($list);
    }
}