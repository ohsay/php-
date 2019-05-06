<?php
namespace app\index\controller;
use think\Controller;

class Index extends Controller
{
    public function index()
    {
        $data = input('get.');
        // 验证
        if($data['type'] == 0){
            $code = [
                'code'=> '5695'
            ];
            return json(['status'=>0,'msg'=>'短信发送成功','data'=>$code]);
        }
    }
}
