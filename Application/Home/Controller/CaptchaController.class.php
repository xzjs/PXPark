<?php
/**
 * Created by PhpStorm.
 * User: xzjs
 * Date: 15/10/24
 * Time: 下午9:51
 */
namespace Home\Controller;

use Think\Controller;

/**
 * 验证码控制器
 * @package Home\Controller
 */
class CaptchaController extends Controller
{
    /**
     * 创建验证码
     * @param $phone 用户手机号
     */
    public function create($phone){
        $Captcha=D('Captcha');
        $data['captcha']='1234';
        $data['phone']=$phone;
        $Captcha->create($data);
        $Captcha->add();
    }

    /**
     * 验证验证码
     * @param $phone 手机号
     * @param $code 验证码
     * @return int 0:正确;1:验证码错误;2:验证码超时;3手机未注册
     */
    public function verify($phone,$code){
        $Captcha=D('Captcha');
        $data=$Captcha->where('phone="'.$phone.'"')->order('time desc')->find();
        if($data){
            if($data['captcha']==$code){
                $time=time()-$data['time'];
                return $time < 300 ? 0 : 2;
            }else{
                return 1;
            }
        }else{
            return 3;
        }
    }
}