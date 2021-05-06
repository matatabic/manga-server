<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * MarkService.php
 *
 * User：Eric
 * Date：2020/10/13
 * Time：下午9:13
 */


namespace Core\Services;


use App\Constants\StatusCode;
use App\Exception\BusinessException;


/**
 * MarkService
 * 标记服务
 * @package Core\Services
 * User：Eric
 * Date：2020/10/13
 * Time：下午9:13
 *
 * @property \App\Models\Mark $markModel
 * @property \Core\Common\Container\Auth $auth
 * @property \App\Models\Episode $episodeModel
 */
class MarkService extends BaseService
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
        $list = $this->markModel->getList($where,$order,$offset,$limit);

        return $list;
    }
    
    /**
     * getMark
     * 获取阅读历史
     * User：Eric
     * Date：2020/10/13
     * Time：下午9:17
     * @param array $reqParam
     * @return array $data
     */
    public function getDownload($reqParam)
    {
        $userInfo = $this->auth->check();
        
        if ($userInfo) {
            $reqParam['user_id'] = $userInfo['id'];
        }
         
        $pagesInfo = $this->getDownloadPagesInfo($reqParam);
        
        $reqParam['ids'] = join(",",$reqParam['ids']);
        
        unset($reqParam['current_page']);
        unset($reqParam['page_size']);

        $list = $this->markModel->getDownload($reqParam, $pagesInfo['offset'], $pagesInfo['page_size']);
        
        $data = [
            'pages' => $pagesInfo,
            'list' => $list,
        ];

        return $data;
    }
    
    /**
     * getMark
     * 获取阅读历史
     * User：Eric
     * Date：2020/10/13
     * Time：下午9:17
     * @param array $reqParam
     * @return array $data
     */
    public function getMark($reqParam)
    {
        $userInfo = $this->auth->check();
        
        if ($userInfo) {
            $reqParam['user_id'] = $userInfo['id'];
        }

        $pagesInfo = $this->getPagesInfo($reqParam);

        unset($reqParam['current_page']);
        unset($reqParam['page_size']);
        
        $list = $this->markModel->getMark($reqParam, $pagesInfo['offset'], $pagesInfo['page_size']);
        
        $newList = [];
        $newList[0]['title'] = '3天内';
        $newList[1]['title'] = '7天内';
        $newList[2]['title'] = '更早';
      
        foreach ($list as $k => &$v){
            if(strtotime($v['created_at']) - strtotime("-3 day") > 0 && strtotime($v['created_at']) - strtotime("-3 day") < 86400*3){
                $newList[0]['data'][] = $v;  
            }else if(strtotime($v['created_at']) - strtotime("-7 day") >0 && strtotime($v['created_at']) - strtotime("-7 day") < 86400*7){
                $newList[1]['data'][] = $v;
            }else{
                $newList[2]['data'][] = $v;
            }
        }
        unset($v);
        
        foreach ($newList as $k => &$v){
            if(!isset($v['data'])){
                unset($newList[$k]);
            }
        }
        unset($v);
        
        $data = [
            'pages' => $pagesInfo,
            'list' => array_merge($newList),
        ];

        return $data;
    }
    
    /**
     * saveMark
     * 保存阅读历史
     * User：Eric
     * Date：2020/12/9
     * Time：上午11:46
     * @param $reqParam
     * @return mixed
     */
    public function saveMark($reqParam)
    {
        $record = $this->episodeModel->getInfoByWhere($reqParam);

        if (count($record) == 0){
            throw new BusinessException(StatusCode::ERR_EXCEPTION, '添加的章节不存在!');
        }
        
        $userInfo = $this->auth->check();
    
        $reqParam['user_id'] = $userInfo['id'];
        
        $result = $this->markModel->saveInfo($reqParam);

        return $result;
    }
    
    /**
     * deleteMark
     * 删除历史
     * User：Eric
     * Date：2020/12/3
     * Time：下午4:41
     * @param $book_id
     * @return int $id
     */
    public function deleteMark($book_id)
    {
        $userInfo = $this->auth->check();
        
        $book_id = explode(',',(string)$book_id);
        
        return $this->markModel->deleteMark($userInfo['id'],$book_id);
    }
    
    /**
     * getPagesInfo
     * 获取分页信息
     * User：Eric
     * Date：2020/10/13
     * Time：下午9:15
     * @param array $where
     * @return mixed
     */
    public function getPagesInfo($where = [])
    {
        $pageInfo = $this->markModel->getPagesInfo($where);

        return $pageInfo;
    }
    
    public function getDownloadPagesInfo($where = [])
    {
        if (isset($where['page_size'])) {
            $pageSize = $where['page_size']>0?$where['page_size']:10;
        }
        if (isset($where['current_page'])) {
            $currentPage = $where['current_page']>0?$where['current_page']:1;
        }
        
        $offset = ($currentPage-1)*$pageSize;

        $total = count($where['ids']);

        return [
            'current_page' => (int)$currentPage,
            'offset' => (int)$offset,
            'page_size' => (int)$pageSize,
            'total' => (int)$total,
        ];
    }
    
}