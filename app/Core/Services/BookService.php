<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * BookService.php
 *
 * User：Eric
 * Date：2020/2/9
 * Time：下午9:41
 */


namespace Core\Services;

use function _\groupBy;
use Hyperf\DbConnection\Db;
use App\Constants\StatusCode;
use App\Exception\BusinessException;

/**
 * BookService
 * 漫画管理服务
 * @package Core\Services
 * User：Eric
 * Date：2020/2/9
 * Time：下午9:41
 *
 * @property \App\Models\Book $bookModel
 * @property \Core\Services\BookCategoryService $bookCategoryService
 * @property \Core\Services\CommendService $commendService
 * @property \Core\Services\ChapterService $chapterService
 * @property \Core\Services\EpisodeService $episodeService
 */
class BookService extends BaseService
{
    /**
     * getList
     * 漫画列表
     * User：Eric
     * Date：2020/2/9
     * Time：下午9:42
     * @param array $where
     * @param array $order
     * @param int $offset
     * @param int $limit
     * @return mixed
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0)
    {
        $list = $this->bookModel->getList($where, $order, $offset, $limit);

        return $list;
    }
    
    /**
     * getBookList
     * 漫画列表
     * User：Eric
     * Date：2020/10/13
     * Time：下午9:42
     * @param array $reqParam
     * @return mixed
     */
    public function getBook($reqParam)
    {   
        $pagesInfo = $this->getPagesInfo($reqParam);

        unset($reqParam['page_size']);
        unset($reqParam['current_page']);
        
        $order = ['sort' => 'DESC','id' => 'DECS'];
        
        $list = $this->bookModel->getList($reqParam,$order,$pagesInfo['offset'],$pagesInfo['page_size']);
        
        foreach ($list as &$v) {
            $v['description_alias'] =  $v['description'] && mb_strlen($v['description']) > 32?mb_substr($v['description'],0,32).'...':'';
            $v['is_show'] = !!$v['is_show'];
        }
        unset($v);
        
        $data = [
            'pages' => $pagesInfo,
            'list' => $list
        ];
        
        return $data;
    }
    
    /**
     * getCommendList
     * 漫画推荐列表
     * User：Eric
     * Date：2020/10/13
     * Time：下午9:42
     * @param array $reqParam
     * @return mixed
     */
    public function getCommend($reqParam)
    {
        $pagesInfo = $this->commendService->getPagesInfo(['page_size'=>$reqParam['page_size'],'current_page'=>$reqParam['current_page']]);
        
        unset($reqParam['page_size']);
        unset($reqParam['current_page']);
        
        $list = $this->bookModel->getCommend($reqParam,[],$pagesInfo['offset'],$pagesInfo['page_size']);
        
        $newList = [];
        $n = -1;
        foreach ($list as $k => &$v){
            if(isset($newList[$n]['title']) && $newList[$n]['title']==$v['classify']){
                $newList[$n]['data'][0][] = $v;
            }else{
                $n++;
                $newList[$n]['title'] = $v['classify'];
                $newList[$n]['data'][0][] = $v;
            }
        }
        unset($v);
        
        $data = [
            'pages' => $pagesInfo,
            'list' => $newList
        ];
        
        return $data;
    }

    /**
     * getRandomList
     * 随机漫画列表
     * User：Eric
     * Date：2020/12/28
     * Time：下午14:39
     * @param int $page_size
     * @return array
     */
    public function getRandomList($page_size)
    {   
       $list = $this->bookModel->getRand($page_size);
       
       $data = [
          'list' => $list
       ];
       
       return $data;
    }
    
    /**
     * getIntro
     * 搜索推荐列表
     * User：Eric
     * Date：2020/11/13
     * Time：下午11:17
     * @return array
     */
    public function getIntro()
    {
       $data = $this->bookModel->getIntro();
       
       return groupBy($data,fn($val) => $val['index']);
    }
    
    /**
     * store
     * 漫画仓库
     * User：Eric
     * Date：2021/3/4
     * Time：下午11:02
     * @return array
     */
    public function store($inputData)
    {
       Db::beginTransaction();
       try{
           
           $id = $this->saveBook($inputData);
           
           $this->bookCategoryService->deleteInfo($id);

           $this->bookCategoryService->saveInfo($id,$inputData['category_ids']);
           
           Db::commit();
           
           return $id;
           
        } catch(\Throwable $ex){
            Db::rollBack();
            throw new BusinessException(StatusCode::ERR_EXCEPTION,'新增错误！');
        }
    }
    
    /**
     * saveBook
     * 保存漫画
     * 不接收数据库字段以外数据
     * User：Eric
     * Date：2021/2/21
     * Time：下午4:52
     * @param $saveData
     * @return null
     */
    public function saveBook($inputData)
    {   
        $saveData = [];
        if (isset($inputData['id']) && $inputData['id']){
            $saveData['id'] = $inputData['id'];
        }
        if (isset($inputData['title']) && $inputData['title']){
            $saveData['title'] = $inputData['title'];
        }
        if (isset($inputData['image']) && $inputData['image']){
            $saveData['image'] = $inputData['image'];
        }
        if (isset($inputData['author']) && $inputData['author']){
            $saveData['author'] = $inputData['author'];
        }
        if (isset($inputData['description']) && $inputData['description']){
            $saveData['description'] = $inputData['description'];
        }
        if (isset($inputData['status_id']) && $inputData['status_id']){
            $saveData['status_id'] = $inputData['status_id'];
        }
        if (isset($inputData['sort'])){
            $saveData['sort'] = $inputData['sort'];
        }
        if (isset($inputData['is_show'])){
            $saveData['is_show'] = $inputData['is_show'];
        }
        if (isset($inputData['is_delete'])){
            $saveData['is_delete'] = $inputData['is_delete'];
        }
        
        $id = $this->bookModel->saveInfo($saveData);
        
        return $id;
    }
    
    /**
     * orderBook
     * 漫画排序
     * User：Eric
     * Date：2020/2/22
     * Time：上午10:04
     * @param array $ids
     * @return bool
     */
    public function orderBook($ids = [])
    {
        if (count($ids) <= 1) {
            return true;
        }

        $order = count($ids); // 排序计数器
        foreach ($ids as $v) {
            $saveData = [
                'id' => $v,
                'sort' => $order
            ];
            $this->saveBook($saveData);
            $order--;
        }

        return true;
    }
    
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
        $pageInfo = $this->bookModel->getPagesInfo($where);

        return $pageInfo;
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
        $info = $this->bookModel->getInfo($id);
        
        $info['category_ids'] = $this->categoryService->getCategoryById($id);
        
        return $info;
    }
    
    /**
     * deleteInfo
     * 根据id删除信息
     * User：Eric
     * Date：2020/2/22
     * Time：下午5:52
     * @param $id
     * @return int
     */
    public function deleteInfo($id)
    {
        $where = ['id' => $id];
        
        $record = $this->bookModel->getInfoByWhere($where);

        if (count($record) == 0){
            throw new BusinessException(StatusCode::ERR_EXCEPTION, '该漫画不存在!');
        }
        
        $saveData = [
            'id' => $id,
            'is_delete' => 1
        ];
            
        $info = $this->saveBook($saveData);

        return $info;
    }
    
    /**
     * typeList
     * 获取类别
     * User：Eric
     * Date：2020/2/22
     * Time：下午10:19
     * @return mixed
     */
    public function typeList()
    {
        $list = $this->categoryService->getListByIdentify('manga-category');
        
        $data = [
          'list' => $list
        ];
       
        return $data;
    }
    
    /**
     * getDownloadList
     * 获取下载
     * User：Eric
     * Date：2021/5/2
     * Time：下午13:19
     * @return mixed
     */
    public function getDownloadList($reqParam)
    {
        $book    = $this->bookModel->getInfoByWhere(['id' => $reqParam['book_id']]);
        $chapter = $this->chapterService->getList($reqParam);
        $episode = $this->episodeService->getList($reqParam);
        
        foreach ($episode as $k => &$v) {
            $v['multiple'] =  isset($v['width']) ? round($v['height']/$v['width'],3) : null;
            $v['title'] = $this->number2chinese($v['chapter_num']);
        }
        unset($v);
        
        $data = [
          'book'    => $book,
          'chapter' => $chapter,
          'episode' => $episode
        ];
       
        return $data;
    }
    
    /**
     * 数字转换为中文
     * @param  integer  $num  目标数字
     */
    public function number2Chinese($num, $m = 1) {
    	switch($m) {
    		case 0:
    			$CNum = array(
    				array('零','壹','贰','叁','肆','伍','陆','柒','捌','玖'),
    				array('','拾','佰','仟'),
    				array('','萬','億','萬億')
    			);
    		break;
    		default:
    			$CNum = array(
    				array('零','一','二','三','四','五','六','七','八','九'),
    				array('','十','百','千'),
    				array('','万','亿','万亿')
    			);
    		break;
    	}
    
    	if (!is_numeric($num)) {
    		return false;
    	}
    
    	$flt = '';
    	if (is_integer($num)) {
    		$num = strval($num);
    	}else if(is_numeric($num)){
    		$num = strval($num);
    		$rs = explode('.',$num,2);
    		$num = $rs[0];
    		$flt = $rs[1];
    	}
    
    	$len = strlen($num);
    	$num = strrev($num);
    	$chinese = '';
    	
    	for($i = 0,$k=0;$i < $len; $i+=4,$k++){
    		$tmp_str = '';
    		$str = strrev(substr($num , $i,4));
    		$str = str_pad($str,4,'0',STR_PAD_LEFT);
    		for ($j = 0; $j < 4; $j++) { 
    			if($str{$j} !== '0'){
    				$tmp_str .= $CNum[0][$str{$j}] . $CNum[1][4-1-$j];
    			}
    		}
    		$tmp_str .= $CNum[2][$k];
    		$chinese = $tmp_str . $chinese;
    		unset($str);
    	}
    	if($flt !== ''){
    		$str = '';
    		for ($i=0; $i < strlen($flt); $i++) { 
    			$str .= $CNum[0][$flt{$i}];
    		}
    		$chinese .= "点{$str}";
    	}
    	return $chinese;
    }
}