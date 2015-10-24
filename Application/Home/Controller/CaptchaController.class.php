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
 * Class CaptchaController
 * @package Home\Controller
 */
class CaptchaController extends Controller
{
    public function create($phone){

    }

    public function verify($phone,$code){
        return true;
    }
}