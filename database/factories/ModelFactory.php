<?php

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
    ];
});

$factory->define(Yunshop\Poster\PosterModel::class, function (Faker\Generator $faker) {
    return [
        'uniacid' => $faker->numberBetween(1, 3),
        'title' => $faker->words(2,true),
        'type' => $faker->numberBetween(1,2),
        'keyword' => $faker->word,
        'time_start' => $faker->unixTime,
        'time_end' => $faker->unixTime + 432000,
        'valid_days' => $faker->numberBetween(1,5),
        'background' => $faker->imageUrl(),
        'style_data' => $faker->words(3,true),
        'push_title' => $faker->words(2,true),
        'push_thumb' => $faker->imageUrl(),
        'push_desc' => $faker->sentence,
        'push_url' => $faker->url,
        'is_open' => $faker->numberBetween(0,1),
        'auto_sub' => $faker->numberBetween(0,1),
        'status' => $faker->numberBetween(0,1),
//        'created_at' => $faker->unixTime,
//        'updated_at' => $faker->unixTime,
//        'deleted_at' => $faker->unixTime,
    ];
});

$factory->define(Yunshop\Poster\PosterSupplementModel::class, function (Faker\Generator $faker) {
    return [
        'poster_id' => function(){
            return factory(Yunshop\Poster\PosterModel::class)->create()->id;
        },
        'not_start_reminder' => '活动还未开始, 请耐心等待',
        'finish_reminder' => '活动已结束, 谢谢您的关注!',
        'wait_reminder' => '您的专属海报正在拼命生成中, 请等待片刻...',
        'not_open_reminder' => '您还没有发展下线的资格, 去努力拥有资格吧!',
        'not_open_reminder_url' => $faker->url,
        'subscriber_credit' => $faker->numberBetween(0, 10),
        'subscriber_bonus' => $faker->numberBetween(0, 10),
        'bonus_method' => $faker->randomElement(array(1, 2)),
        'subscriber_coupon_id' => $faker->numberBetween(1000, 1999),
        'subscriber_coupon_num' => $faker->numberBetween(0, 10),
        'recommender_credit' => $faker->numberBetween(0, 10),
        'recommender_bonus' => $faker->numberBetween(0, 10),
        'recommender_coupon_id' => $faker->numberBetween(1000, 1999),
        'recommender_coupon_num' => $faker->numberBetween(0, 10),
        'subscriber_award_notice' => '您获得了扫码关注的奖励',
        'recommender_award_notice' => '您获得了推荐用户扫码关注的奖励',
        'subscriber_booked_notice' => '扫码关注获得的奖励已入账',
        'recommender_booked_notice' => '推荐扫码关注获得的奖励已入账',
    ];
});


