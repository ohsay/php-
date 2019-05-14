<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
class User extends Base
{
    //  获取用户信息
    public function index(){
        $token = $this->token;
        // $res = Db::name('user_token')->where('user_token',$token)->find();
        $res = Db::name('users')->where('token',$token)->field( 'nickname,sex,avatar')->find();
        if($res){
            return json(['status'=>0,'msg'=>'获取用户信息成功','data'=>$res]);
        }else{
            return json(['status' => 0, 'msg' => '获取用户信息失败', 'data' => []]);
        }
    }
}