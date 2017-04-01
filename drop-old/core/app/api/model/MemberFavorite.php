<?php
namespace app\api\model;

class MemberFavorite extends BaseModel
{
    protected $tableName = 'sz_yi_member_favorite';

    public function getList($uniacid){
        $where['uniacid'] = $uniacid;
        $set_data = $this->where($where)->find();
        $set     = unserialize($set_data['sets']);
        $app_set_data = $set['app']['base']['accept'];
        return (bool)$app_set_data;
    }
    
}