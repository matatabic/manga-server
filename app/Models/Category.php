<?php

declare (strict_types=1);
namespace App\Models;

use Hyperf\DbConnection\Db;
/**
 * @property int $id 
 * @property string $display_name 
 * @property string $name 
 * @property int $parent_id 
 * @property string $url 
 * @property int $sort
 * @property string $image 
 * @property string $additional 
 * @property string $description 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class Category extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'category';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'display_name', 'name', 'parent_id', 'url', 'order', 'image', 'additional', 'description', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['parent_id' => 'integer', 'order' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    /**
     * getList
     * 分类管理列表
     * User：YM
     * Date：2020/2/9
     * Time：下午9:49
     * @param array $where
     * @param array $order
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0)
    {
        $query = $this->query()->select($this->table . '.id', $this->table . '.name', $this->table . '.image', $this->table . '.url', $this->table . '.display_name', $this->table . '.parent_id', $this->table . '.order', $this->table . '.description', $this->table . '.created_at');
        // 循环增加查询条件
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
    
    public function getCategoryById($Id){
        $sql = "SELECT
            	manga_category.id,
            	manga_category.parent_id 
            FROM
            	manga_category
            	LEFT JOIN manga_book_category ON manga_category.id = manga_book_category.category_id 
            WHERE
            	manga_book_category.book_id = ?";
        $result=  Db::select($sql,[$Id]);
        
        return array_map('get_object_vars', $result);
    }
    
    public function getTreeList($Identify){
        $sql = "SELECT
                	table3.id,
                	table3.display_name name,
                	table2.display_name typeName
                FROM
                	manga_category AS table1
                	LEFT JOIN manga_category AS table2 ON table1.id = table2.parent_id 
                	LEFT JOIN manga_category AS table3 ON table2.id = table3.parent_id 
                WHERE
                	table1.NAME = ?";
        $result=  Db::select($sql,[$Identify]);
        
        return array_map('get_object_vars', $result);
    }
}