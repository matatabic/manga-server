<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * SysLogService.php
 *
 * User：YM
 * Date：2020/2/16
 * Time：上午11:48
 */


namespace Core\Services;

/**
 * SysLogService
 * 系统日志服务
 * @package Core\Services
 * User：YM
 * Date：2020/2/16
 * Time：上午11:48
 *
 * @property \App\Models\Log $logModel
 */
class SysLogService extends BaseService
{
    /**
     * getList
     * 获取列表
     * User：YM
     * Date：2020/2/10
     * Time：下午10:38
     * @param $inputData
     * @return array
     */
    public function getSysLogList($inputData)
    {
        $pagesInfo = $this->getPagesInfo($inputData);
        $where = $inputData;
        unset($where['page_size']);
        unset($where['current_page']);
        $order = ['id'=>'DESC'];
        $list = $this->getList($where,$order,$pagesInfo['offset'],$pagesInfo['page_size']);

        foreach ($list as &$v) {
            $v['user_id_alias'] = $v['user_id']?mb_substr($v['user_id'],0,16).'...':'';
            $v['channel_alias'] = $v['channel'] && mb_strlen($v['channel']) > 24?mb_substr($v['channel'],0,24).'...':'';
            $v['uri_alias'] = $v['uri'] && mb_strlen($v['uri']) > 24?mb_substr($v['uri'],0,24).'...':'';
            $v['qid_alias'] = $v['qid'] && mb_strlen($v['qid']) > 24?mb_substr($v['qid'],0,24).'...':'';
            $v['arguments_alias'] = $v['arguments'] && mb_strlen($v['arguments']) > 32?mb_substr($v['arguments'],0,32).'...':'';
            $v['message_alias'] = $v['message'] && mb_strlen($v['message']) > 32?mb_substr($v['message'],0,32).'...':'';
            $v['platform_device'] = $v['platform'].' '.$v['device'];
            $v['request_body_size_alias'] = formatBytes($v['request_body_size']);
            $v['response_body_size_alias'] = formatBytes($v['response_body_size']);
            $v['execution_time_alias'] = $v['execution_time']?$v['execution_time'].'秒':'';
        }
        unset($v);

        $data = [
            'pages' => $pagesInfo,
            'data' => $list
        ];

        return $data;
    }
    
    /**
     * getList
     * 条件获取列表
     * User：YM
     * Date：2020/2/10
     * Time：下午10:34
     * @param array $where 查询条件
     * @param array $order 排序条件
     * @param int $offset 偏移
     * @param int $limit 条数
     * @return mixed
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0)
    {
        $list = $this->logModel->getList($where,$order,$offset,$limit);
        return $list;
    }

    /**
     * getPagesInfo
     * 获取分页信息
     * User：YM
     * Date：2020/2/10
     * Time：下午10:35
     * @param array $where
     * @return mixed
     */
    public function getPagesInfo($where = [])
    {
        $pageInfo = $this->logModel->getPagesInfo($where);

        return $pageInfo;
    }
}