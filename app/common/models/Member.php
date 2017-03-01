<?php
namespace app\common\models;

use Illuminate\Database\Eloquent\Model;

/**
 * Created by PhpStorm.
 * User: jan
 * Date: 21/02/2017
 * Time: 12:58
 */
class Member extends Model
{
    public $table = 'mc_members';

    public static function getNickNnme()
    {
        return self::select('nickname')
            ->whereNotNull('nickname')
            ->inRandomOrder()
            ->first()
            ->toArray();
    }


}