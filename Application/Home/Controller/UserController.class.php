<?php

/**
 * Created by PhpStorm.
 * User: xzjs
 * Date: 15/10/24
 * Time: 下午3:33
 */
namespace Home\Controller;

use Think\Controller;
use Org\MyClass\RandName;

/**
 * 用户控制器
 *
 * @package Home\Controller
 */
class UserController extends Controller
{
    /**
     * @param $nickname 用户名
     * @param $pwd 密码
     * @return int|mixed -1:用户名未注册;-2:密码错误;登录成功返回一个用户数组
     */
    function web_login()
    {
    	
        $nickname = I('param.nickname');
        $pwd = I('param.pwd');
		//echo $nickname;
       $condition['nickname'] = $nickname;
        //$condition['type'] = $type;
        $User = D('User');
        $data = $User->where($condition)->find();
        if ($data) {
            $pwd = md5($pwd);
            if ($data ['pwd'] == $pwd) {
                $array = array(
                    "id" => $data ['id'],
                    "nickname" => $data ['nickname'],
                    "type" => $data ['type'],
                ); 
                
            	 session_start();                // 首先开启session
                     // 直接输出 username 
                $_SESSION['id']= $data ['id'];
                $_SESSION['nickname']= $data ['nickname']; 
                $_SESSION['type']= $data ['type']; 
                $this->assign('Info', json_encode($array));
                echo $array;
                if ($data ['type'] == 2 || $data ['type'] == 3) {
                    $this->redirect("../index.php/Home/Common/index.html");
                }
                if ($data ['type'] == 4) {
                    $this->redirect("../index.php/Home/Super/index.html");
                }
                if ($data ['type'] == 1) {
                	$this->error('该用户不允许登陆');
                }
                echo $array;

            } else {
                $this->error('密码错误');
            }
        } else {
            $this->error('用户名未注册');
        } 
    }

    /**
     * 扣除账户余额
     */
    function cost($uid, $money)
    {
        //$uid = I('param.user_id');
        //$money = I('param.money');
        $check = M()->query("select my_money from px_user where id=$uid");
        $real_monye = $check [0] ['my_money'];
        if ($money > $real_monye)
            echo false;
        else {
            $result = M()->execute("update px_user set  my_money=$real_monye-$money where  id=$uid");
            if ($result == 0) {
                //throw new Exception ( "wrong" );
                echo false;
            } else
                echo true;
        }
    }

    /**
     * 获取单个用户停车历史记录给前端用
     */
    function getparkrecord($uid)
    {
        $result = M()->query("SELECT  px_car.no, px_parkrecord.start_time,px_parkrecord.end_time,px_parkrecord.money FROM px_user_car ,px_car,px_parkrecord WHERE px_user_car.user_id=$uid AND px_user_car.car_id=px_car.id AND px_car.id=px_parkrecord.car_id AND px_user_car.status=1");
        $arry = array();

        for ($i = 0; $i < count($result); $i++) {
            $arry [$i] = array(
                'car_no' => $result [$i] ['no'],
                'stime' => date('Y-m-d h:m:s ', $result [$i] ['start_time']),
                'etime' => date('Y-m-d h:m:s ', $result [$i] ['end_time']),
                'money' => $result [$i] ['money']
            );
        }

        return json_encode($arry);
    }

    /**
     * 获取所有普通用户信息
     */
    function persons_info()
    {
        $result = M()->query("SELECT id,nickname,phone,NAME,card_no,member_id,score FROM px_user WHERE TYPE=1");
        $arry = array();

        for ($i = 0; $i < count($result); $i++) {
            $arry [$i] = array(
                'uid' => $result [$i] ['id'],
                'nick' => $result [$i] ['nickname'],
                'telphone' => $result [$i] ['phone'],
                'name' => $result [$i] ['name'],
                'cardId' => $result [$i] ['card_no'],
                'memeberLevel' => '',
                'creditLevel' => '',
                'points' => $result [$i] ['score']
            )// 'parkingHistory'=>'ffd',

            ;
        }

        return json_encode($arry);
    }

    /**
     * 获取用户一个月内消费
     */
    public function getSpend($uid)
    {
        $result = M()->query("SELECT SUM(px_parkrecord.money) AS consum  FROM px_parkrecord,px_user_car WHERE px_user_car.user_id=$uid  AND px_user_car.status=1 AND px_user_car.car_id=px_parkrecord.car_id   AND px_parkrecord.end_time>( UNIX_TIMESTAMP(NOW())-25920000)");
        return $result;
        // echo "ff0".$result[0]['consum'];
    }

    /**
     * 通过网页注册用户
     */
    public function web_register($username, $password, $factname, $cardNo, $phone, $message)
    {
        $Captcha = A('Captcha');
        $data ['code'] = 0;
        $code_status = $Captcha->verify($phone, $message);
        switch ($code_status) {
            case 1 :
                $data ['code'] = 1;
                break;
            case 2 :
                $data ['code'] = 2;
                break;
            case 3 :
                $data ['code'] = 5;
                break;
            default :
                break;
        }
        if ($data ['code'] == 0) {

            $validate_rules = array(

                array(
                    'phone',
                    '',
                    '手机号已经被注册！',
                    0,
                    'unique',
                    1
                ),
                array(
                    'nickname',
                    'require',
                    '用户名必须！'
                ),
                array(
                    'pwd',
                    'require',
                    '密码必须！'
                ),
                array(
                    'name',
                    'require',
                    '真实姓名必须！'
                ),
                array(
                    'card_no',
                    'require',
                    '身份证号必须！'
                ),
                array(
                    'phone',
                    'require',
                    '手机号必须！'
                )
            ); // 动态验证规则

            $auto_rules = array(
                array(
                    'pwd',
                    'md5',
                    3,
                    'function'
                ), // 对password字段在新增和编辑的时候使md5函数处理
                array(
                    'remain',
                    '0'
                )
            ) // 新增的时候设为0
            ; // 动态生成规则
            $User = D('User');
            $user = array(
                "nickname" => $username,
                "pwd" => $password,
                "name" => $factname,
                "card_no" => $cardNo,
                "phone" => $phone
            ); // 实例化User对象

            if ($User->validate($validate_rules)->auto($auto_rules)->create($user)) {
                $upload = new \Think\Upload (); // 实例化上传类
                $upload->maxSize = 3145728; // 设置附件上传大小
                $upload->exts = array(
                    'jpg',
                    'gif',
                    'png',
                    'jpeg'
                ); // 设置附件上传类型
                $upload->rootPath = '.' . C('USERICON_PATH'); // 设置附件上传根目录
                $upload->autoSub = false;
                $upload->saveName = time() . '_' . mt_rand(); // 上传文件
                $info = $upload->upload();
                if (!$info) {
                    $result = null;
                } else {
                    $User->card_img = $info ['cardfile'] ['savename'];
                    $result = $User->add();
                }
                if ($result) {
                    echo "<script>window.alert(\"注册成功！\"),location.href='" . U('Common/user_info?id=' . $result) . "';</script>"; // 添加成功
                } else {
                    echo '数据添加错误！'; // 添加 失败
                }
            } else {
                $this->error($User->getError()); // 自动验证失败
            }
        } else {
            echo "验证码验证失败";
        }
    }

    /**
     * 用户注册
     *
     * @param $phone 手机号
     * @param $nickname 昵称
     * @param $pwd 密码
     * @return int -1:电话号码已存在;-2:内部错误;正数:返回的插入id
     */
    public function register($phone, $nickname, $pwd, $type = 1)
    {
        $User = D("User");
        $data = array(
            'phone' => $phone,
            'nickname' => $nickname,
            'pwd' => $pwd,
            'type' => $type
        );
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
     *
     * @param $name 用户名
     * @param $pwd 密码
     * @return int|mixed -1:用户名未注册;-2:密码错误;正数:用户id
     */
    public function login_byName($name, $pwd, $type)
    {
        $condition['nickname'] = $name;
        //$condition['type'] = $type;
        $User = D('User');
        $data = $User->where($condition)->find();
        if ($data) {
            $pwd = md5($pwd);
            if ($data ['pwd'] == $pwd) {
                return $data ['id'];
            } else {
                return -2;
            }
        } else {
            return -1;
        }
    }

    /**
     * 用户登录
     *
     * @param $phone 手机号
     * @param $pwd 密码
     * @return int|mixed -1:手机号未注册;-2:密码错误;正数:用户id
     */
    public function login($phone, $pwd)
    {
        $User = D('User');
        $data = $User->where('phone="' . $phone . '"')->find();
        if ($data) {
            $pwd = md5($pwd);
            if ($data ['pwd'] == $pwd) {
                return $data ['id'];
            } else {
                return -2;
            }
        } else {
            return -1;
        }
    }

    /**
     * 修改密码
     *
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

    /**
     * 查询用户的详细信息
     *
     * @param $id 用户id
     * @return int|mixed -1:用户id不存在;数组:用户详细信息数组
     */
    public function detail($id)
    {
        $User = D('User');
        $data = $User->find($id);
        if ($data) {
            return $data;
        } else {
            return -1;
        }
    }

    /**
     * 用户忘记密码
     *
     * @param $id 用户id
     * @param $pwd 修改后的pwd
     * @return int 0:正常
     */
    public function forget($id, $pwd)
    {
        $User = D('User');
        $User->where('id=' . $id)->setField('pwd', md5($pwd));
        return 0;
    }

    public function logout()
    { $a=$_SESSION['id'];
      $b=$_SESSION['nickname']; 
      $c=$_SESSION['type'];
      unset($a);
      unset($b);
      unset($c);
      
        $this->success('注销成功','../Index/index.html');
    	
    }

    /**
     * 随机生成一个用户
     * @param int $type 用户类别
     * @return mixed 返回的用户的数组
     */
    public function get_list($type = 0)
    {
    	/* $name=new RandName(); 
    	$name->RandName();
    	$user_name=$name->getName(2);
    	$nick_name=$name->getNickname();
    	$phone="1".rand(0,9999999999);
    	$user=array("phone"=>$phone,"nickname"=>$nick_name,"name"=>$user_name,"member_id"=>rand(1,3),"my_money"=>rand(0,200),
    			"pwd"=>$pwd = rand(100000,999999),"car_no"=>rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).
    			rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9).rand(0,9),"type"=>1);
    	$User=D('User');
    	if($User->create($user)){
    		$id=$User->add();
    	}
    	$user['id']=$id;
		return $user; */
        $condition = array();
        switch ($type) {
            case 1:
            case 2:
            case 3:
            case 4:
                $condition['type'] = $type;
                break;
            default:
                break;
        }
        $UserModel = D('User');
        return $UserModel->where($condition)->relation(true)->select();
    }
    
    
    
}