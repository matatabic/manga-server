<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * BriefController.php
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
 * BriefController
 * 漫画简介
 * @package App\Controller\Manga
 * User：Eric
 * Date：2020/5/19
 * Time：上午11:32
 *
 * @Controller(prefix="app_api/v1/brief")
 *
 * @property \Core\Services\BriefService $briefService
 */
class BriefController extends BaseController
{
    /**
     * getList
     * 漫画简介列表
     * User：Eric
     * Date：2020/5/19
     * Time：上午11:34
     * @return \Psr\Http\Message\ResponseInterface
     * 
     * @GetMapping(path="getList")
     */
    public function getList()
    {
        $reqParam = [];
        $inputData = $this->request->all();
        $validator = $this->validation->make(
            $inputData,
            [
                'book_id' => 'required|integer|gt:0',
            ],
            [
                'book_id.required' => 'book_id不能为空',
            ]
        );
        if ($validator->fails()){
            $errorMessage = $validator->errors()->first();
            throw new BusinessException(StatusCode::ERR_EXCEPTION,$errorMessage);
        }
        
        $data = $this->briefService->getBrief($inputData['book_id']);
        
        return $this->success($data);
    }


}