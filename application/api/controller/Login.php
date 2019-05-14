<?php
namespace app\api\controller;
use think\Controller;
use think\Db;
use \Firebase\JWT\JWT;
class Login extends Controller
{
    /**
     * 获取短信验证码
     */
    public function getCode()
    {
        $mobile = input('mobile')? input('mobile'):'';
        $type   = input('type')  ? input('type'):'3';
        // 登陆验证码
        // if($_POST){

            if ($type == 1) {
                // 验证
                if (empty($mobile)) {
                    return json(['status' => 1, 'msg' => '请输入手机号']);
                }
                if (!check_mobile_number($mobile)) {
                    return json(['status' => 1, 'msg' => '手机号格式不正确']);
                }

                $limit_time = 60; // 60秒以内不能重复获取
                // $where['phone'] = $data['mobile'];
                // $where['sms_type'] = $data['sms_type'];
                $nowTime = time();
                $list = Db::query("select * from squ_verify_code where phone={$mobile} and '{$nowTime}'-create_time<{$limit_time} limit 0,5");
                $cnt = count($list);
                // 1分钟
                if ($cnt > 1) {
                    return json(['status' => 1, 'msg' => '系统繁忙请稍后再试']);
                }

                $code = rand(1234, 9999);
                $tpl = '【SC】您的手机验证码：' . $code . ' 若非您本人操作，请忽略本短信。';
                $content = $tpl;
                // 验证码验证
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
                    'phone' => $mobile,
                    // 'sms_type' => 1,
                    'create_time' => time(),
                    // 'create_ip'=>CLIENT_IP,
                    'sms_con' => $content
                );
                $res = Db::name('verify_code')->insert($db_data);
                if (!$res) {
                    return json(['status' => 1, 'msg' => '系统繁忙请稍后再试']);
                }

                $code_info = [
                    'code' => $code
                ];
                return json(['status' => 0, 'msg' => '短信发送成功', 'data' => $code_info]);
            }
        // }

    }

    /**
     * 登录逻辑
     */
    public function login(){
        // 防止为空时报错
        $mobile = input('mobile')   ? input('mobile') : '';
        $code   = input('code')     ? input('code') : '';
        // if($_POST){
            //验证
            $err = $this->check_mobile($mobile,$code);
            if ($err) return $err;
            // 检查用户是否存在
            $check = Db::name('users')->where('mobile', $mobile)->find();
        // 启动事务
        Db::startTrans();
        try {  
                if($check){
                    // dump($check);exit;
                    $key = "huang";  //上一个方法中的 $key 本应该配置在 config文件中的
                    $info = JWT::decode($check['token'], $key, ["HS256"]); //解密jwt
                    $info = get_object_vars($info);
                    if($info['exp']<time()){
                        $check['token'] = $this->getToken(); 
                    }
                    $data1=[
                        'login_time'     => time(),
                        'token'          => $check['token']
                    ];
                    $res =Db::name('users')->where('mobile',$mobile)->update($data1);
                    // 提交事务
                    Db::commit(); 
                    return json(['status' => 0, 'msg' => '登录成功', 'data' => $check['token']]);               
                }else{
                    //注册
                    $jwt = $this->getToken();
                    $data1 = [
                        'mobile'         => $mobile,
                        'nickname'       => $mobile,
                        'register_time'  => time(),
                        'login_time'     => time(),
                        'register_method' => 'mobile',
                        'token'          => $jwt
                    ];
                    $res = Db::name('users')->insert($data1);
                    // 生成token
                    // $token = $this->settoken($mobile);
                    // $time_out = strtotime("+2 hour");
                    // $token_info = [
                    //     'user_token' => $token,
                    //     'mobile'     => $mobile,
                    //     'expire_time' => $time_out
                    // ];
                    // $result = Db::name('user_token')->insert($token_info);
                    $t = [
                        'token' => $jwt
                    ];
                    // 提交事务
                    Db::commit();
                    return json(['status' => 0, 'msg' => '登录成功', 'data'=>$t]);   
                }
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return json(['status' => 1, 'msg' => '登录失败','data'=>[]]);
        }   


    }  

    public function check_mobile($mobile,$code){
       
        if (empty($mobile)) {
            return json(['status' => 1, 'msg' => '请输入手机号']);
        }
        $check_phone = check_mobile_number($mobile);
        if (!$check_phone) {
            // return array();
            return json([ 'status' => 1, 'msg' => '手机号格式不正确']);
        }
        if (!$code) {
            // return array('code' => 0, 'msg' => '请输入验证码');
            return json([ 'status' => 1, 'msg' => '请输入验证码']);
        }
        // 验证码
        // $checkData['sms_type'] = $data['sms_type'];
        $checkData['code'] = $code;
        $checkData['phone'] = $mobile;
        $res = checkPhoneCode($checkData);
        if ($res['code'] == 0) {
            return json(['status' => 1, 'msg' => $res['msg']]);
        }            
    }

    public function settoken($mobile){
        $str = md5(uniqid(md5(microtime(true).$mobile), true));  //生成一个不会重复的字符串
        $str = sha1($str);  //加密　　 
        return $str; 
    }

    public function getToken()
    {
        $key = "huang";  //这里是自定义的一个随机字串，应该写在config文件中的，解密时也会用，相当    于加密中常用的 盐  salt
        $token = [
            "iss" => "",  //签发者 可以为空
            "aud" => "", //面象的用户，可以为空
            "iat" => time(), //签发时间
            "nbf" => time() + 1, //在什么时候jwt开始生效  （这里表示生成100秒后才生效）
            "exp" => time() + 7200, //token 过期时间
            "uid" => 123 //记录的userid的信息，这里是自已添加上去的，如果有其它信息，可以再添加数组的键值对
        ];
        $jwt = JWT::encode($token, $key, "HS256"); //根据参数生成了 token
        return $jwt;
    }
    public function check()
    {
        $jwt = input("token");  //上一步中返回给用户的token
        $key = "huang";  //上一个方法中的 $key 本应该配置在 config文件中的
        $info = JWT::decode($jwt, $key, ["HS256"]); //解密jwt
        return json($info);
    }
    
}