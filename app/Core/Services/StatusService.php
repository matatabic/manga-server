<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * StatusService.php
 *
 * User：Eric
 * Date：2020/10/16
 * Time：上午10:11
 */


namespace Core\Services;


use App\Constants\StatusCode;
use App\Exception\BusinessException;


/**
 * StatusService
 * 漫画状态服务
 * @package Core\Services
 * User：Eric
 * Date：2020/10/16
 * Time：上午10:11
 *
 * @property \App\Models\Status $statusModel
 */
class StatusService extends BaseService
{
    /**
     * getList
     * 条件获取漫画状态
     * User：Eric
     * Date：2020/10/16
     * Time：上午10:11
     * @param $where
     * @param $order
     * @param $pagesInfo
     * @param $pageSize
     * @return mixed
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0)
    {
        $list = $this->statusModel->getList($where,$order,$offset,$limit);
        
        $data = [
            'list' => $list
        ];
        
        return $data;
    }
    
    

}