<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * ReadLogController.php
 *
 * User：Eric
 * Date：2020/5/19
 * Time：上午11:32
 */


namespace App\Controller\Manga;

use App\Constants\StatusCode;
use App\Controller\BaseController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Exception\BusinessException;
use App\Middleware\LoginAuthMiddleware;
use App\Middleware\CommonAuthMiddleware;

/**
 * MarkController
 * 阅读标记管理
 * @package App\Controller\Manga
 * User：Eric
 * Date：2020/10/13
 * Time：上午11:32
 *
 * @Controller(prefix="app_api/v1/mark")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(CommonAuthMiddleware::class)
 * })
 * @property \Core\Services\MarkService $markService
 */
class MarkController extends BaseController
{
    /**
     * getList
     * 阅读标记
     * User：Eric
     * Date：2020/5/19
     * Time：上午11:34
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @GetMapping(path="getDownload")
     */
    public function getDownload()
    {
        $inputData = $this->request->all();
        $validator = $this->validation->make(
            $inputData,
            [
                'ids'=> 'required|array',
            ]
        );

        if ($validator->fails()){
            $errorMessage = $validator->errors()->first();
            throw new BusinessException(StatusCode::ERR_EXCEPTION,$errorMessage);
        }
      
        $reqParam = [
            'ids'          => $inputData['ids'],
            'page_size'    => $inputData['page_size'] ?? 9,
            'current_page' => $inputData['current_page'] ?? 1,
            'is_delete'    => 0,    
        ];
        
        $data = $this->markService->getDownload($reqParam);

        return $this->success($data);
    }
    
    /**
     * getMark
     * 阅读标记
     * User：Eric
     * Date：2020/5/19
     * Time：上午11:34
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @GetMapping(path="getMark")
     */
    public function getMark()
    {
        $inputData = $this->request->all();
        
        $reqParam = [
            'page_size'    => $inputData['page_size'] ?? 9,
            'current_page' => $inputData['current_page'] ?? 1,
            'is_delete'    => 0,    
        ];
        
        $data = $this->markService->getMark($reqParam);

        return $this->success($data);
    }
    
    /**
     * saveMark
     * 存入标记
     * User：Eric
     * Date：2020/12/9
     * Time：上午11:21
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="saveMark")
     */
    public function saveMark()
    {
        $inputData = $this->request->all();
        $validator = $this->validation->make(
            $inputData,
            [
                'book_id' => 'required|integer|gt:0',
                'chapter_id' => 'required|integer|gt:0',
                'chapter_num' => 'required|integer|gt:0',
                'roast'  => 'required|integer|gt:0',
            ],
            [
                'book_id.required'     => 'book_id不能为空',
                'chapter_id.required'  => 'chapter_id不能为空',
                'chapter_num.required' => 'chapter_num不能为空',
                'roast.required'       => 'roast不能为空',
            ]
        );
        if ($validator->fails()){
            $errorMessage = $validator->errors()->first();
            throw new BusinessException(StatusCode::ERR_EXCEPTION,$errorMessage);
        }
        
        $reqParam = [
            'book_id'     =>  $inputData['book_id'],
            'chapter_id'  =>  $inputData['chapter_id'],
            'chapter_num' =>  $inputData['chapter_num'],
            'roast'       =>  $inputData['roast'],
        ];
        
        $result = $this->markService->saveMark($reqParam);

        return $this->success($result);
    }
    
    /**
     * delUserCollection
     * 删除收藏
     * User：Eric
     * Date：2020/10/13
     * Time：下午5:41
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="delUserMark")
     */
    public function delUserMark()
    {
        $inputData = $this->request->all();
        $validator = $this->validation->make(
            $inputData,
            [
                'book_id' => 'required',
            ],
            [
                'book_id.required' => 'book_id不能为空',
            ]
        );
        
        if ($validator->fails()){
            $errorMessage = $validator->errors()->first();
            throw new BusinessException(StatusCode::ERR_EXCEPTION,$errorMessage);
        }
        
        $id = $this->markService->deleteMark($inputData['book_id']);
        
        if($id > 0){
             return $this->success($id,'删除成功!');
        }
        return $this->error(StatusCode::ERR_EXCEPTION,'删除失败!');
    }
}