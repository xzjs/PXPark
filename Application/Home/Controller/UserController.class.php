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
	
	public function web_register() {
		
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
		$validate_rules = array(
				array('phone','','手机号已经被注册！',0,'unique',1),
				array('nickname','require','用户名必须！'),
				array('pwd','require','密码必须！'),
				array('name','require','真实姓名必须！'),
				array('card_img','require','省份证照片必须！'),
				array('card_no','require','身份证号必须！'),
				array('phone','require','手机号必须！'),
				array('captcha','require','验证码必须！'),
		);//动态验证规则
		
		$auto_rules = array (
				array('pwd','md5',3,'function') , // 对password字段在新增和编辑的时候使md5函数处理
		);//动态生成规则
		
		$User = M("User"); // 实例化User对象
		if ($User->validate($validate_rules)->auto($auto_rules)->create()){
			$result=$User->add();
		if ($result) {
				echo "数据添加成功";//添加成功
			} else {
				echo '数据添加错误！';//添加失败
			}
		} else {
			echo "验证失败";//自动验证失败
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
}