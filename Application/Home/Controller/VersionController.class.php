<?php
/**
 * Created by PhpStorm.
 * User: xzjs
 * Date: 15/11/17
 * Time: 上午10:09
 */
namespace Home\Controller;

use Think\Controller;

/**
 * APP版本控制器
 * @package Home\Controller
 */
class VersionController extends Controller
{
    /**
     * 获取版本
     * @param $type 系统类型
     * @return mixed 查询结果
     */
    public function get_version($type){
        $Version=M('Version');
        return $Version->where('type='.$type)->order('time desc')->find();
    }
}