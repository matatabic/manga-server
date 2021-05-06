<?php

declare (strict_types=1);
namespace App\Models;

use Hyperf\DbConnection\Db;

/**
 * @property string $id
 * @property string $title
 * @property string $cover
 * @property int $sort
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Book extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'book';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'title', 'image' , 'description', 'status_id' , 'author' , 'sort', 'is_show', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'sort' => 'integer' , 'chapter_total' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    /**
     * getCount
     * 重写父类的该方法，用于条件查询计算总数
     * User：Eric
     * Date：2020/10/13
     * Time：下午11:42
     * @param array $where
     * @return int
     */
    public function getCount($where = [])
    {   
        $where_sql = ' WHERE ';
        $category_sql = '';
        
        foreach ($where as $k => $v) {
            if ($v != null || $v === 0) {
                if ($k === 'title') {
                    $where_sql = $where_sql."manga_book.$k like '%$v%' AND ";
                    continue;
                }
                if ($k === 'category_ids') {
                    if($v[0] > 0){
                        $category_sql .= "RIGHT JOIN (
                            	SELECT
                            		GROUP_CONCAT( category_id ) ids,
                            		book_id 
                            	FROM
                            		manga_book_category 
                            	GROUP BY
                            		book_id 
                            	HAVING";
                        foreach ($v as $key => $val){
                            $category_sql .= "
                            		Find_IN_SET( $val, ids ) AND ";
                        }
                        $category_sql = rtrim($category_sql,"AND ");
                        $category_sql .= ") category_ids ON category_ids.book_id = manga_book.id";
                    }
                    continue;
                }
                $where_sql = $where_sql."manga_book.$k=$v AND ";
            }
        }
        
        $where_sql = rtrim($where_sql,"AND ");
        $where_sql = $category_sql.$where_sql;
        $sql = "SELECT
                	count(*) count
                FROM
                	manga_book
                    $where_sql
                ";
                
        $query = Db::select($sql);
        $query = json_decode(json_encode($query), true);
        $query = current($query)['count'];
        
        return $query > 0 ? $query : 0;
    }
   
    /**
     * getList
     * 获取漫画列表
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
        $where_sql = ' WHERE ';
        $category_sql = '';
       
        foreach ($where as $k => $v) {
            if ($v != null || $v === 0) {
                if ($k === 'title') {
                    $where_sql = $where_sql."manga_book.$k like '%$v%' AND ";
                    continue;
                }
                if ($k === 'category_ids') {
                    if($v[0] > 0){
                        $category_sql .= "RIGHT JOIN (
                            	SELECT
                            		GROUP_CONCAT( category_id ) ids,
                            		book_id 
                            	FROM
                            		manga_book_category 
                            	GROUP BY
                            		book_id 
                            	HAVING";
                        foreach ($v as $key => $val){
                            $category_sql .= "
                            		Find_IN_SET( $val, ids ) AND ";
                        }
                        $category_sql = rtrim($category_sql,"AND ");
                        $category_sql .= ") category_ids ON category_ids.book_id = manga_book.id";
                    }
                    continue;
                }
                $where_sql = $where_sql."manga_book.$k=$v AND ";
            }
                
        }
        $where_sql = rtrim($where_sql,"AND ");       
        $where_sql = $category_sql.$where_sql;
        $sql = "SELECT
                	manga_book.id,
                	manga_book.title,
                	manga_book.image,
                	manga_book.author,
                	manga_book.is_show,
                	date_format( manga_book.updated_at,'%Y-%m-%d') updated_at,
                	manga_status.title status,
                	manga_status.color statusColor,
                	manga_book.description,
                	manga_book.chapter_total,
                	GROUP_CONCAT( manga_category.display_name ORDER BY manga_category.id SEPARATOR ' ' ) category
                FROM
                	manga_book
                	LEFT JOIN manga_book_category cate1 ON cate1.book_id = `manga_book`.`id`
                	LEFT JOIN `manga_category` ON `manga_category`.`id` = cate1.category_id
                	LEFT JOIN `manga_status` ON `manga_status`.`id` = `manga_book`.`status_id` 
                    $where_sql
                    GROUP BY
                	`manga_book`.`id` 
                ORDER BY
                	`manga_book`.`sort` DESC,
                	`manga_book`.`id` DESC 
                	LIMIT ? OFFSET ?
                ";
                
        $query = Db::select($sql,[$limit,$offset]);
        
        return $query ? json_decode(json_encode($query), true) : [];
        
    }
    /**
     * getCommend
     * 获取漫画推荐列表
     * User：Eric
     * Date：2020/10/13
     * Time：下午9:49
     * @param array $where
     * @param array $order
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getCommend($where = [], $order = [], $offset = 0, $limit = 0)
    {
        $sql = "select t1.id,t5.display_name classify,t1.title,t1.image,
                    GROUP_CONCAT(t4.display_name ORDER BY t4.id SEPARATOR ' ') category 
                    from manga_book t1 
                RIGHT JOIN (SELECT * FROM
                            	manga_category_commend a
                            WHERE
                            	(SELECT	count(id) FROM
                            			manga_category_commend b
                            		WHERE b.commend_id = a.commend_id AND b.id > a.id) < ?
                            ORDER BY commend_id asc) t2  on t1.id = t2.book_id 
                LEFT JOIN manga_book_category t3 ON t3.book_id = t2.id
                LEFT JOIN manga_category t4 ON t4.id = t3.category_id
                LEFT JOIN manga_commend t5 ON t5.id = t2.commend_id
                LEFT JOIN manga_status t7 ON t7.id = t1.status_id
                RIGHT JOIN (
                    SELECT id,sort from manga_commend ORDER BY sort DESC LIMIT ? OFFSET ? ) t6
                        ON t6.id=t2.commend_id
                GROUP BY t1.id 
                ORDER BY t6.sort DESC,t2.sort DESC";
                
        $query =  Db::select($sql,[$where['size'],$limit,$offset]);
        
        return $query ? json_decode(json_encode($query), true) : [];
    }
    
    /**
     * getIntro
     * 获取漫画推荐列表
     * User：Eric
     * Date：2020/10/13
     * Time：下午10:58
     * @param $category_id
     * @param $limit
     * @return array
     */
    public function getIntro($where = [])
    {
        $sql = "select t1.id,t1.title,t2.index from manga_book t1 left join 
                    manga_intro t2 on t2.book_id = t1.id 
                    where t2.is_show=1";
                
        $query =  Db::select($sql);
        
        return $query ? json_decode(json_encode($query), true) : [];
    }
    /**
     * getRand
     * 随机获取漫画
     * User：Eric
     * Date：2020/10/13
     * Time：下午9:49
     * @param $limit
     * @return array
     */
    public function getRand($limit)
    {
        $query = Db::table('book') 
        ->select(Db::raw("manga_book.id,manga_book.title,manga_book.image,manga_book.author,manga_status.color statusColor,manga_status.title status,GROUP_CONCAT(manga_category.display_name ORDER BY manga_category.id SEPARATOR ' ') category"))
        ->leftjoin('book_category', 'book_category.book_id', '=', 'book.id')
        ->leftjoin('category', 'category.id', '=', 'book_category.category_id')
        ->leftjoin('status', 'status.id', '=', 'book.status_id')
        ->groupBy('book.id') 
        ->limit($limit)
        ->orderByRaw('rand()')
        ->get();

        return $query ? $query->toArray() : [];
    }
}