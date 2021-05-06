<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * CollectController.php
 *
 * User：Eric
 * Date：2020/10/13
 * Time：下午5:37
 */


namespace App\Controller\Manga;


use App\Constants\StatusCode;
use App\Controller\BaseController;
use App\Exception\BusinessException;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Middleware\LoginAuthMiddleware;
use App\Middleware\CommonAuthMiddleware;

/**
 * CollectionController
 * 收藏控制器
 * @package App\Controller\Manga
 * User：Eric
 * Date：2020/10/13
 * Time：下午5:37
 *
 * @Controller(prefix="app_api/v1/collection")
 *
 * @Middlewares({
 *     @Middleware(LoginAuthMiddleware::class),
 *     @Middleware(CommonAuthMiddleware::class)
 * })
 *
 * @property \Core\Services\CollectionService $collectionService
 */
class CollectionController extends BaseController
{
    /**
     * getCollectList
     * 收藏列表
     * User：Eric
     * Date：2020/10/13
     * Time：下午5:41
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @GetMapping(path="getList")
     */
    public function getCollection()
    {
        $inputData = $this->request->all();
        
        $reqParam = [
            'page_size'    => $inputData['page_size'] ?? 9,
            'current_page' => $inputData['current_page'] ?? 1,
        ];
 
        $data = $this->collectionService->getCollection($reqParam);

        return $this->success($data);
    }

    /**
     * addUserCollection
     * 保存收藏
     * User：Eric
     * Date：2020/10/13
     * Time：下午5:41
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="addUserCollection")
     */
    public function addUserCollection()
    {
        $inputData = $this->request->all();
        $validator = $this->validation->make(
            $inputData,
            [
                'book_id' => 'required|integer|gt:0',
                'sort'    => 'integer|gt:0'
            ],
            [
                'book_id.required' => 'book_id不能为空',
            ]
        );
        if ($validator->fails()){
            $errorMessage = $validator->errors()->first();
            throw new BusinessException(StatusCode::ERR_EXCEPTION,$errorMessage);
        }
        
        $reqParam = [
            'book_id' => $inputData['book_id'],
            'sort'    => $inputData['sort'] ?? 0
        ];    

        $id = $this->collectionService->addUserCollection($reqParam);

        if($id > 0){
             return $this->success($id,'收藏成功!');
        }
        return $this->error(StatusCode::ERR_EXCEPTION,'收藏失败!');
    }

    /**
     * delUserCollection
     * 删除收藏
     * User：Eric
     * Date：2020/10/13
     * Time：下午5:41
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @PostMapping(path="delUserCollection")
     */
    public function delUserCollection()
    {
        $inputData = $this->request->all();
        $validator = $this->validation->make(
            $inputData,
            [
                'id' => 'required',
            ],
            [
                'id.required' => 'id不能为空',
            ]
        );
        
        if ($validator->fails()){
            $errorMessage = $validator->errors()->first();
            throw new BusinessException(StatusCode::ERR_EXCEPTION,$errorMessage);
        }
        
        $id = $this->collectionService->deleteCollection($inputData['id']);
        
        if($id > 0){
             return $this->success($id,'取消收藏成功!');
        }
        return $this->error(StatusCode::ERR_EXCEPTION,'取消失败!');
    }

}