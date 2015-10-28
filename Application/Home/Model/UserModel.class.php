<?php
/**
 * Created by PhpStorm.
 * User: xzjs
 * Date: 15/10/24
 * Time: 下午3:44
 */
namespace Home\Model;
use Think\Model;

/**
 * 用户模型
 * Class UserModel
 * @package Home\Model
 */
class UserModel extends Model {
    protected $_validate = array(
        array('phone','','手机号已经被注册！',0,'unique',1), // 在新增的时候验证name字段是否唯一
    );

    protected $_auto = array (
        array('pwd','md5',3,'function') , // 对password字段在新增和编辑的时候使md5函数处理
    );
}