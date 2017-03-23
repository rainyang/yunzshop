<?php

namespace app\common\services\menu\Filters;

use Illuminate\Contracts\Auth\Access\Gate;
use app\common\services\menu\Builder;

class GateFilter implements FilterInterface
{
    protected $gate;

    public function __construct(Gate $gate)
    {
        $this->gate = $gate;
    }

    public function transform($item, Builder $builder)
    {
        if (! $this->isVisible($item)) {
            return false;
        }

        if (isset($item['header'])) {
            $item = $item['header'];
        }

        return $item;
    }

    protected function isVisible($item)
    {
        return ! isset($item['can']) || $this->gate->allows($item['can']);
    }
}
