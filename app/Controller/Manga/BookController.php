<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * BookController.php
 *
 * User：Eric
 * Date：2020/5/19
 * Time：上午11:32
 */


namespace App\Controller\Manga;


use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use App\Exception\BusinessException;
use App\Constants\StatusCode;

/**
 * BookController
 * 漫画管理
 * @package App\Controller\Manga
 * User：Eric
 * Date：2020/10/13
 * Time：上午11:32
 *
 * @Controller(prefix="app_api/v1/book")
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
                'category_ids'=> 'array',
                'status_id'   => 'integer',
            ]
        );

        if ($validator->fails()){
            $errorMessage = $validator->errors()->first();
            throw new BusinessException(StatusCode::ERR_EXCEPTION,$errorMessage);
        }
        
        $reqParam = [
            'title'        => $inputData['title'] ?? null,
            'category_ids' => $inputData['category_ids'] ?? null,
            'status_id'    => $inputData['status_id'] ?? null,
            'page_size'    => $inputData['page_size'] ?? 10,
            'current_page' => $inputData['current_page'] ?? 1,
            'is_delete'    => 0,
            'is_show'      => 1
        ];

        $data = $this->bookService->getBook($reqParam);
        
        return $this->success($data);
    }
    
    /**
     * getCommend
     * 漫画推荐列表
     * User：Eric
     * Date：2020/10/13
     * Time：上午11:34
     * @return \Psr\Http\Message\ResponseInterface
     * 
     * @GetMapping(path="getCommend")
     */
    public function getCommend()
    {
        $inputData = $this->request->all();
        
        $reqParam = [
            'size'         => $inputData['size'] ?? 6,
            'page_size'    => $inputData['page_size'] ?? 3,
            'current_page' => $inputData['current_page'] ?? 1,
        ];

        $data = $this->bookService->getCommend($reqParam);
        
        return $this->success($data);
    }
    
    /**
     * getIntro
     * 搜索推荐
     * User：Eric
     * Date：2020/10/13
     * Time：上午11:17
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @GetMapping(path="getIntro")
     */
    public function getIntro()
    {
        $data = $this->bookService->getIntro($reqParam);

        return $this->success($data);
    }

    /**
     * getGuess
     * 猜你喜欢
     * User：Eric
     * Date：2020/10/13
     * Time：上午9:34
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @GetMapping(path="getGuessList")
     */
    public function getGuessList()
    {
        $inputData = $this->request->all();
        
        $reqParam = [
            'page_size'    => $inputData['page_size'] ?? 6,
            'current_page' => $inputData['current_page'] ?? 1,
        ];

        $data = $this->bookService->getGuessList($reqParam);

        return $this->success($data);
    }
    
    /**
     * getRandomList
     * 随机获取漫画
     * User：Eric
     * Date：2020/12/28
     * Time：下午14:39
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @GetMapping(path="getRandomList")
     */
    public function getRandomList()
    {
        $inputData = $this->request->all();
        
        $page_size  = $inputData['page_size'] ?? 6;

        $data = $this->bookService->getRandomList($page_size);

        return $this->success($data);
    }
    
    /**
     * getDownloadList
     * 获取漫画下载数据
     * User：Eric
     * Date：2021/5/2
     * Time：上午11:54
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @GetMapping(path="getDownloadList")
     */
    public function getDownloadList()
    {
        $inputData = $this->request->all();
        $validator = $this->validation->make(
            $inputData,
            [
                'book_id'     => 'required|integer',
                'chapter_num' => 'required|array',
            ]
        );

        if ($validator->fails()){
            $errorMessage = $validator->errors()->first();
            throw new BusinessException(StatusCode::ERR_EXCEPTION,$errorMessage);
        }
        
        $reqParam = [
            'book_id'     => $inputData['book_id'],
            'chapter_num' => $inputData['chapter_num'],
        ];
        
        $data = $this->bookService->getDownloadList($reqParam);

        return $this->success($data);
    }
}