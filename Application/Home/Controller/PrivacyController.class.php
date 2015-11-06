<?php
/**
 * Created by PhpStorm.
 * User: xzjs
 * Date: 15/11/6
 * Time: 下午1:42
 */
namespace Home\Controller;

use Think\Controller;

/**
 * 使用条款控制器
 * @package Home\Controller
 */
class PrivacyController extends Controller
{
    /**
     * 获取最新的使用条款id
     * @return mixed
     */
    public function get_privacy(){
        $Privacy=D('Privacy');
        $data=$Privacy->order('time desc')->find();
        return $data['id'];
    }

    public function detail($id){
        $Privacy=D('Privacy');
        $data=$Privacy->find($id);
        echo $data['content'];
    }
}