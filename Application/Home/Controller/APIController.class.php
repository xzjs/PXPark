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
 * Class APIController
 * @package Home\Controller
 */
class APIController extends Controller
{
    /**
     * 用户注册API
     */
    public function register(){
        $User=A('User');
        $result=$User->register(I('post.phone'),I('post.nickname'),I('post.pwd'));
        $data['code']=0;
        switch($result){
            case -1:
                $data['code']=3;
                break;
            case -2:
                $data['code']=4;
                break;
            default:
                break;
        }
        $data['id']=$result;
        echo json_encode($data);
    }
}