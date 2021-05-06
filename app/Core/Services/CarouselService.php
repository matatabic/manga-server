<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * CarouselService.php
 *
 * User：YM
 * Date：2020/2/9
 * Time：下午5:46
 */


namespace Core\Services;


/**
 * CarouselService
 * 轮播图服务
 * @package Core\Services
 * User：YM
 * Date：2020/2/9
 * Time：下午5:46
 *
 * @property \App\Models\AdCarousel $adCarouselModel
 * @property \Core\Services\AttachmentService $attachmentService
 * @property \Core\Services\CategoryService $categoryService
 */
class CarouselService extends BaseService
{
    /**
     * getList
     * 条件获取轮播图列表
     * User：YM
     * Date：2020/2/9
     * Time：下午5:51
     * @param array $where 查询条件
     * @param array $order 排序条件
     * @param int $offset 偏移
     * @param int $limit 条数
     * @return mixed
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0)
    {

        $list = $this->adCarouselModel->getList($where,$order,$offset,$limit);

        foreach ($list as $k => $v) {
            $info = $this->attachmentService->getInfo($v['image']);
            $list[$k]['image_url'] = isset($info['full_path'])?$info['full_path']:'';
            $list[$k]['target'] = $v['is_new_win']?'_blank':'_self';
        }

        return $list;
    }
    
    /**
     * getCarouselList
     * 获取列表
     * User：YM
     * Date：2020/2/9
     * Time：下午5:49
     * @param $inputData
     * @return array
     */
    public function getCarouselList($inputData)
    {
        $pagesInfo = $this->getPagesInfo($inputData);
        $order = ['order'=>'ASC'];
        $list = $this->getList([],$order,$pagesInfo['offset'],$pagesInfo['page_size']);
        $data = [
            'pages' => $pagesInfo,
            'data' => $list
        ];

        return $data;
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
        $pageInfo = $this->adCarouselModel->getPagesInfo($where);

        return $pageInfo;
    }

    /**
     * saveCarousel
     * 保存轮播图，构造数据，防止注入
     * 不接收数据库字段以外数据
     * User：YM
     * Date：2020/2/9
     * Time：下午5:52
     * @param $inputData
     * @return null
     */
    public function saveCarousel($inputData)
    {
        $saveData = [];
        if (isset($inputData['id']) && $inputData['id']){
            $saveData['id'] = $inputData['id'];
        }
        if (isset($inputData['title']) && $inputData['title']){
            $saveData['title'] = $inputData['title'];
        }
        if (isset($inputData['c_type'])){
            $saveData['c_type'] = $inputData['c_type'];
        }
        if (isset($inputData['image']) && $inputData['image']){
            $saveData['image'] = $inputData['image'];
        }
        if (isset($inputData['url'])){
            $saveData['url'] = $inputData['url'];
        }
        if (isset($inputData['order'])){
            $saveData['order'] = $inputData['order'];
        }
        if (isset($inputData['bg_color'])){
            $saveData['bg_color'] = $inputData['bg_color'];
        }
        if (isset($inputData['is_new_win'])){
            $saveData['is_new_win'] = $inputData['is_new_win'];
        }
        if (isset($inputData['additional'])){
            $saveData['additional'] = $inputData['additional'];
        }
        if (isset($inputData['is_show'])){
            $saveData['is_show'] = $inputData['is_show'];
        }
        if (isset($inputData['description'])){
            $saveData['description'] = $inputData['description'];
        }
        $id = $this->adCarouselModel->saveInfo($saveData);

        return $id;
    }
    
    /**
     * addCarousel
     * 保存
     * User：YM
     * Date：2020/2/9
     * Time：下午5:49
     * @param $data
     * @return mixed
     */
    public function addCarousel($data)
    {
        if ( !(isset($data['id']) && $data['id']) ) {
            $data['order'] = $this->getCarouselCount();
        }
        return $this->saveCarousel($data);
    }
    
    /**
     * getInfo
     * 根据id获取信息
     * User：YM
     * Date：2020/2/9
     * Time：下午5:52
     * @param $id
     * @return \App\Models\BaseModel|\Hyperf\Database\Model\Model|null
     */
    public function getInfo($id)
    {
        $info = $this->adCarouselModel->getInfo($id);
        $info['is_new_win'] = (string)$info['is_new_win'];
        $info['is_show'] = (string)$info['is_show'];
        $info['image_info'] = $this->attachmentService->getInfo($info['image']);
        return $info;
    }

    /**
     * deleteInfo
     * 根据id删除信息
     * User：YM
     * Date：2020/2/9
     * Time：下午5:52
     * @param $id
     * @return int
     */
    public function deleteInfo($id)
    {
        $info = $this->adCarouselModel->deleteInfo($id);

        return $info;
    }
    
    /**
     * orderCarousel
     * 轮播图排序
     * User：YM
     * Date：2020/2/9
     * Time：下午5:49
     * @param array $ids
     * @return bool
     */
    public function orderCarousel($ids = [])
    {
        if (count($ids) <= 1) {
            return true;
        }

        $order = 0; // 排序计数器
        foreach ($ids as $v) {
            $saveData = [
                'id' => $v,
                'order' => $order
            ];
            $this->saveCarousel($saveData);
            $order++;
        }

        return true;
    }
    
    /**
     * typeList
     * 获取类别
     * User：YM
     * Date：2020/2/9
     * Time：下午9:40
     * @return mixed
     */
    public function typeList()
    {
        $list = $this->categoryService->getListByIdentify('carousel-category');

        return $list;
    }
    
    /**
     * getCarouselCount
     * 根据条件获取总数
     * User：YM
     * Date：2020/2/9
     * Time：下午10:50
     * @return int
     */
    public function getCarouselCount()
    {
        $count = $this->adCarouselModel->getCount();

        return $count;
    }
}