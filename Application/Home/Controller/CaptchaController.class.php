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
    public function create($phone){

    }

    /**
     * 验证验证码
     * @param $phone 手机号
     * @param $code 验证码
     * @return int 0:正确;1:验证码错误;2:验证码超时
     */
    public function verify($phone,$code){
        return 0;
    }
}