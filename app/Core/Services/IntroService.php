<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * IntroService.php
 *
 * User：Eric
 * Date：2020/10/13
 * Time：下午9:13
 */


namespace Core\Services;


use App\Constants\StatusCode;
use App\Exception\BusinessException;


/**
 * IntroService
 * 推介搜索服务
 * @package Core\Services
 * User：Eric
 * Date：2020/10/13
 * Time：下午9:13
 *
 * @property \App\Models\Intro $introModel
 */
class IntroService extends BaseService
{
    /**
     * getList
     * 条件获取阅读历史
     * User：Eric
     * Date：2020/10/13
     * Time：下午5:51
     * @param $where
     * @param $order
     * @param $pagesInfo
     * @param $pageSize
     * @return mixed
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0)
    {
        $list = $this->introModel->getList($where,$order,$offset,$limit);

        return $list;
    }
    
    /**
     * getIntro
     * 获取搜索推介
     * User：Eric
     * Date：2020/10/13
     * Time：下午9:17
     * @param array $reqParam
     * @return array $data
     */
    public function getIntro()
    {
        
    }
    

}