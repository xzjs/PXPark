<?php
/**
 * Created by PhpStorm.
 * User: xzjs
 * Date: 15/10/24
 * Time: 下午3:33
 */
namespace Home\Controller;

use Think\Controller;

class UserController extends Controller
{
    /**
     * 用户注册
     * @param $phone 手机号
     * @param $nickname 昵称
     * @param $pwd 密码
     * @return int -1:电话号码已存在;-2:内部错误;正数:返回的插入id
     */
    public function register($phone,$nickname,$pwd){
        $User=D("User");
        $data['phone']=$phone;
        $data['nickname']=$nickname;
        $data['pwd']=$pwd;
        if($User->create($data)){
            $result=$User->add();
            if($result){
                return $result;
            }else{
                return -2;
            }
        }else{
            return -1;
        }
    }
}