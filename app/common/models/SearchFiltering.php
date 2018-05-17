<?php

namespace app\common\models;

use Illuminate\Database\Eloquent\SoftDeletes;


/**
* 
*/
class SearchFiltering extends \app\common\models\BaseModel
{
    use SoftDeletes;

    public $table = 'yz_search_filtering';

    protected $guarded = [];

    protected $hidden = [
        'deleted_at',
    ];


    public function scopeGetFilterGroup($query,$parent_id = 0)
    {
    	return $query->where('parent_id', $parent_id)->where('is_show', 0);
    }

    public function scopeCategoryLabel($query, $ids = [])
    {

        if ($ids && is_array($ids)) {
            return $query->whereIn('id', $ids);
        }

        return $query;

    }
}