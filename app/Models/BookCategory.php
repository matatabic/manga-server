<?php

declare (strict_types=1);
namespace App\Models;

use Hyperf\DbConnection\Db;

/**
 * @property int $id 
 * @property string $book_id 
 * @property string $category_id
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class BookCategory extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'book_category';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'book_id', 'category_id', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'book_id' => 'integer', 'category_id' => 'integer',  'created_at' => 'datetime', 'updated_at' => 'datetime'];
    
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
        $query = $this;
        foreach ($where as $k => $v) {
            if ($v || $v != null) {
                if ($k === 'title') {
                    $query = $query->where($this->table . '.' . $k , 'like' ,'%'. $v .'%');
                    continue;
                }
                if ($k === 'category_id') {
                    $query = $query->leftjoin('book_category', 'book_id', '=', $this->table . '.id');
                    $query = $query->where(fn($queryS) => $queryS->whereIn('book_category.category_id', $v));
                    continue;
                }
                $query = $query->where($this->table . '.' . $k, $v);
            }
        }
        $query = $query->count();
        return $query > 0 ? $query : 0;
    }
    
    /**
     * deleteInfo
     * 重写父类的该方法，用于条件查询计算总数
     * User：Eric
     * Date：2021/3/4
     * Time：下午11:36
     * @param array $where
     * @return int
     */
    public function deleteInfo($book_id)
    {
        return Db::table('book_category')->where('book_id', $book_id)->delete();
    }
}