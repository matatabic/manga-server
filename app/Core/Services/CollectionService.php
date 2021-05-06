<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * CollectService.php
 *
 * User：Eric
 * Date：2020/10/13
 * Time：下午9:13
 */


namespace Core\Services;


use App\Constants\StatusCode;
use App\Exception\BusinessException;


/**
 * CollectService
 * 收藏服务
 * @package Core\Services
 * User：Eric
 * Date：2020/10/13
 * Time：下午9:13
 *
 * @property \Core\Common\Container\Auth $auth
 * @property \App\Models\Collection $collectionModel
 * @property \App\Models\Mark $markModel
 * 
 */
class CollectionService extends BaseService
{
    /**
     * getCollection
     * 获取收藏
     * User：Eric
     * Date：2020/10/13
     * Time：下午9:17
     * @param array $reqParam
     * @return array $mix
     */
    public function getCollection($reqParam)
    {   
        $userInfo = $this->auth->check();

        $reqParam['user_id'] = $userInfo['id'];
        
        $pagesInfo = $this->getPagesInfo($reqParam);
        
        unset($reqParam['page_size']);
        unset($reqParam['current_page']);
        
        $order = ['sort' => 'DESC','id' => 'DESC'];
        
        $list = $this->collectionModel->getCollection($reqParam, $order, $pagesInfo['offset'], $pagesInfo['page_size']);

        foreach ($list as &$v){
            if($v['chapter_total'] > 0){
                $v['chapter_info'] = "第".$v['chapter_num']."话/第".$v['chapter_total']."话";
            }else{
                $v['chapter_info'] = "暂无章节";
            }
             
            unset($v['chapter_id']);
            unset($v['chapter_total']);
        }
        unset($v);
        
        $data = [
            'pages' => $pagesInfo,
            'list' => $list
        ];

        return $data;
    }
    
    /**
     * getList
     * 条件获取章节列表
     * User：Eric
     * Date：2020/8/11
     * Time：下午5:51
     * @param $where
     * @param $order
     * @param $pagesInfo
     * @param $pageSize
     * @return mixed
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0)
    {
        $list = $this->collectionModel->getList($where,$order,$offset,$limit);

        return $list;
    }
    
    
    /**
     * addUserCollection
     * 保存
     * User：Eric
     * Date：2020/2/11
     * Time：下午9:17
     * @param array $reqParam
     * @return int $id
     */
    public function addUserCollection($reqParam)
    {
        $userInfo = $this->auth->check();
        
        $reqParam['user_id'] = $userInfo['id'];

        $where = ['user_id' => $reqParam['user_id'],'book_id' => $reqParam['book_id']];
        
        $record = $this->collectionModel->getInfoByWhere($where);

        if (count($record) > 0){
            throw new BusinessException(StatusCode::ERR_EXCEPTION, '重复添加!');
        }

        return $this->collectionModel->saveInfo($reqParam);
    }

    /**
     * deleteCollect
     * 取消收藏
     * User：Eric
     * Date：2020/2/11
     * Time：下午9:17
     * @param $id
     * @return int $id
     */
    public function deleteCollection($id)
    {
        $userInfo = $this->auth->check();
        
        $id = explode(',',(string)$id);
        
        return $this->collectionModel->deleteCollection($userInfo['id'],$id);
    }

    /**
     * getPagesInfo
     * 获取分页信息
     * User：YM
     * Date：2020/2/11
     * Time：下午9:15
     * @param array $where
     * @return mixed
     */
    public function getPagesInfo($where = [])
    {
        $pageInfo = $this->collectionModel->getPagesInfo($where);

        return $pageInfo;
    }

}