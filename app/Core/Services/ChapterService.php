<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * ChapterService.php
 *
 * User：Eric
 * Date：2020/10/13
 * Time：下午9:41
 */


namespace Core\Services;



/**
 * ChapterService
 * 章节管理服务
 * @package Core\Services
 * User：Eric
 * Date：2020/10/13
 * Time：下午9:41
 *
 * @property \App\Models\Chapter $chapterModel
 */
class ChapterService extends BaseService
{
    /**
     * EpisodeBatchUpdate
     * 漫画画册批量更新
     * User：Eric
     * Date：2020/10/13
     * Time：下午9:42
     * @param $reqParam
     * @return mixed
     */
    public function updateData($data)
    {
        $list = $this->chapterModel->updateBatch($data);

    }
    
    /**
     * getList
     * 条件获取章节列表
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
        $list = $this->chapterModel->getList($where,$order,$offset,$limit);

        return $list;
    }
    
    /**
     * getList
     * 获取所有章节列表
     * User：Eric
     * Date：2020/10/13
     * Time：下午5:51
     * @param $where
     * @return mixed
     */
    public function getChapterList($where = [])
    {
        $data = $this->chapterModel->getList($where ,['chapter_num' => 'DESC']);
        
        $list = [
            'list' => $data
        ];
        
        return $list;
    }
    
    /**
     * getChapter
     * 章节列表
     * User：Eric
     * Date：2020/10/13
     * Time：下午9:42
     * @param $reqParam
     * @return mixed
     */
    public function getChapter($reqParam)
    {
        $list = $this->chapterModel->getChapter($reqParam);

        return $list;
    }
    
    /**
     * getPagesInfo
     * 获取分页信息
     * User：YM
     * Date：2020/2/9
     * Time：下午5:52
     * @param array $where
     * @return array
     */
    public function getPagesInfo($where = [])
    {
        $pageInfo = $this->chapterModel->getPagesInfo($where);

        return $pageInfo;
    }


    /**
     * getChapterCount
     * 获取总数
     * User：YM
     * Date：2020/2/11
     * Time：下午5:51
     * @param array $where
     * @return int
     */
    public function getChapterCount($where = [])
    {
        $count = $this->chapterModel->getCount($where);

        return $count;
    }
}