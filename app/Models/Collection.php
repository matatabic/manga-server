<?php

declare (strict_types=1);
namespace App\Models;

use Hyperf\DbConnection\Db;

/**
 * @property int $id
 * @property int $user_id
 * @property int $book_id
 * @property int $sort
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Collection extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'collection';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_id', 'book_id', 'sort', 'is_delete', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'user_id' => 'integer' , 'book_id' => 'integer' , 'sort' => 'integer' , 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    
    /**
     * getList
     * 获取收藏
     * User：Eric
     * Date：2020/10/13
     * Time：下午9:49
     * @param array $where
     * @param array $order
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0)
    {
        $query = $this->query()->select('collection.id', 'book.title' ,'book.image');
        // 循环增加查询条件
        $query = $query->leftjoin('book', 'book.id', '=', $this->table . '.book_id');
         foreach ($where as $k => $v) {
            if ($v || $v != null) {
                $query = $query->where($this->table . '.' . $k, $v);
            }
        }
        // 追加排序
        if ($order && is_array($order)) {
            foreach ($order as $k => $v) {
                $query = $query->orderBy($this->table . '.' . $k, $v);
            }
        }
        // 是否分页
        if ($limit) {
            $query = $query->offset($offset)->limit($limit);
        }
        $query = $query->get();
        return $query ? $query->toArray() : [];
    }
    
    /**
     * getCollection
     * 获取收藏
     * User：Eric
     * Date：2020/10/13
     * Time：下午9:49
     * @param array $where
     * @param array $order
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getCollection($where = [], $order = [], $offset = 0, $limit = 0)
    {
        $sql = "SELECT
                    manga_collection.id,
                	manga_collection.book_id,
                	manga_book.image,
                	manga_book.title,
                	manga_book.chapter_total,
                	IF(mark.chapter_num is null, 1, mark.chapter_num) as chapter_num
                FROM
                	manga_collection
                	LEFT JOIN manga_book ON manga_collection.book_id = manga_book.id
                	LEFT JOIN ( SELECT * FROM 
                	( SELECT * FROM manga_mark where manga_mark.user_id=? ORDER BY id DESC ) AS temp 
                	GROUP BY temp.book_id ) AS mark 
                	ON manga_collection.book_id = mark.book_id 
                where manga_collection.user_id=?
                order by manga_collection.sort desc,id desc LIMIT ?,?";
                
                
        $query =  Db::select($sql,[$where['user_id'],$where['user_id'],$offset,$limit]);
        
        return $query ? json_decode(json_encode($query), true) : [];
    }
    
    
    /**
     * deleteCollection
     * 获取收藏
     * User：Eric
     * Date：2020/10/13
     * Time：下午9:49
     * @param int $user_id
     * @param int $id
     */
    public function deleteCollection($user_id,$id)
    {
    
        $query = $this->query()->where(['user_id'=>$user_id])->whereIn('id',$id)->delete();

        return $query ? $query : 0;
    }

}