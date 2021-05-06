<?php

declare (strict_types=1);
namespace App\Models;

use Hyperf\DbConnection\Db;
use Hyperf\Database\Schema\Schema;
/**
 * @property int $id
 * @property int $chapter_id
 * @property int $chapter_num
 * @property int $roast
 * @property string $image
 * @property int $roast
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Episode extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'episode';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id' , 'chapter_id' , 'chapter_num' , 'roast' , 'image', 'roast', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'book_id' => 'integer' , 'chapter_id' => 'integer' , 'chapter_num' => 'integer' , 'roast' => 'integer' , 'number' => 'integer' , 'roast' => 'integer' , 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    
    
    /**
     * getList
     * 分类管理列表
     * User：Eric
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
        $query = $this->query()->select($this->table . '.id', $this->table . '.image', $this->table . '.chapter_id', $this->table . '.chapter_num' , 'chapter.episode_total' , $this->table . '.number', $this->table . '.roast', $this->table . '.width', $this->table . '.height', $this->table . '.roast', 'book.chapter_total');
        $query = $query->leftjoin('chapter', 'chapter.id', '=', $this->table . '.chapter_id');
        $query = $query->leftjoin('book', 'book.id', '=', $this->table . '.book_id');
        // 循环增加查询条件
        foreach ($where as $k => $v) {
            if ($k === 'chapter_num' && is_array($v)) {
                $query = $query->whereIn($this->table . '.' . $k, $v );
                continue;
            }
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
     * getCount
     * 重写父类的该方法，用于条件查询计算总数
     * User：Eric
     * Date：2020/10/19
     * Time：下午16:09
     * @param array $where
     * @return int
     */
    public function getCount($where = [])
    {
        $query = $this->query();
        foreach ($where as $k => $v) {
            if ($k === 'book_id') {
                $query = $query->where($this->table . '.book_id', $v);
            }
        }
        $query = $query->count();
        return $query > 0 ? $query : 0;
    }

    //批量更新
    public function updateBatch($multipleData = [])
    {
        try {
            $tableName = Db::getTablePrefix() . $this->getTable();
            $firstRow  = current($multipleData);
 
            $updateColumn = array_keys($firstRow);
            // 默认以id为条件更新
            $referenceColumn = isset($firstRow['id']) ? 'id' : current($updateColumn);
            unset($updateColumn[0]);
            $updateSql = "UPDATE " . $tableName . " SET ";
            $sets      = [];
            $bindings  = [];
            foreach ($updateColumn as $uColumn) {
                $setSql = "`" . $uColumn . "` = CASE ";
                foreach ($multipleData as $data) {
                    $setSql .= "WHEN `" . $referenceColumn . "` = ? THEN ? ";
                    $bindings[] = $data[$referenceColumn];
                    $bindings[] = $data[$uColumn];
                }
                $setSql .= "ELSE `" . $uColumn . "` END ";
                $sets[] = $setSql;
            }
            $updateSql .= implode(', ', $sets);
            $whereIn   = collect($multipleData)->pluck($referenceColumn)->values()->all();
            $bindings  = array_merge($bindings, $whereIn);
            $whereIn   = rtrim(str_repeat('?,', count($whereIn)), ',');
            $updateSql = rtrim($updateSql, ", ") . " WHERE `" . $referenceColumn . "` IN (" . $whereIn . ")";
            return Db::update($updateSql, $bindings);
        } catch (\Exception $e) {
            return false;
        }
    }
    
}