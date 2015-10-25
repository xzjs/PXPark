<?php
/**
 * Created by PhpStorm.
 * User: xzjs
 * Date: 15/10/24
 * Time: 下午2:10
 */
namespace Home\Controller;

use Think\Controller;

/**
 * API控制器
 * @package Home\Controller
 */
class APIController extends Controller
{
    /**
     * 用户注册API
     */
    public function register()
    {
        $Captcha = A('Captcha');
        $data['code'] = 0;
        $code_status = $Captcha->verify(I('post.phone'), I('post.captcha'));
        switch ($code_status) {
            case 1:
                $data['code'] = 1;
                break;
            case 2:
                $data['code'] = 2;
                break;
            default:
                break;
        }
        if ($data['code'] == 0) {
            $User = A('User');
            $result = $User->register(I('post.phone'), I('post.nickname'), I('post.pwd'));
            switch ($result) {
                case -1:
                    $data['code'] = 3;
                    break;
                case -2:
                    $data['code'] = 4;
                    break;
                default:
                    $data['id'] = $result;
                    break;
            }
        }
        echo json_encode($data);
    }

    /**
     * 用户登录API
     */
    public function login(){
        $User=A('User');
        $data['code']=0;
        $result=$User->login(I('post.phone'),I('post.pwd'));
        switch($result){
            case -1:
                $data['code']=5;
                break;
            case -2:
                $data['code']=6;
                break;
            default:
                $data['id']=$result;
                break;
        }
        echo json_encode($data);
    }

    /**
     * 修改密码API
     */
    public function change_pwd(){
        $Captcha = A('Captcha');
        $data['code'] = 0;
        $code_status = $Captcha->verify(I('post.phone'), I('post.captcha'));
        switch ($code_status) {
            case 1:
                $data['code'] = 1;
                break;
            case 2:
                $data['code'] = 2;
                break;
            default:
                break;
        }
        if ($data['code'] == 0) {
            $User = A('User');
            $result = $User->change_pwd(I('post.id'),I('post.pwd'));
            if(!$result){
                $data['code']=4;
            }
        }
        echo json_encode($data);
    }
}