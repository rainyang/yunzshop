<?php
/**
 * Created by PhpStorm.
 * User: jan
 * Date: 22/02/2017
 * Time: 23:54
 */

namespace app\backend\modules\member\models;


use Prettus\Validator\LaravelValidator;

class TestMemberValidator extends LaravelValidator
{
    protected $rules = [
        'title' => 'required',
        'email' => 'required',
        'text'  => 'min:3',
        'author'=> 'required'
    ];

    protected $messages = [
        'required' => 'The :attribute field is required.',
        'email.required' => 'We need to know your e-mail address!',
    ];
}