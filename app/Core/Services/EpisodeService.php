<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 *
 * EpisodeService.php
 *
 * User：Eric
 * Date：2020/2/9
 * Time：下午9:41
 */


namespace Core\Services;

use App\Constants\StatusCode;
use App\Exception\BusinessException;


/**
 * EpisodeService
 * 章节详情管理服务
 * @package Core\Services
 * User：Eric
 * Date：2020/2/9
 * Time：下午9:41
 *
 * @property \Core\Common\Container\Auth $auth
 * @property \Core\Services\MarkService $markService
 * @property \App\Models\Episode $episodeModel
 * @property \App\Models\Chapter $chapterModel
 * 
 */
class EpisodeService extends BaseService
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
        $list = $this->episodeModel->updateBatch($data);

    }
    
    /**
     * getEpisode
     * 漫画画册列表
     * User：Eric
     * Date：2020/2/9
     * Time：下午9:42
     * @param $reqParam
     * @return mixed
     */
    public function getEpisode($reqParam, $mark)
    {   
        $list = $this->getList(['chapter_num' => $reqParam['chapter_num'], 'book_id' => $reqParam['book_id']], ['roast' => 'ASC']);
        
        if(count($list) == 0){
            throw new BusinessException(StatusCode::ERR_EXCEPTION,'没有该章节');
        }
        
        $is_roast = true;
        foreach ($list as $k => &$v) {
            $v['multiple'] =  isset($v['width']) ? round($v['height']/$v['width'],3) : null;
            $v['title'] = $this->number2chinese($v['chapter_num']);
            if($reqParam['roast'] && $v['roast'] == $reqParam['roast']){
                $record = $v;
                $is_roast = false;
            }
        }
        unset($v);
        
        if($is_roast){
            $record = $list[0];
        }
        
        $userInfo = $this->auth->check();
       
        if (count($list) > 0 && $userInfo && $mark == 1) {
            $this->markService->saveMark([
                'book_id'     => $reqParam['book_id'],
                'chapter_id'  => $record['chapter_id'],
                'chapter_num' => $record['chapter_num'],
                'roast'       => $record['roast'],
            ]);
        }
        
        $pagesInfo =[
            'current_title'     => $this->number2chinese($record['chapter_num']),
            'current_chapter_id'=> (int)$record['chapter_id'],
            'current_chapter'   => (int)$record['chapter_num'],
            'episode_offset'    => (int)$record['number'],
            'episode_total'     => (int)count($list),
            'chapter_total'     => (int)$record['chapter_total'],
        ];
         
        $data = [
                'pages' => $pagesInfo,
                'list' => $list
            ];
            
        return $data;
    }
    
    /**
     * getList
     * 漫画画册列表
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
        $list = $this->episodeModel->getList($where,$order,$offset,$limit);

        return $list;
    }
    
    /**
     * getPagesInfo
     * 获取分页信息
     * User：Eric
     * Date：2020/2/9
     * Time：下午5:52
     * @param array $where
     * @return array
     */
    public function getPagesInfo($where = [])
    {
        $pageInfo = $this->chapterService->getPagesInfo($where);
        
        return $pageInfo;
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