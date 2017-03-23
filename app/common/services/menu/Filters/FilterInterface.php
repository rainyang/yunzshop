<?php

namespace app\common\services\menu\Filters;

use app\common\services\menu\Builder;

interface FilterInterface
{
    public function transform($item, Builder $builder);
}
