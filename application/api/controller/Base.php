<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use \Firebase\JWT\JWT;
class Base extends Controller
{
    public $token;
    public function _initialize()
    {
        parent::_initialize();

        // if (!function_exists('getallheaders')) {
        //         $data = $this->getallheaders();
        // }else{
                $data = getallheaders();
        // }        
                // dump($data);exit;
                $this->token = $data['Authorization'];
                
                $res = $this->check_token($this->token);
                if ($res) {
                    json($res)->send();
                    exit;
                }

        // }
        

    }
    function getallheaders()
    {
        $headers = '';
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
    public function check_token($token){
        //判断token
        // $check = Db::name('user_token')->where('user_token', $token)->find();
        // dump($check);
        $key = "huang";  //上一个方法中的 $key 本应该配置在 config文件中的
        $info = JWT::decode($token, $key, ["HS256"]); //解密jwt
        $info = get_object_vars($info);
        if ($info['exp'] < time()) {
            return ['status' => 0, 'msg' => 'token已过期', 'data' => []];
        }        
    }    
}    