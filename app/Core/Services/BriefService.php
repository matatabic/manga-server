<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *
 * BookService.php
 *
 * User：Eric
 * Date：2020/2/9
 * Time：下午9:41
 */


namespace Core\Services;

use App\Exception\BusinessException;
use App\Constants\StatusCode;

/**
 * BriefService
 * 漫画简介管理服务
 * @package Core\Services
 * User：Eric
 * Date：2020/2/9
 * Time：下午9:41
 *
 * @property \Core\Common\Container\Auth $auth
 * @property \Core\Services\BookService $bookService
 * @property \Core\Services\CollectionService $collectionService
 * @property \Core\Services\MarkService $markService
 * @property \Core\Services\ChapterService $chapterService
 */
class BriefService extends BaseService
{
    /**
     * getBrief
     * 获取简介
     * User：Eric
     * Date：2020/8/10
     * Time：下午9:17
     * @param array $book_id
     * @return array $data
     */
    public function getBrief($id)
    {
        $where = ['book_id' => $id];
        
        $book = $this->bookService->getList(['id' => $id], [], 0, 1);
        
        if (count($book) == 0){
            throw new BusinessException(StatusCode::ERR_EXCEPTION,'book不存在!');
        }
        
        
        $userInfo = $this->auth->check();
        if ($userInfo) {
            $where['user_id'] = $userInfo['id'];
            $mark       = $this->markService->getList($where,['id'=>'desc'],0,1);
            $collection = $this->collectionService->getList($where);
        }
        
        $chapterList = $this->chapterService->getChapter($id);
        
        $data['bookInfo']['id']            = $book[0]['id'];
        $data['bookInfo']['title']         = $book[0]['title'];
        $data['bookInfo']['image']         = $book[0]['image'];
        $data['bookInfo']['category']      = $book[0]['category'];
        $data['bookInfo']['author']        = $book[0]['author'];
        $data['bookInfo']['description']   = $book[0]['description'];
        $data['bookInfo']['status']        = $book[0]['status'];
        $data['bookInfo']['chapter_total'] = $book[0]['chapter_total'];
        
        $data['book_update_info'] = $book[0]['updated_at'].'更新至第'.$book[0]['chapter_total'].'话';
        
        $data['collection_id'] = $collection[0]['id'] ?? 0;
        
        $data['markChapterNum'] = $mark[0]['chapter_num'] ?? 0;
        
        $data['markRoast'] = $mark[0]['roast'] ?? 0;
        
        $data['chapterList'] = $chapterList;
        
        return $data;
    }

    

}