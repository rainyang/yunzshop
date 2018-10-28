<?php
/**
 * Created by PhpStorm.
 * Author: 芸众商城 www.yunzshop.com
 * Date: 16/03/2017
 * Time: 08:58
 */

namespace app\common\traits;


use ArrayAccess;
use BadMethodCallException;
use Illuminate\Support\Collection;
use InvalidArgumentException;

/**
 * Class TreeTrait
 *
 * 使用
 * <?php
 * namespace app\common\models;
 * use app\common\traits\TreeTrait;
 *
 * class MyClass
 * {
 *      use TreeTrait;
 *
 *      // 自定义属性（可选）
 *      protected $treeNodeIdName = 'id';
 *      protected $treeNodeParentIdName = 'parent_id';
 *      protected $treeNodeDisplayName = 'name';
 *      protected $treeSpacer = '&nbsp;&nbsp;&nbsp;';
 *      protected $treeFirstIcon = '&nbsp;&nbsp;&nbsp;│ ';
 *      protected $treeMiddleIcon = '&nbsp;&nbsp;&nbsp;├─ ';
 *      protected $treeLastIcon = '&nbsp;&nbsp;&nbsp;└─ ';
 * /**
 *  * 获取待处理的原始节点数据
 *  *
 *  * 必须实现
 *  *
 *  * return \Illuminate\Support\Collection
 *  *
 *      public function getTreeAllNodes()
 *      {
 *
 *      }
 * }
 *
 * 可用方法
 * ```php
 *
 * public function setAllNodes(Collection $nodes)
 * public function getSubLevel($parentId)
 * public function getDescendants($parentId, $depth = 0, $adds = '')
 * public function getLayerOfDescendants($id)
 * public function getSelf($id)
 * public function getParent($id)
 * public function getAncestors($id, $depth = 0)
 *```
 * @package app\common\traits
 */
trait MemberTreeTrait
{
    public $_allNodes = null;

    protected $treeNodeIdName = 'member_id';
    protected $treeNodeParentIdName = 'parent_id';
    protected $treeNodeDisplayName = 'name';
    protected $treeSpacer = '&nbsp;';
    protected $treeFirstIcon = '&nbsp;│ ';
    protected $treeMiddleIcon = '&nbsp;├─ ';
    protected $treeLastIcon = '&nbsp;└─ ';

    /**
     * 数据主ID名.
     *
     * @return string
     */
    protected function getTreeNodeIdName()
    {
        return property_exists($this, 'treeNodeIdName') ? $this->treeNodeIdName : 'id';
    }

    /**
     * 数据名称显示字段.
     *
     * @return string
     */
    protected function getTreeNodeDisplayName()
    {
        return property_exists($this, 'treeNodeDisplayName') ? $this->treeNodeDisplayName : 'name';
    }

    /**
     * 数据父ID名.
     *
     * @return string
     */
    protected function getTreeNodeParentIdName()
    {
        return property_exists($this, 'treeNodeParentIdName') ? $this->treeNodeParentIdName
            : 'parent_id';
    }

    protected function getTreeSpacer()
    {
        return property_exists($this, 'treeSpacer') ? $this->treeSpacer : '   ';
    }

    protected function getTreeFirstIcon()
    {
        return property_exists($this, 'treeFirstIcon') ? $this->treeFirstIcon
            : '   │ ';
    }

    protected function getTreeMiddleIcon()
    {
        return property_exists($this, 'treeMiddleIcon') ? $this->treeMiddleIcon
            : '   ├─ ';
    }

    protected function getTreeLastIcon()
    {
        return property_exists($this, 'treeLastIcon') ? $this->treeLastIcon
            : '   └─ ';
    }

    /**
     * 获取待格式树结构的节点数据.
     *
     * @return mixed
     */
    final protected function getAllNodes($uniacid)
    {
        if ($this->_allNodes) {
            \Log::debug('------allnodes----');
            return $this->_allNodes;
        }
        if (!method_exists($this, 'getTreeAllNodes')) {
            throw new BadMethodCallException('Method [getTreeAllNodes] does not exist.');
        }
        $data = $this->getTreeAllNodes($uniacid); // 由use的class来实现
        if (!$data instanceof ArrayAccess) {
            throw new InvalidArgumentException('tree data must be a collection');
        }
        // 重置键值
        $this->_allNodes = collect([]);
        foreach ($data as $item) {
            $this->_allNodes->put($item->{$this->getTreeNodeIdName()}, $item);
        }
        return $this->_allNodes;
    }

    /**
     * 设置 所有节点.
     *
     * @param \Illuminate\Support\Collection $nodes
     */
    public function setAllNodes(Collection $nodes)
    {
        $this->_allNodes = $nodes;
    }

    /**
     * 获取子级（仅子代一级）.
     *
     * @param mixed $parentId
     *
     * @return array
     */
    public function getSubLevel($uniacid, $parentId)
    {
        $data = $this->getAllNodes($uniacid);
        $childList = collect([]);

        foreach ($data as $val) {
            if ($val->{$this->getTreeNodeParentIdName()} == $parentId) {
                $childList->put($val->{$this->getTreeNodeIdName()}, $val);
            }
        }
        return $childList;
    }

    /**
     * 获取父级（仅一级）.
     *
     * @param mixed $parentId
     *
     * @return array
     */
    public function getParentLevel($uniacid, $subId)
    {
        $data = $this->getAllNodes($uniacid);
        $parentList = collect([]);

        if (!empty($data[$subId]) && $data[$subId]['parent_id'] > 0) {
            $parentList->put($subId, $data[$subId]);
        }

        /*foreach ($data as $val) {
            if ($val->{$this->getTreeNodeIdName()} == $subId && 0 == $val->{$this->getTreeNodeParentIdName()}) {
                return $parentList;
            }

            if ($val->{$this->getTreeNodeIdName()} == $subId && $val->{$this->getTreeNodeParentIdName()} > 0) {
                $parentList->put($val->{$this->getTreeNodeParentIdName()}, $val);
            }
        }*/
        return $parentList;
    }

    /**
     * 获取指定节点的所有后代.
     *
     * @param mixed $parentId
     * @param int $depth
     * @param string $adds
     *
     * @return \Illuminate\Support\Collection
     */
    public function getDescendants($uniacid, $parentId, $depth = 0, $adds = '')
    {
        static $array;
        if (!$array instanceof ArrayAccess || $depth == 0) {
            $array = collect([]);
        }
        $number = 1;
        $child = $this->getSubLevel($uniacid, $parentId);
\Log::debug('------child----', $child->count());
        if ($child) {
            $nextDepth = $depth + 1;
            $total = $child->count();
            foreach ($child as $val) {
                $j = $k = '';
                if ($number == $total) {
                    $j .= $this->getTreeLastIcon();
                    $k = $this->getTreeSpacer();
                } else {
                    $j .= $this->getTreeMiddleIcon();
                    $k = $adds ? $this->getTreeFirstIcon() : '';
                }
                $val->spacer = $adds ? ($adds . $j) : '';
                $val->depth = $depth;
                $array->put($val->{$this->getTreeNodeIdName()}, $val);
                $this->getDescendants($uniacid,
                    $val->{$this->getTreeNodeIdName()},
                    $nextDepth,
                    $adds . $k . $this->getTreeSpacer()
                );
                ++$number;
            }
        }
        return $array;
    }

    public function getNodeParents($uniacid, $subId, $depth = 0, $adds = '')
    {
        static $array;
        if (!$array instanceof ArrayAccess || $depth == 0) {
            $array = collect([]);
        }
        $number = 1;
        $parent = $this->getParentLevel($uniacid, $subId);

        \Log::debug('------parent----', $parent->count());
        if ($parent) {
            $nextDepth = $depth + 1;
            $total = $parent->count();
            foreach ($parent as $val) {
                $j = $k = '';
                if ($number == $total) {
                    $j .= $this->getTreeLastIcon();
                    $k = $this->getTreeSpacer();
                } else {
                    $j .= $this->getTreeMiddleIcon();
                    $k = $adds ? $this->getTreeFirstIcon() : '';
                }
                $val->spacer = $adds ? ($adds . $j) : '';
                $val->depth = $depth;
                $array->put($val->{$this->getTreeNodeParentIdName()}, $val);
                $this->getNodeParents($uniacid,
                    $val->{$this->getTreeNodeParentIdName()},
                    $nextDepth,
                    $adds . $k . $this->getTreeSpacer()
                );
                ++$number;
            }
        }
        return $array;
    }

    /**
     * 格式化为下拉选择数据
     * @param $parentId
     * @param int $depth
     * @param string $adds
     * @return array
     */
    public function toSelectArray($parentId, $depth = 0, $adds = '')
    {

        $treeList = [];
        $allTrees = $this->getDescendants($parentId, $depth, $adds);
        if ($allTrees) {
            foreach ($allTrees as $value) {
                $id = $value->{$this->getTreeNodeIdName()};
                $treeList[$id] = $value->spacer . $value->{$this->getTreeNodeDisplayName()};
            }
        }

        return $treeList;
    }

    /**
     * 获取指定节点的所有后代（分层级）.
     *
     * @param mixed $id
     *
     * @return \Illuminate\Support\Collection
     */
    public function getLayerOfDescendants($id)
    {
        $child = $this->getSubLevel($id);
        $data = collect([]);
        if ($child) {
            foreach ($child as $val) {
                $val->child = $this->getLayerOfDescendants($val->{$this->getTreeNodeIdName()});
                $data->put($val->{$this->getTreeNodeIdName()}, $val);
            }
        }
        return $data;
    }

    /**
     * 获取指定id的数据.
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function getSelf($id)
    {
        $data = $this->getAllNodes();
        return $data->get($id);
    }

    /**
     * 获取父一级节点.
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function getParent($id)
    {
        $node = $this->getSelf($id);
        if ($node) {
            $parentId = $node->{$this->getTreeNodeParentIdName()};
            return $this->getSelf($parentId);
        }
    }

    /**
     * 获取节点的所有祖先.
     *
     * @param int $id
     * @param int $depth
     *
     * @return array
     */
    public function getAncestors($id, $depth = 0)
    {
        static $array;
        if (!$array instanceof ArrayAccess || $depth == 0) {
            $array = collect([]);
        }
        $parent = $this->getParent($id);
        if ($parent) {
            $nextDepth = $depth + 1;
            $array->prepend($parent);   // 添加到开头
            $this->getAncestors($parent->{$this->getTreeNodeIdName()}, $nextDepth);
        }
        return $array;
    }

}