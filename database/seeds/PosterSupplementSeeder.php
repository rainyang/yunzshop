<?php

use Illuminate\Database\Seeder;

Class PosterSupplementSeeder extends Seeder
{
    public function run()
    {
        factory(Yunshop\Poster\PosterSupplementModel::class, 10)->create()->make();
    }
}