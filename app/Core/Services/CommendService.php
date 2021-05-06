<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * CommendServices.php
 *
 * User：Eric
 * Date：2020/10/13
 * Time：下午9:13
 */


namespace Core\Services;


use App\Constants\StatusCode;
use App\Exception\BusinessException;


/**
 * CommendServices
 * 推荐服务
 * @package Core\Services
 * User：Eric
 * Date：2020/10/13
 * Time：下午9:13
 *
 * @property \App\Models\Commend $commendModel
 */
class CommendService extends BaseService
{

    /**
     * getPagesInfo
     * 获取分页信息
     * User：Eric
     * Date：2020/10/13
     * Time：下午5:52
     * @param array $where
     * @return array
     */
    public function getPagesInfo($where = [])
    {
        $pageInfo = $this->commendModel->getPagesInfo($where);
        
        return $pageInfo;
    }


}