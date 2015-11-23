<?php
/**
 * Created by PhpStorm.
 * User: syx
 * Date: 15/10/26
 * Time: 下午10:44
 */
namespace Home\Model;
use Think\Model\RelationModel;

/**
 * 车辆模型
 * @author sun
 *
 */
class CarModel extends RelationModel{
     protected $_validate = array(
        array('no','','车辆已经被注册！',0,'unique',1), // 在新增的时候验证no字段是否唯一
     	//array('id','','id不存在！',0,'unique',2), // 编辑数据时候验证id是否存在
    );
    protected $_link = array(
        'User' => array(
            'mapping_type'      =>  self::MANY_TO_MANY,
            'relation_table'    =>  '__USER_CAR__' //此处应显式定义中间表名称，且不能使用C函数读取表前缀
        ),
    );
}
?>