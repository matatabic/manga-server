<?php

declare (strict_types=1);
namespace App\Models;



use Hyperf\DbConnection\Db;

/**
 * @property int $id
 * @property int $user_id
 * @property int $book_id
 * @property int $roast
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Mark extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mark';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_id', 'book_id', 'chapter_id', 'chapter_num', 'roast', 'is_delete', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer' , 'book_id' => 'integer' , 'chapter_id' => 'integer' , 'chapter_num' => 'integer' , 'roast' => 'integer' , 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    
    /**
     * getCount
     * 重写父类的该方法，用于条件查询计算总数
     * User：Eric
     * Date：2020/11/26
     * Time：上午11:42
     * @param array $where
     * @return int
     */
    public function getCount($where = [])
    {
        $sql = "SELECT count(distinct book_id) as total FROM manga_mark 
            	where manga_mark.user_id=? and is_delete=?";
      
        $query =  Db::select($sql,[$where['user_id'],$where['is_delete']]);
        
        return $query ? json_decode(json_encode($query), true)[0]['total'] : 0;
    }
    
    /**
     * getList
     * 获取历史列表
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
        $query = $this->query()->select( 'book.id', 'book.title' , 'book.image',$this->table .'.chapter_id',$this->table .'.chapter_num',$this->table .'.roast',$this->table .'.created_at');
        // 循环增加查询条件
        $query = $query->leftjoin('book', 'book.id', '=', $this->table . '.book_id');
         foreach ($where as $k => $v) {
            if ($v != null || $v === 0) {
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
     * getMark
     * 获取历史
     * User：Eric
     * Date：2020/11/26
     * Time：下午9:49
     * @param array $where
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getMark($where = [], $offset = 0, $limit = 0)
    {
        $sql = "SELECT
                    mark.id,
            		mark.book_id,
            		manga_book.title,
            		manga_book.image,
            		manga_book.author,
            		manga_book.chapter_total,
            		mark.chapter_id,
            		mark.chapter_num,
            		mark.roast,
            		mark.created_at
            	FROM
            		manga_book
            		RIGHT JOIN (SELECT * FROM (SELECT * from manga_mark WHERE user_id = ? AND is_delete = ? order by id desc) temp ";
            		
        if($where['ids']){
            $sql = $sql . " WHERE book_id in (".$where['ids'].")";
        }
        
        $sql = $sql. " GROUP BY book_id ) mark 
            		ON manga_book.id = mark.book_id 
            	order by mark.created_at desc LIMIT ?,?";
        
        $query =  Db::select($sql,[$where['user_id'],$where['is_delete'],$offset,$limit]);

        return $query ? json_decode(json_encode($query), true) : [];
    }
    
    /**
     * getMark
     * 获取历史
     * User：Eric
     * Date：2020/11/26
     * Time：下午9:49
     * @param array $where
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getDownload($where = [], $offset = 0, $limit = 0)
    {
        $sql = "SELECT
                    manga_book.id as book_id,
            		manga_book.title,
            		manga_book.image,
            		manga_book.author,
            		manga_book.chapter_total,
            		mark.chapter_id,
            		mark.chapter_num,
            		mark.roast,
            		mark.created_at
            	FROM
            		manga_book
            		left JOIN (SELECT * FROM (SELECT * from manga_mark WHERE user_id = ? AND is_delete = ? order by id desc) temp GROUP BY book_id ) mark 
            		ON manga_book.id = mark.book_id 
				where manga_book.id in (".$where['ids'].") order by created_at desc,book_id desc LIMIT ?,?";
            		

        $query =  Db::select($sql,[$where['user_id'],$where['is_delete'],$offset,$limit]);

        return $query ? json_decode(json_encode($query), true) : [];
    }
    
    /**
     * deleteMark
     * 获取收藏
     * User：Eric
     * Date：2020/12/3
     * Time：下午9:49
     * @param int $user_id
     */
    public function deleteMark($user_id,$book_id)
    {
    
        $query = $this->query()->where(['user_id'=>$user_id,['is_delete','<>',1]])->whereIn('book_id',$book_id)->update(['is_delete'=>1]);

        return $query ? $query : 0;
    }
}