<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Db;
// 应用公共文件
/**
 * 手机号格式检查
 * @param string $mobile
 * @return bool
 */
function check_mobile_number($mobile)
{
    if (!is_numeric($mobile)) {
        return false;
    }
    $reg = '#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#';

    return preg_match($reg, $mobile) ? true : false;
}

// 校验手机验证码
function checkPhoneCode($data){
	// if(!$data['sms_type']||!$data['code']||!$data['phone']){
    //     return array('code' => 0, 'msg' => '缺少验证参数');
    // }
    $item = Db::name('verify_code')->where(['phone'=>$data['phone']])->order('id desc')->find();
	if(!$item['id']|| ($data['code'] != $item['code'])){
        return array('code' => 0, 'msg' => '该验证码不正确');
    }
	if($item['status']||$item['verify_num']>2){
        return array('code' => 0, 'msg' => '请重新获取验证码');
    }
    
	//查到验证码且验证使用未达到限制次数
	$msg='';
	$db_data=array('verify_num'=>$item['verify_num']+1);
	if($data['code']==$item['code']){
		//检测验证码有效期
		if(time()-$item['create_time']>1800){
			$msg='该验证码已失效';
			$db_data['status']=1;
		}else{
			$db_data['status']=2;
		}
	}else{
		$msg='该验证码不正确';
		if($db_data['verify_num']>2){
			$db_data['status']=1;
		}
	}
    $db_data['verify_time'] = time();
    $res = Db::name('verify_code')->where(['id'=>$item['id']])->update($db_data);
	if(!$res){
		$msg='该验证码不正确';
	}
	if($msg){
        return array('code' => 0, 'msg' => $msg);
	}
    return array('code' => 200, 'msg' => '验证通过');
}