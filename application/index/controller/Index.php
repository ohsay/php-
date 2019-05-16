<?php
namespace app\index\controller;
use think\Controller;
use \Firebase\JWT\JWT;
class Index extends Controller
{
    public function index()
    {
        //  return $this->success('跳转成功','index/index');
        // $array[] = array("age" => 20, "name" => "li");
        // $array[] = array("age" => 21, "name" => "ai");
        // $array[] = array("age" => 20, "name" => "ci");
        // $array[] = array("age" => 22, "name" => "di");

        // foreach ($array as $key => $value) {
        //     $age[$key] = $value['age'];
        //     $name[$key] = $value['name'];
        // }

        // array_multisort($age, SORT_NUMERIC, SORT_DESC, $name, SORT_STRING, SORT_ASC, $array);
        // dump($age);
        // dump( $name);
        // dump($array);
        return $this->fetch();
    }
    public function getToken()
    {
        $key = "huang";  //这里是自定义的一个随机字串，应该写在config文件中的，解密时也会用，相当    于加密中常用的 盐  salt
        $token = [
            "iss" => "",  //签发者 可以为空
            "aud" => "", //面象的用户，可以为空
            "iat" => time(), //签发时间
            "nbf" => time() + 100, //在什么时候jwt开始生效  （这里表示生成100秒后才生效）
            "exp" => time() + 7200, //token 过期时间
            "uid" => 123 //记录的userid的信息，这里是自已添加上去的，如果有其它信息，可以再添加数组的键值对
        ];
        $jwt = JWT::encode($token, $key, "HS256"); //根据参数生成了 token
        return $jwt;
        // return json([
        //     "token" => $jwt
        // ]);
    }
    public function check()
    {
        $jwt = input("token");  //上一步中返回给用户的token
        $key = "huang";  //上一个方法中的 $key 本应该配置在 config文件中的
        $info = JWT::decode($jwt, $key, ["HS256"]); //解密jwt
        return json($info);
    }

    public function upload_avatar(){
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
