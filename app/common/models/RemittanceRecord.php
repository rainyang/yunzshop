<?php
/**
 * Created by PhpStorm.
 * User: shenyang
 * Date: 2018/6/8
 * Time: 下午4:15
 */

namespace app\common\models;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class TransferRecord
 * @package app\common\models
 * @property string report_url
 * @property int process_id
 * @property Collection process
 */
class RemittanceRecord extends BaseModel
{
    public $table = 'yz_remittance_record';
    protected $guarded = ['id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function process(){
        return $this->belongsTo(Process::class);
    }
}