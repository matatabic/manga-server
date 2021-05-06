<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * BookCategoryService.php
 *
 * User：Eric
 * Date：2020/2/9
 * Time：下午5:46
 */


namespace Core\Services;



/**
 * BookCategoryService
 * 漫画分类服务
 * @package Core\Services
 * User：Eric
 * Date：2020/2/9
 * Time：下午5:46
 *  
 * @property \App\Models\BookCategory $bookCategoryModel
 */
class BookCategoryService extends BaseService
{   
    /**
     * getCategoryById
     * 获取分页信息
     * User：YM
     * Date：2020/2/9
     * Time：下午9:42
     * @param array $where
     * @return array
     */
    public function getBookCategoryById($id)
    {
        $pageInfo = $this->bookCategoryModel->getCategoryById($id);

        return $pageInfo;
    }
    
    /**
     * saveInfo
     * 保存漫画分类
     * User：Eric
     * Date：2021/3/4
     * Time：下午11:10
     * @return array
     */
    public function saveInfo($book_id ,$category_ids)
    {
       foreach ($category_ids as &$v){
           $id = $this->bookCategoryModel->saveInfo(['book_id'=>$book_id ,'category_id'=>$v[1]]);
       }
       unset($v);
       
       return $id;
    }
    
    /**
     * saveInfo
     * 保存漫画分类
     * User：Eric
     * Date：2021/3/4
     * Time：下午11:30
     * @return array
     */
    public function deleteInfo($book_id)
    {
      
       $id = $this->bookCategoryModel->deleteInfo(['book_id'=>$book_id]);
       
       return $id;
    }
    
    /**
     * getPagesInfo
     * 获取分页信息
     * User：Eric
     * Date：2020/9/11
     * Time：下午15:52
     * @param array $where
     * @return array
     */
    public function getPagesInfo($where = [])
    {
        $pageInfo = $this->bookCategoryModel->getPagesInfo($where);

        return $pageInfo;
    }

    
}