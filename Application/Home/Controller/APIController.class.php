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

    /**
     * 查询余额接口
     */
    public function remain(){
        $User=D('User');
        $result=$User->find(I('post.id'));
        $data['code']=0;
        if($result){
            $data['money']=$User->remain;
        }else{
            $data['code']=7;
        }
        echo json_encode($data);
    }

    /**
     * 获取用户详细信息接口
     */
    public function user(){
        $User=A('User');
        $data['code']=0;
        $result=$User->detail(I('post.id'));
        if($result==-1){
            $data['code']=7;
        }else{
            $u['nickname']=$result['nickname'];
            $u['phone']=$result['phone'];
            $u['img']=$result['img'];
            $data['user']=$u;
        }
        echo json_encode($data);
    }

    /**
     * 获取验证码接口
     */
    public function captcha(){
        $Captcha=A('Captcha');
        $Captcha->create(I('post.phone'));
    }

    /**
     * 修改头像接口
     */
    public function change_image(){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Uploads/'; // 设置附件上传根目录
        $upload->autoSub = false;
        $upload->saveName = time() . '_' . mt_rand();
        // 上传文件
        $info = $upload->upload();
        $data['code']=0;
        if (!$info) {// 上传错误提示错误信息
            $data['code']=4;
        } else {// 上传成功
            $User=D('User');
            $User->find(I('post.id'));
            $User->img=$info['img']['savename'];
            $User->save();
        }
        echo json_encode($data);
    }
}