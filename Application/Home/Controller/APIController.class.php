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
     * 使用条款
     */
    public function privacy()
    {
        $car = A('Car');
        $result = $car->getprivacy();
        echo $result;
    }

    /**
     * 车辆删除
     * @param $id 车辆id
     */
    public function delete_car()
    {

        $car = A('Car');
        $code = $car->delete_car(I('post.id'));
        $array = array("code" => $code);
        //echo $array;
        echo json_encode($array);

    }

    /**
     * 我的车辆查询
     *
     * @param $id 用户id
     */
    public function my_car()
    {
        $mycar_infor = A('Car');
        $id = $_POST ['id'];
        $list = $mycar_infor->get_mycar_info($id);
        if ($list) {
            $code = 0;// 0：成功

        } else {
            if ($list == NULL) {
                //$code = 9; // 结果为空
            } else {
                $code = 4;//内部出错
            }
        }

        $array = array("code" => $code, "car_list" => $list);
        //echo $array;
        echo json_encode($array);

    }

    /**
     * 增加车辆
     *
     * @return int 7:车牌号已存在;4:内部错误;0:成功
     */
    public function add_car()
    {
        $car = A('Car');
        $code = $car->add_car_in_usrcar(I('post.id'), I('post.type'), I('post.no'));
        $array = array("code" => $code);
        echo json_encode($array);
    }

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
            case 3:
                $data['code'] = 5;
                break;
            default:
                break;
        }
        if ($data['code'] == 0) {
            $User = A('User');
            $result = $User->register(I('post.phone'), I('post.name'), I('post.pwd'));
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
    public function login()
    {
        $User = A('User');
        $data['code'] = 0;
        $result = $User->login(I('post.phone'), I('post.pwd'));
        switch ($result) {
            case -1:
                $data['code'] = 5;
                break;
            case -2:
                $data['code'] = 6;
                break;
            default:
                $data['id'] = $result;
                break;
        }
        echo json_encode($data);
    }

    /**
     * 修改密码API
     */
    public function change_pwd()
    {
        $Captcha = A('Captcha');
        $User=D('User');
        $data_user=$User->find(I('post.id'));
        $data['code'] = 0;
        $code_status = $Captcha->verify($data_user['phone'], I('post.captcha'));
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
            $result = $User->change_pwd(I('post.id'), I('post.pwd'));
            if (!$result) {
                $data['code'] = 4;
            }
        }
        echo json_encode($data);
    }

    /**
     * 查询余额接口
     */
    public function remain()
    {
        $User = D('User');
        $result = $User->find(I('post.id'));
        $data['code'] = 0;
        if ($result) {
            $data['money'] = $User->remain;
        } else {
            $data['code'] = 7;
        }
        echo json_encode($data);
    }

    /**
     * 获取用户详细信息接口
     */
    public function user()
    {
        $User = A('User');
        $data['code'] = 0;
        $result = $User->detail(I('post.id'));
        if ($result == -1) {
            $data['code'] = 7;
        } else {
            $u['nickname'] = $result['nickname'];
            $u['phone'] = $result['phone'];
            $u['img'] = C('IP').__ROOT__.'/Uploads/'.$result['img'];
            $data['user'] = $u;
        }
        echo json_encode($data);
    }

    /**
     * 获取验证码接口
     */
    public function captcha()
    {
        $Captcha = A('Captcha');
        $Captcha->create(I('post.phone'));
    }

    /**
     * 修改头像接口
     */
    public function change_img()
    {
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 3145728;// 设置附件上传大小
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath = './Uploads/'; // 设置附件上传根目录
        $upload->autoSub = false;
        $upload->saveName = time() . '_' . mt_rand();
        // 上传文件
        $info = $upload->upload();
        $data['code'] = 0;
        if (!$info) {// 上传错误提示错误信息
            $data['code'] = 4;
        } else {// 上传成功
            $User = D('User');
            $User->find(I('post.id'));
            $User->img = $info['img']['savename'];
            $User->save();
        }
        echo json_encode($data);
    }

    /**
     * 获取目的停车场列表
     */
    public function park_list()
    {
        //$park_model=D('Park');
        //if($park_model->create())
        $data['code'] = 0;
        /* else
            $data['code']=4; */
        $park = A('Park');
        $reslut = $park->getList(I('param.lon'), I('param.lat'));
        $data['park_list'] = $reslut;
        echo json_encode($data);
    }

    /**
     * 获取指定停车场详情
     */
    public function park_detail()
    {
        $park = A('Park');
        $reslut = $park->getDetail(I('param.id'));
        if (count($reslut) == 0)
            $data['code'] = 7;
        else
            $data['code'] = 0;
        $data['park'] = $reslut;
        echo json_encode($data);
    }

    public function record()
    {
        $park = A('Park');
        $reslut = $park->getRecord(I('param.id'));
        if (count($reslut) == 0)
            $data['code'] = 7;
        else
            $data['code'] = 0;
        $data['record'] = $reslut;
        echo json_encode($data);
    }

    public function recharge()
    {
        $park = A('Rechargerecord');
        $reslut = $park->getList(I('param.id'));
        if (count($reslut) == 0)
            $data['code'] = 7;
        else
            $data['code'] = 0;
        $data['record'] = $reslut;
        echo json_encode($data);
    }

}