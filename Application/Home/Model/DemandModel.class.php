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
class DemandModel extends RelationModel
{

    /**
     * @var array 自动完成数组
     */
    protected $_auto = array(
        array('time', 'time', 3, 'function'), // 对update_time字段在更新的时候写入当前时间戳
    );

    /**
     * @var array 关联模型
     */
    protected $_link = array(
        'User'=>self::BELONGS_TO,
        'Park'=>self::BELONGS_TO,
    );
}

?>