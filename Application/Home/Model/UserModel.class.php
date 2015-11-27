<?php
/**
 * Created by PhpStorm.
 * User: xzjs
 * Date: 15/10/24
 * Time: 下午3:44
 */
namespace Home\Model;
use Think\Model\RelationModel;

/**
 * 用户模型
 * @package Home\Model
 */
class UserModel extends RelationModel {
    protected $_validate = array(
        array('phone','','手机号已经被注册！',0,'unique',1), // 在新增的时候验证name字段是否唯一
    		array('nickname','','昵称已经被注册！',0,'unique',1),
    		
    );

    protected $_auto = array (
        array('pwd','md5',3,'function') , // 对password字段在新增和编辑的时候使md5函数处理
    	array('remain','0'),//新增的时候设为0
    );

    protected $_link = array(
        'Car'=> self::MANY_TO_MANY,
    );
}