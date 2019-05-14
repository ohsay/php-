<?php
namespace app\api\validate;

use \think\Validate;
//品牌验证器

class Login extends validate
{

    protected $rule = [
        'mobile'         => 'require|chsAlpha|max:30',
        'user_name'     => 'require|chsAlpha',
        'account'       =>  'require|alphaNum|length:16,19', 
        'address'       => 'chsAlphaNum',
        
        
    ];
    protected $message = [
        
        'value.require'           => '请填写银行名称',
        'value.chsAlpha'          => '银行名称必须是汉字或者字母',
        'value.max'               => '银行名称最长30位',
        'user_name.require'       => '请填写姓名',
        'user_name.chsAlpha'      => '姓名必须是数字或者字母',
        'account.alphaNum'        => '请填写数字或者字母',
        'address.chsAlphaNum'     => '地址是汉字字母或者数字',
        'account.require'         => '请填写卡号',
        'account.length'          => '银行卡号长度16-19位',
        
    ];
    // protected $scene = [
    //     'editcard' => [ 'value', 'user_name','account','address'],
    //     'pay' => [ 'account','user_name',],
    //     // 'del' => ['admin_id'],
    // ];


}