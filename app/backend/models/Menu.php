<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 09/03/2017
 * Time: 10:52
 */

namespace app\backend\models;


use app\common\models\BaseModel;
use app\common\traits\TreeTrait;

class Menu extends BaseModel
{
    use TreeTrait;

    public $table = 'yz_menu';

    //设置字段默认值
    public $attributes = [
        'parent_id'=>0,
        'name'=>'',
        'item'=>'',
        'url'=>'',
        'url_params'=>'',
        'permit'=>1,
        'menu'=>1,
        'icon'=>'',
        'sort'=>0,
        'status'=>1
    ];
    //不可填充
    public $guarded = ['id'];

    /**
     * 父菜单与子菜单栏目1:n关系
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childs()
    {
        return $this->hasMany('app\backend\models\Menu','parent_id','id');
    }

    /**
     * 子菜单与父菜单1:1关系
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo('app\backend\models\Menu','parent_id','id');
    }

    /**
     * 获取待处理的原始节点数据
     *
     * 必须实现
     *
     * return \Illuminate\Support\Collection
     */
    public function getTreeAllNodes()
    {
        return self::where('status', 1)
            ->orderBy('sort', 'asc')->get();
    }

    /**
     * 获取菜单栏目
     *
     * @param $parent_id
     * @param int $child_switch
     * @return mixed
     */
    public static function getMenuAllInfo($parent_id = 0, $child_switch = 1)
    {
        $result = self::where('parent_id', $parent_id)
                   ->where('status', 1)
                   ->orderBy('sort', 'asc');

        if ($child_switch) {
            $result = $result->with(['childs'=>function ($query) {
                return $query->where('status', 1)->orderBy('sort', 'asc');
            }]);
        }

        return $result;
    }

    /**
     * 通过ID获取菜单栏目
     *
     * @param $id
     * @return mixed
     */
    public static function getMenuInfoById($id)
    {
        return self::where('id', $id)->first();
    }

    /**
     * 获取子项
     *
     * @param $id
     * @return mixed
     */
    public static function getChildCountByParentId($parentId)
    {
        return self::where('parent_id', $parentId)->count();
    }

    /**
     * 重写检测提示文字
     * @return array
     */
    public function validationMessages()
    {
        return array_merge(parent::validationMessages(),[
            "different" => " 不能选择自己为上级。"
        ]);
    }

    /**
     *  定义字段名
     * 可使
     * @return array */
    public function atributeNames() {
        return [
            'item'=> '标识',
            'name'=> '菜单',
            'url'=> 'URL',
            'url_params'=> 'URL参数',
            'icon'=> '图标',
            'sort'=> '排序',
            'permit'=> '权限控制',
            'menu'=> '菜单显示',
            'status'=> '状态',
        ];
    }

    /**
     * 字段规则
     * @return array */
    public function rules() {

        $rule =  [
            'item' => 'required|unique:'.$this->table,
            'name' => 'required|max:45',
            'url' => 'max:255',
            'url_params' => 'max:255',
            'icon' => 'max:45',
            'sort' => 'required|integer',
            'permit' => 'required|digits_between:0,1',
            'menu' => 'required|digits_between:0,1',
            'status' => 'required|digits_between:0,1'
        ];
        //修改时不能选择自己做为上级
        if((int)$this->getAttributeValue('id') > 0){
            $rule = array_merge(['parent_id' => 'different:id'], $rule);
        }

        return $rule;
    }
}