<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
       $this->display();
    }
    
    /**
     * 用户登录
     */
   public function login(){
 
   	$user=A('User');
   	$type=I('param.type');
   	$name=I('param.name');
   	$pwd=I('param.pswd');
   //echo  $pwd;
  	$flag=$user->login_byName($name,$pwd,$type);
	echo  $flag;
   	
   }
}