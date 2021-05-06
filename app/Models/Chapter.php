<?php

declare (strict_types=1);
namespace App\Models;

use Hyperf\DbConnection\Db;
use Hyperf\Database\Schema\Schema;
/**
 * @property int $id
 * @property string $title
 * @property int $book_id
 * @property int episode_total
 * @property int $chapter_num
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Chapter extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'chapter';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'title', 'book_id', 'chapter_num', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'book_id' => 'integer', 'chapter_num' => 'integer', 'updated_at' => 'datetime'];
    /**
     * getList
     * 章节管理列表
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
        $query = $this->query()->select($this->table . '.id', $this->table . '.title', $this->table .'.episode_total', $this->table . '.chapter_num');
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
     * getChapter
     * 获取下一个id
     * User：Eric
     * Date：2020/10/17
     * Time：下午19:38
     * @param int $book_id
     * @return int
     */
    public function getChapter($book_id)
    {
        $sql = 'select manga_chapter.id,manga_chapter.title,manga_chapter.chapter_num,min(manga_episode.roast) roast,date_format(manga_chapter.created_at,"%Y-%m-%d") as created_at
                    from manga_chapter left join manga_episode on manga_episode.chapter_id = manga_chapter.id  
                where manga_chapter.book_id=? 
                group by manga_episode.chapter_id
                order by manga_chapter.chapter_num desc';
             
        $result =  Db::select($sql,[$book_id]);

        return $result ? json_decode(json_encode($result),true) : [];
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