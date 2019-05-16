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

    public function upload_avatar(){
        if(!isset($_FILES['file'])){
            return json(['status'=>1,'msg'=>'请上传图片','data'=>[]]);
        }
        $file = $_FILES['file']; //得到传输的数据
        $name = $file['name'];
        $type = strtolower(substr($name, strrpos($name, '.') + 1)); //得到文件类型，并且都转化成小写
        $allow_type = array('jpg', 'jpeg', 'gif', 'png'); //定义允许上传的类型
        //判断文件类型是否被允许上传
        if (!in_array($type, $allow_type)) {
            //如果不被允许，则直接停止程序运行
            return json(['status'=>1,'msg'=>'上传文件格式错误','data'=>[]]);
        }
        //判断是否是通过HTTP POST上传的
        if (!is_uploaded_file($file['tmp_name'])) {
            //如果不是通过HTTP POST上传的
            return json(['status'=>1,'msg'=>'请使用post格式','data'=>[]]);
        }
        $upload_path = ROOT_PATH . 'public' . DS . 'upload'; //上传文件的存放路径
        $explode = explode(".", $file['name']);
        $md = md5($explode[1]).'.'.$explode[1];
        $res = move_uploaded_file($file['tmp_name'], $upload_path . '/' . $md);
        //开始移动文件到相应的文件夹
        if ($res) {
            return json(['status'=>0,'msg'=>'上传成功','data'=>['url'=>$upload_path . '/' . $md]]);
        } else {
            return json(['status'=>1,'msg'=>'上传失败','data'=>[]]);
        }

    }

}