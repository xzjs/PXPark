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
class TargetModel extends RelationModel {
    protected $_validate = array(
    		array('park_id','require','停车场id不能为空'),
    		array('num','require','停车指数不能为空'),
    );

    protected $_auto = array (
    		array('time','time',3,'function'), // 更新的时候写入当前时间戳
    );

}