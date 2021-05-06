<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * ChapterController.php
 *
 * User：Eric
 * Date：2020/10/13
 * Time：下午4:57
 */


namespace App\Controller\Manga;


use App\Constants\StatusCode;
use App\Controller\BaseController;
use App\Exception\BusinessException;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

/**
 * ChapterController
 * 漫画章节管理
 * @package App\Controller\Manga
 * User：Eric
 * Date：2020/5/21
 * Time：上午10:54
 *
 * @Controller(prefix="app_api/v1/chapter")
 *
 *
 * @property \Core\Services\ChapterService $chapterService
 */
class ChapterController extends BaseController
{
    /**
     * getList
     * 漫画章节列表
     * User：Eric
     * Date：2020/10/13
     * Time：下午4:57
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
        
        $reqParam = [
            'book_id'  => $inputData['book_id']
        ];

        $data = $this->chapterService->getChapterList($reqParam);
        
        return $this->success($data);
    }




}