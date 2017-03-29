<?php
namespace app\backend\modules\goods\models;
/**
 * Created by PhpStorm.
 * User: yanglei
 * Date: 2017/2/22
 * Time: 下午2:24
 */

use Illuminate\Validation\Rule;

class Category extends \app\common\models\Category
{

    /**
     * @return mixed
     */
    public static function getAllCategory()
    {
        return self::uniacid()
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * @return mixed
     */
    public static function getAllCategoryGroup()
    {
        $categorys = self::getAllCategory();

        $categoryMenus['parent'] = $categoryMenus['children'] = [];
        
        foreach ($categorys as $category)
        {
            !empty($category['parent_id']) ?
                $categoryMenus['children'][$category['parent_id']][] = $category :
                $categoryMenus['parent'][$category['id']] = $category;
        }

        return $categoryMenus;
    }
    

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public static function getCategory($id)
    {
        return self::find($id);
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function daletedCategory($id)
    {
        return self::where('id', $id)
            ->orWhere('parent_id', $id)
            ->delete();
    }

    /**
     *  定义字段名
     * 可使
     * @return array
     */
    public  function atributeNames()
    {
        return [
            'name' => '分类名称',
        ];
    }

    /**
     * 字段规则
     * @return array
     */
    public  function rules()
    {
        return [
            'name' => ['required', Rule::unique($this->table)
                ->ignore($this->id)
                ->where('uniacid', \YunShop::app()->uniacid)],
        ];
    }
}