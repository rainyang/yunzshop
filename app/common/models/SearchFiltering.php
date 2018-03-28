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

    public $guarded = [];

    
}