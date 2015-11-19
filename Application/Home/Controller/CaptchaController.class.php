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
    public function create($phone)
    {
        $Info=M('Info');
        $data_info=$Info->find();
        $username = $data_info['username'];       //用户账号
        $password = $data_info['password'];    //密码
        $apikey = $data_info['apikey'];    //密码
        $mobile = $phone;    //号手机码
        $code=rand(1000,9999);
        $content = "您的短信验证码是：$code";        //内容
//即时发送
        $result = $this->sendSMS($username, $password, $mobile, $content, $apikey);
        echo $result;
        $Captcha = D('Captcha');
        $data['captcha'] = $code;
        $data['phone'] = $phone;
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

    /*--------------------------------
    功能:		PHP HTTP接口 发送短信
    修改日期:	2013-05-08
    说明:		http://m.5c.com.cn/api/send/?username=用户名&password=密码&mobile=手机号&content=内容&apikey=apikey
    状态:
        发送成功	success:msgid
        发送失败	error:msgid

    注意，需curl支持。

    返回值											说明
    success:msgid								提交成功，发送状态请见4.1
    error:msgid								提交失败
    error:Missing username						用户名为空
    error:Missing password						密码为空
    error:Missing apikey						APIKEY为空
    error:Missing recipient					手机号码为空
    error:Missing message content				短信内容为空
    error:Account is blocked					帐号被禁用
    error:Unrecognized encoding				编码未能识别
    error:APIKEY or password error				APIKEY 或密码错误
    error:Unauthorized IP address				未授权 IP 地址
    error:Account balance is insufficient		余额不足
    error:Black keywords is:党中央				屏蔽词
    --------------------------------*/


    public function sendSMS($username, $password, $mobile, $content, $apikey)
    {
        $url = 'http://m.5c.com.cn/api/send/?';
        $data = array
        (
            'username' => $username,                    //用户账号
            'password' => $password,                //密码
            'mobile' => $mobile,                    //号码
            'content' => $content,                //内容
            'apikey' => $apikey,                    //apikey
        );
        $result = $this->curlSMS($url, $data);            //POST方式提交
        return $result;
    }

    public function curlSMS($url, $post_fields = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3600); //60秒
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_REFERER, 'http://www.yourdomain.com');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        $data = curl_exec($ch);
        curl_close($ch);
        $res = explode("\r\n\r\n", $data);
        return $res[2];
    }
}