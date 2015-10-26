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
    public function register($phone, $nickname, $pwd)
    {
        $User = D("User");
        $data['phone'] = $phone;
        $data['nickname'] = $nickname;
        $data['pwd'] = $pwd;
        if ($User->create($data)) {
            $result = $User->add();
            if ($result) {
                return $result;
            } else {
                return -2;
            }
        } else {
            return -1;
        }
    }

    /**
     * 用户登录
     * @param $phone 手机号
     * @param $pwd 密码
     * @return int|mixed -1:手机号未注册;-2:密码错误;正数:用户id
     */
    public function login($phone, $pwd)
    {
        $User = D('User');
        $data = $User->where('phone="' . $phone . '"')->find();
        if ($data) {
            if ($data['pwd'] == md5($pwd)) {
                return $data['id'];
            } else {
                return -2;
            }
        } else {
            return -1;
        }
    }

    /**
     * 修改密码
     * @param $id 用户id
     * @param $pwd 密码
     * @return bool 受影响的行数或者false
     */
    public function change_pwd($id, $pwd)
    {
        $User = D('User');
        $User->find($id);
        $User->pwd = md5($pwd);
        $result = $User->save();
        return $result;
    }

    public function change_img($id, $img)
    {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Uploads/'; // 设置附件上传根目录
        $upload->autoSub = false;
        $upload->saveName = time() . '_' . mt_rand();
        // 上传文件
        $info = $upload->upload();
        if (!$info) {// 上传错误提示错误信息
            $this->error($upload->getError());
        } else {// 上传成功
            $User=D('User');
            $User->find($id);
        }
    }

    /**
     * 获取用户详细信息
     * @param $id 用户id
     * @return int|mixed -1:用户id未找到;数组:用户信息
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