<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
class Login extends Controller
{
    /**
     * 获取短信验证码
     */
    public function getCode()
    {
        $data = input();
    
        // 登陆验证码
        if($_GET){

            if ($data['type'] == 0) {
                // 验证
                if (empty($data['mobile'])) {
                    return json(['status' => 1, 'msg' => '请输入手机号']);
                }
                if (!check_mobile_number($data['mobile'])) {
                    return json(['status' => 1, 'msg' => '手机号格式不正确']);
                }
                $code = rand(123456, 999999);
                $tpl = '【SC】您的手机验证码：' . $code . ' 若非您本人操作，请忽略本短信。';
                $content = $tpl;
                // $result = sendSms($data['phone'], $content);
                // if ($result != '1') {
                //     // $res_num = strpos($result,'ok');
                //     // if($res_num != 8){
                //     return array('code' => 0, 'msg' => '短信发送失败-');
                // }

                // 插入verify_code记录

                //过期时间60s
                //60s内如果一致就可以通过。

                $db_data = array(
                    'code' => $code,
                    'phone' => $data['mobile'],
                    'sms_type' => 1,
                    'create_time' => time(),
                    // 'create_ip'=>CLIENT_IP,
                    'sms_con' => $content
                );
                $res = Db::name('verify_code')->insert($db_data);
                if (!$res) {
                    return array('code' => 0, 'msg' => '系统繁忙请稍后再试');
                }

                $code_info = [
                    'code' => $code
                ];
                return json(['status' => 0, 'msg' => '短信发送成功', 'data' => $code_info]);
            }
        }

    }

    /**
     * 登录逻辑
     */
    public function login(){
        $data = input('post.');
        if($_POST){
            //验证

            // 从数据库获取code验证码
            
        }

    }  

/**
 * 
 *    用户登录成功，给用户一个token,这个token相当于你们的userid
 *    我前端保存这个token,比如要请求用户信息的借口，那我前端给什么给你，你才知道是我请求？
 *    token可以设置过期时长，也就是个多长时间，token就失效要重新登录获取新的token,但是userid是唯一的，不存在过期的情况，比较不安全。哪个app是永久不会登陆过期的？
 * 
 * 
 * 
 * 
 * 
 */


 





}