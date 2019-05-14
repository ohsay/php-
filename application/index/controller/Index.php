<?php
namespace app\index\controller;
use think\Controller;
use \Firebase\JWT\JWT;
class Index extends Controller
{
    public function index()
    {
        //  return $this->success('跳转成功','index/index');
        $array[] = array("age" => 20, "name" => "li");
        $array[] = array("age" => 21, "name" => "ai");
        $array[] = array("age" => 20, "name" => "ci");
        $array[] = array("age" => 22, "name" => "di");

        foreach ($array as $key => $value) {
            $age[$key] = $value['age'];
            $name[$key] = $value['name'];
        }

        array_multisort($age, SORT_NUMERIC, SORT_DESC, $name, SORT_STRING, SORT_ASC, $array);
        dump($age);
        dump( $name);
        dump($array);
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
}
