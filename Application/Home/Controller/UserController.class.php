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
	 * 获取用户一个月内消费
	 */
	public function getSpend($uid){
		$result=M()->query("SELECT SUM(px_parkrecord.money) AS consum  FROM px_parkrecord,px_user_car WHERE px_user_car.user_id=$uid  AND px_user_car.status=1 AND px_user_car.car_id=px_parkrecord.car_id   AND px_parkrecord.end_time>( UNIX_TIMESTAMP(NOW())-25920000)");
		return  $result;
//echo "ff0".$result[0]['consum'];
	}

	/**
	 * 
	 *通过网页注册用户
	 */
	public function web_register($username,$password,$factname,$cardNo,$phone,$message) {
		$Captcha = A('Captcha');
		$data['code'] = 0;
		$code_status = $Captcha->verify($phone,$message);
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
			
		$validate_rules = array(
				
				array('phone','','手机号已经被注册！',0,'unique',1),
				array('nickname','require','用户名必须！'),
				array('pwd','require','密码必须！'),
				array('name','require','真实姓名必须！'),
				array('card_no','require','身份证号必须！'),
				array('phone','require','手机号必须！'),
		);//动态验证规则
		
		$auto_rules = array (
				array('pwd','md5',3,'function') , // 对password字段在新增和编辑的时候使md5函数处理
				array('remain','0'),//新增的时候设为0
		);//动态生成规则
		$User=D('User');
		$user = array("nickname"=>$username,"pwd"=>$password,"name"=>$factname,"card_no"=>$cardNo,"phone"=>$phone); // 实例化User对象 
		
		if ($User->validate($validate_rules)->auto($auto_rules)->create($user)){
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize = 3145728;// 设置附件上传大小
			$upload->exts = array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
			$upload->rootPath = '.'.C('USERICON_PATH'); // 设置附件上传根目录
			$upload->autoSub = false;
			$upload->saveName = time() . '_' . mt_rand();// 上传文件
			$info = $upload->upload();
			if (!$info) {
				$result=null;
			} else {
				$User->card_img = $info['cardfile']['savename'];
				$result=$User->add();
			}
		if ($result) {
				echo "<script>window.alert(\"注册成功！\"),location.href='".U('Common/user_info?id='.$result)."';</script>";//添加成功
			} else {
				echo '数据添加错误！';//添加 失败
			}
		} else {
			$this->error($User->getError());//自动验证失败
		}
	}else{
		echo "验证码验证失败";
	}
	}

	/**
	 * 用户注册
	 * @param $phone 手机号
	 * @param $nickname 昵称
	 * @param $pwd 密码
	 * @return int -1:电话号码已存在;-2:内部错误;正数:返回的插入id
	 */
    public function register($phone,$nickname,$pwd,$type=1){
        $User=D("User");
		$data=array(
			'phone'=>$phone,
			'nickname'=>$nickname,
			'pwd'=>$pwd,
			'type'=>$type
		);
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

	/**
	 * 用户忘记密码
	 * @param $id 用户id
	 * @param $pwd 修改后的pwd
	 * @return int 0:正常
	 */
    public function forget($id,$pwd) {
		$User=D('User');
		$User->where('id='.$id)->setField('pwd',md5($pwd));
		return 0;
    }

	public function logout(){

	}
}