<?php
/**
 * Created by PhpStorm.
 * User: xzjs
 * Date: 15/11/16
 * Time: 下午1:17
 */
namespace Home\Controller;

use Think\Controller;

class ResponseController extends Controller{

    /**
     * 添加
     * @return int|mixed 添加后的主键或者0
     */
    public function add(){
        $Response=D('Response');
        if($Response->create()){
            $result = $Response->add(); // 写入数据到数据库 
            if($result){
                return $result;
            }
        }
        return 0;
    }
}