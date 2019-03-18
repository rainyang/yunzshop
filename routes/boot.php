<?php
Route::get('/', function () {
    $path = 'addons/yun_shop';
    $file = $path .  '/api.php';

    if (!file_exists($file)) {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $f_data = file_get_contents('api.php');

        file_put_contents($file, $f_data);
    }

    return redirect('/admin/index');
});