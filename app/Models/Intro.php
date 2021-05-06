<?php

declare (strict_types=1);
namespace App\Models;



use Hyperf\DbConnection\Db;

/**
 * @property int $id
 * @property int $book_id
 * @property int $is_enable
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Intro extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'intro';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'book_id', 'is_enable', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'book_id' => 'integer' , 'is_enable' => 'integer' , 'created_at' => 'datetime', 'updated_at' => 'datetime'];


}