<?php
/**
 * Created by PhpStorm.
 * User: xzjs
 * Date: 15/10/24
 * Time: 下午3:33
 */
namespace Home\Controller;

use Think\Controller;

/**
 * 用户控制器
 * @package Home\Controller
 */
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

    /**
     * 用户登录
     * @param $phone 手机号
     * @param $pwd 密码
     * @return int|mixed -1:手机号未注册;-2:密码错误;正数:用户id
     */
    public function login($phone,$pwd){
        $User=D('User');
        $data=$User->where('phone="'.$phone.'"')->find();
        if($data){
            $pwd=md5($pwd);
            if($data['pwd']==$pwd){
                return $data['id'];
            }else{
                return -2;
            }
        }else{
            return -1;
        }
    }

    /**
     * 修改密码
     * @param $id 用户id
     * @param $pwd 密码
     * @return bool 受影响的行数或者false
     */
    public function change_pwd($id,$pwd){
        $User=D('User');
        $User->find($id);
        $User->pwd=md5($pwd);
        $result=$User->save();
        return $result;
    }

    /**
     * 查询用户的详细信息
     * @param $id 用户id
     * @return int|mixed -1:用户id不存在;数组:用户详细信息数组
     */
    public function detail($id){
        $User=D('User');
        $data=$User->find($id);
        if($data){
            return $data;
        }else{
            return -1;
        }
    }
}