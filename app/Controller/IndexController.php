<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Controller;

use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\DbConnection\Db;
use Hyperf\Utils\Parallel;

/**
 * IndexController
 * 类的介j
 * @package App\Controller
 * User：YM
 * Date：2019/11/12
 * Time：下午5:03
 *
 *
 *
 * @AutoController()
 * @property \Core\Services\CategoryService $categoryService
 * @property \Core\Services\EpisodeService $episodeService
 * @property \App\Models\Chapter $chapterModel
 */


class IndexController extends BaseController
{
    /**
     * index
     * 函数的含义说明
     * User：YM
     * Date：2019/11/13
     * Time：上午9:58
     * @return array
     */
    public function index()
    {
        // $user = $this->request->input('id');
        // $sql = 'select id from manga_chapter where book_id='.$user;
        // $result =  Db::select($sql);
        // $result = json_decode(json_encode($result),true);
        // $arr = [];
        // $leng = count($result);
        // foreach ($result as $k => &$v){
        //     $arr[$k]['id'] = $v['id'];
        //     $arr[$k]['sort'] = $leng-$k;
        // }
        
        // $this->chapterService->updateData($arr);
        $user = $this->request->input('aa', 'Hyperf2');
        $method = $this->request->getMethod();
//        throw new \App\Exception\BusinessException(StatusCode::ERR_EXCEPTION,'11');
//        $tmp = $this->testRepo->test();
        //return $this->success([$tmp]);

        $data = [
//            'method1' => $method,
//            'message' => $tmp,
//            'aaaa' => getCookie('aaa'),
//            'QQ' => $this->request->getHeaders(),
//            'DDD' => $this->request->getServerParams(),
//            'bb' => $this->request->getCookieParams(),
//            'adb' => getClientInfo(),
            '888' => array_values(swoole_get_local_ip()),
//            '999' => $this->request->getBody(),
            '090' => getSessionId(),
            '999' => getAllSession(),
//            '98989' => getLogArguments(),
            '0909' => $this->request->getHeaders(),
            '98989' => $this->request->getServerParams(),
            '88888' => $this->request->fullUrl(),
            '1288' => getLogArguments(),
//            '2222' => $text,
//            '3333' => getHostByName(getHostName()),
        ];

        return $this->success($data);
    }
    
//     public function findNum($str=''){
//         $str=trim($str);
//         if(empty($str)){return '';}
//         $result='';
//         for($i=0;$i<strlen($str);$i++){
//             if(is_numeric($str[$i])){
//                 $result.=$str[$i];
//             }
//         }
//         return $result;
//     }
    
    // public function index()
    // {
    //     for($a=1098;$a<1099;$a++){
    //         $sql = 'select * from manga_episode where book_id=? order by roast asc';
    //         $result =  Db::select($sql,[$a]);
    //         $result = json_decode(json_encode($result),true);
    //         var_dump($result);
    //         $arr =[];
    //         $leng = count($result);
        
    //         $n = 1;
    //         $start =$result[0]['chapter_num'];
    //         foreach ($result as $key => &$v){
    //             if($v['chapter_num']==$start){
    //                 $arr[$key]['id'] = $v['id'];
    //                 $arr[$key]['number'] = $n;
    //                 $n++;
    //             }else{
    //                 $parallel = new Parallel(500);
    //                 $this->episodeService->updateData($arr);
    //                 $parallel->add(function () use ($arr) {
    //                     $this->episodeService->updateData($arr);
    //                 });
    //                 $n = 1;
    //                 $start = $result[$key]['chapter_num'];
    //                 unset($arr);
    //                 $arr[$key]['id'] = $v['id'];
    //                 $arr[$key]['number'] = $n;
    //                 $n++;
    //             }
    //             if($key == $leng-1){
    //                 $parallel = new Parallel(500);
    //                 $this->episodeService->updateData($arr);
    //                 $parallel->add(function () use ($arr) {
    //                     $this->episodeService->updateData($arr);
    //                 });
    //                 unset($arr);
    //             }
    //         }
    //         $parallel->wait();
    //         // var_dump($arr);
    //         unset($v);
    //     }
    // }
    //47591
    // public function index(){
    //         for($a=1;$a<47592;$a++){
    //             $sql = 'select number from manga_episode where chapter_id=? order by number desc limit 1';
    //             $result =  Db::select($sql,[$a]);
    //             $result = json_decode(json_encode($result),true);
    //             $num = $result[0]['number'];
    //             $parallel = new Parallel(5000);
               
    
    //             $parallel->add(function () use ($num,$a) {
    //                 $updateSql = 'update manga_chapter set chapter_total = ? where id=?';
    //                 Db::update($updateSql, [$num,$a]);
    //             });
    
                
    //             $parallel->wait();

    //     }
    // }
    // public function index(){
           
    //         $sql = 'select id from manga_book where id in(2) ';
            
    //         $result =  Db::select($sql);
    //         $result = json_decode(json_encode($result),true);
           
    //         $parallel = new Parallel(500);
    //         foreach ($result as $k =>&$v ){
    //             $sql = 'select id from manga_chapter where book_id=? order by id desc';
    //             $a =  Db::select($sql,[$v['id']]);
    //             $a = json_decode(json_encode($a),true);
               
            
    //           foreach ($a as $key =>&$val){
    //               $id = $val['id'];
    //               $num = $key +1;
                    
    //                 $parallel->add(function () use ($num,$id) {
    //                 $updateSql = "update manga_chapter set chapter_num =$num where id = $id";
    //                 var_dump($num);
    //                 var_dump($id);
    //                 var_dump($updateSql);
    //                 Db::update($updateSql);
    //             //     $sql = DB::getQueryLog();
            
    //             //     $query = end($sql);
    //             //     var_dump($query);
                    
    //             //   $res =  $this->chapterModel->saveInfo([
    //             //         'id'     => $val['id'],
    //             //         'chapter_num' => $num
    //             //     ]);
    //             //     var_dump($res);
    //                 });
    //             }
                
                
    
                
    //             $parallel->wait();

    //         }
    // }
    //  public function index(){
            
    //         $sql = 'select id from manga_chapter';
            
    //         $result =  Db::select($sql);
    //         $result = json_decode(json_encode($result),true);
           
    //         $parallel = new Parallel(5000);
    //         foreach ($result as $k =>&$v ){
    //             $sql = 'select id from manga_episode where chapter_id=? order by roast asc';
    //             $a =  Db::select($sql,[$v['id']]);
    //             $a = json_decode(json_encode($a),true);
               
              
    //           foreach ($a as $key =>&$val){
    //               $id = $val['id'];
    //               $num = $key +1;
    //                 $parallel->add(function () use ($num,$id) {
    //                 $updateSql = 'update manga_episode set number = ? where id=?';
                 
    //                 Db::update($updateSql, [$num,$id]);
    //                 });
    //             }
                
                
    
                
    //             $parallel->wait();

    //         }
    // }
}
