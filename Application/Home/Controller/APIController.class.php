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
     * 获取支付宝信息
     */
    function get_alipay()
    {
        //ini_set('date.timezone','Asia/Shanghai');
        $order_no = (String)date("Ymdhms") + rand(1, 10);
        $date = array(
            "pid" => '2088121188830505',
            "account" => 'qdhuitianpingxing@163.com ',
            "private_key" => '',
            "public_key" => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDDI6d306Q8fIfCOaTXyiUeJHkrIvYISRcc73s3vF1ZT7XN8RNPwJxo8pWaJMmvyTn9N4HQ632qJBVHf8sxHi/fEsraprwCtzvzQETrNRwVxLO5jVmRGi60j8Ue1efIlzPXV9je9mkjzOmdssymZkh2QhUrCmZYI/FCEa3/cNMW0QIDAQAB',
            "order_no" => $order_no,
            "notify_url" => 'http://27.223.89.130:48082/PXPark/index.php/Home/API/recharge_add'


        );
        $array = array(
            "code" => '0',
            "msg" => '正常返回',
            "data" => $date,


        );
        echo json_encode($array);
    }

    function timetostring($str)
    {
        $cliptime = explode("-", $str);
        $result = "";
        for ($i = 0; $i < count($cliptime); $i++) {
            $result = $result . $cliptime[$i];
        }
        return $result;
    }

    /**
     * 使用条款
     */
    public function privacy()
    {
        $Privacy = A('Privacy');
        $id = $Privacy->get_privacy();
        $data = array(
            'code' => 0,
            'url' => $this->get_url('/index.php/Home/Privacy/detail/id/' . $id)
        );
        echo json_encode($data);
    }

    /**
     * 车辆删除
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
                $code = 9; // 结果为空
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
        $User = D('User');
        $data_user = $User->find(I('post.id'));
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
        $result = $User->detail(I('param.id'));
        $result_spend = $User->getSpend(I('param.id'));
        if ($result == -1 || $result_spend == -1) {
            $data['code'] = 7;
        } else {
            $u['nickname'] = $result['nickname'];
            $u['phone'] = $result['phone'];
            $u['remain'] = $result['remain'];
            $u['consume'] = $result_spend[0]['consum'];
            $u['img'] = $this->get_url(C('UPLOAD') . $result['img']);
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
        $park = A('Park');
        $reslut = $park->getList(I('param.lon'), I('param.lat'));
        if(!$reslut){
        	$data['code']=4;
        }else{
        	$data['code'] = 0;
        	$data['msg'] = "正常返回";
        	$data['park_list'] = $reslut;
        }
        echo json_encode($data);
    }

    /**
     * 获取指定停车场详情
     */
    public function park_detail()
    {
        $park = A('Park');
        $result = $park->getDetail(I('param.id'));
        if (count($result) == 0)
            $data['code'] = 7;
        else
            $data['code'] = 0;
        $data['msg'] = '正常返回';
        $str=array();
        //var_dump($result);
        foreach ($result['Rule'] as $r) {
            $Rule = D('Rule');
            $rs = $Rule->relation(true)->find($r['id']);
            //var_dump($rs);
            $rule_temp['name']=$r['name'];
            $rule_temp['type']=$rs['Ruletype']['name'];
            $rule_temp['rule_time']=array();
            foreach ($rs['Ruletime'] as $rt) {
                //var_dump($rt);
                array_push($rule_temp['rule_time'],array(
                    'start_time'=>$rt['start_time'],
                    'end_time'=>$rt['end_time'],
                    'fee'=>$rt['fee'],
                    'type'=>$rt['type']==1?'白天':'夜晚',
                    'car_type'=>$rt['car_type']==1?'大车':'小车'
                )) ;

            }
            array_push($str,$rule_temp);
        }
        $d = array(
            'id' => $result['id'],
            'name' => $result['name'],
            'lon' => $result['lon'],
            'lat' => $result['lat'],
            'price' => $result['price'],
            'remain' => $result['remain_num'],
            'total' => $result['total_num'],
            'img' => $this->get_url(C('UPLOAD') . $result['img']),
            'rule' => $str,
            'type' => $result['type'],
            'address' => $result['address']
        );
        $data['park'] = $d;
        echo json_encode($data);
    }

    /**
     * 查询停车记录
     */
    public function record()
    {
        $parkrecord = A('Parkrecord');
        $result = $parkrecord->getRecord(I('param.id'));
        $result_num = count($result);
        if ($result_num == 0) {
            $data['code'] = 7;
        } else {
            $data['code'] = 0;
        }
        $page = I('post.page', 0);
        $num = I('post.num', 0);
        $data['record'] = array();
        if (($page - 1) * $num <= $result_num) {
            $end = $page * $num > $result_num ? $result_num : $page * $num;
            $end = $end == 0 ? $result_num : $end;
            for ($i = ($page - 1) * $num; $i < $end; $i++) {
                $Park = D('Park');
                $Car = D('Car');
                $record_list = array(
                    'park_name' => $Park->where('id=' . $result[$i]['park_id'])->getField('name'),
                    'car_no' => $Car->where('id=' . $result[$i]['car_id'])->getField('no'),
                    'start_time' => date('Y-m-d H:i:s',$result[$i]['start_time']),
                    'end_time' => date('Y-m-d H:i:s',$result[$i]['end_time']),
                    'money' => $result[$i]['money']
                );
                array_push($data['record'], $record_list);
            }
        }
        echo json_encode($data);
    }

    /**
     * 查询充值记录
     */
    public function recharge()
    {
        $recharge = A('Rechargerecord');
        $reslut = $recharge->getList(I('param.id'),I('param.page',0),I('param.num'),0);
        if (count($reslut) == 0)
            $data['code'] = 7;
        else
            $data['code'] = 0;
        $data['record'] = $reslut;
        echo json_encode($data);
    }

    /**
     * 增加充值记录
     */
    public function recharge_add()
    {
        $recharge = A('Rechargerecord');
        $reslut = $recharge->add(I('param.id'),I('param.money'),I('param.type'));
        if($reslut){
        	$json['code'] =0;
        	$json['msg']="正常返回";
        	$json['data']=array();
        }else{
        	$json['code'] =4;
        	$json['msg']="内部错误";
        	$json['data']=array();
        }
        echo json_encode($json);
    }

    /**
     * 忘记密码
     */
    public function forget()
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
            $UserModel = D('User');
            $id = $UserModel->where('phone="' . I('post.phone') . '"')->getField('id');
            if ($id) {
                $result = $User->forget($id, I('post.pwd'));
                switch ($result) {
                    case -1:
                        $data['code'] = 3;
                        break;
                    case -2:
                        $data['code'] = 4;
                        break;
                    default:
                        break;
                }
            } else {
                $data['code'] = 5;
            }
        }
        $data['msg'] = "正常返回";
        $data['data'] = array();
        echo json_encode($data);
    }

    /**
     * 获取拼接url
     * @param $arg 参数
     * @return string 拼接好的字符串
     */
    private function get_url($arg)
    {
        return C('IP') . __ROOT__ . $arg;
    }
}