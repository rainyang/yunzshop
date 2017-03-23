<?php

namespace JeroenNoten\LaravelAdminLte\Events;

use app\common\services\menu\Builder;

class BuildingMenu
{
    public $menu;

    public function __construct(Builder $menu)
    {
        $this->menu = $menu;
    }
}
