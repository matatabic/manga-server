<?php

declare (strict_types=1);
namespace App\Models;




/**
 * @property int $id
 * @property string $title
 * @property int $is_enable
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Status extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'status';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'title', 'color', 'sort', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'sort' => 'integer' , 'is_enable' => 'integer' , 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    /**
     * getList
     * 获取列表
     * User：Eric
     * Date：2020/10/19
     * Time：上午10:20
     * @param array $where 查询条件
     * @param array $order 排序条件
     * @param int $offset 偏移
     * @param int $limit 条数
     * @return array
     */
    public function getList($where = [], $order = [], $offset = 0, $limit = 0)
    {
        $query = $this->query()->select($this->table . '.id', $this->table . '.title', $this->table . '.created_at');
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



}