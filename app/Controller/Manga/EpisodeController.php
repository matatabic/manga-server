<?php

declare(strict_types=1);
/**
 * Created by PhpStorm.
 *​
 * EpisodeController.php
 *
 * User：Eric
 * Date：2020/10/13
 * Time：上午10:54
 */


namespace App\Controller\Manga;


use App\Constants\StatusCode;
use App\Controller\BaseController;
use App\Exception\BusinessException;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
/**
 * EpisodeController
 * 漫画画册管理
 * @package App\Controller\Manga
 * User：Eric
 * Date：2020/10/13
 * Time：上午10:54
 *
 * @Controller(prefix="app_api/v1/episode")
 * 
 *
 * @property \Core\Services\EpisodeService $episodeService
 */
class EpisodeController extends BaseController
{
    /**
     * getEpisodeList
     * 漫画章节列表
     * User：Eric
     * Date：2020/2/11
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
                'book_id'    => 'required|integer|gt:0',
                'chapter_num' => 'required|integer|gt:0',
                'roast'      => 'integer',
            ],
            [
                'book_id.required'     => 'book_id不能为空',
                'chapter_num.required' => 'chapter_num不能为空',
            ]
        );
        if ($validator->fails()){
            $errorMessage = $validator->errors()->first();
            throw new BusinessException(StatusCode::ERR_EXCEPTION,$errorMessage);
        }
        
        $reqParam = [
            'book_id'      =>  $inputData['book_id'],
            'chapter_num'  =>  $inputData['chapter_num'],
            'roast'        =>  $inputData['roast'] ?? null,
        ];
        
        $mark = $inputData['mark'] ?? 1;
        
        $data = $this->episodeService->getEpisode($reqParam, $mark);
        
        return $this->success($data);
    }




}