<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * BookController.php
 *
 * User：Eric
 * Date：2020/2/9
 * Time：上午11:32
 */


namespace App\Controller\Admin;


use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use App\Exception\BusinessException;
use App\Constants\StatusCode;

/**
 * BookController
 * 漫画管理
 * @package App\Controller\Admin
 * User：Eric
 * Date：2020/10/13
 * Time：上午11:32
 *
 * @Controller(prefix="admin_api/v1/book")
 *
 * @property \Core\Services\BookService $bookService
 * 
 */
class BookController extends BaseController
{
    
    /**
     * getList
     * 漫画列表
     * User：Eric
     * Date：2020/10/13
     * Time：上午11:34
     * @return \Psr\Http\Message\ResponseInterface
     * 
     * @GetMapping(path="getList")
     */
    public function getList()
    {
        $inputData = $this->request->all();
        $validator = $this->validation->make(
            $inputData,
            [
                'category_id'  => 'array',
                'status_id'    => 'integer',
            ]
        );

        if ($validator->fails()){
            $errorMessage = $validator->errors()->first();
            throw new BusinessException(StatusCode::ERR_EXCEPTION,$errorMessage);
        }
        
        $reqParam = [
            'title'        => $inputData['title'] ?? null,
            'category_id'  => $inputData['category_id'] ?? null,
            'status_id'    => $inputData['status_id'] ?? null,
            'page_size'    => $inputData['page_size'] ?? 10,
            'current_page' => $inputData['current_page'] ?? 1,
            'is_enable'    => $inputData['is_enable'] ?? null,
            'is_show'      => $inputData['is_show'] ?? 0,
        ];

        $data = $this->bookService->getBook($reqParam);
        
        return $this->success($data);
    }
    
    /**
     * store
     * 漫画保存
     * User：Eric
     * Date：2021/2/21
     * Time：下午4:35
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="store")
     */
    public function store()
    {
        $inputData = $this->request->all();
        $validator = $this->validation->make(
            $inputData,
            [
                'id'        => 'integer',
                'status_id' => 'integer',
                'is_enable' => 'integer',
            ]
        );

        if ($validator->fails()){
            $errorMessage = $validator->errors()->first();
            throw new BusinessException(StatusCode::ERR_EXCEPTION,$errorMessage);
        }

        $id = $this->bookService->store($inputData);

        return $this->success($id);
    }
    
    /**
     * orderBook
     * 漫画拖拽排序
     * User：Eric
     * Date：2020/2/22
     * Time：上午10:05
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="order")
     */
    public function orderBook()
    {
        $reqParam = $this->request->all();
        $this->bookService->orderBook($reqParam['ids']);

        return $this->success('ok');
    }
    
    /**
     * getInfo
     * 获取漫画详情
     * User：Eric
     * Date：2020/2/22
     * Time：下午3:15
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="get_info")
     */
    public function getInfo()
    {
        $reqParam = $this->request->all();
        $info = $this->bookService->getInfo($reqParam['id']);

        $data = [
            'info' => $info,
        ];

        return $this->success($data);
    }
    
    /**
     * destroy
     * 删除漫画
     * User：YM
     * Date：2020/2/9
     * Time：下午5:43
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="delete")
     */
    public function destroy()
    {
        $reqParam = $this->request->all();
        $this->bookService->deleteInfo($reqParam['id']);

        return $this->success('ok');
    }
    
    /**
     * typeList
     * 获取类型列表
     * User：Eric
     * Date：2020/2/22
     * Time：下午10:19
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="type_list")
     */
    public function typeList()
    {
        $list = $this->bookService->typeList();
        return $this->success($list);
    }
}