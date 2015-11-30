<?php
/**
 * Created by PhpStorm.
 * User: xzjs
 * Date: 15/11/11
 * Time: 下午4:24
 */
namespace Home\Model;
use Think\Model\RelationModel;
class ParkrecordModel extends RelationModel {
	
	protected $_validate = array(
			array('park_id','require','停车场id名必须存在！'),
			array('car_id','require','车辆id名必须存在！'),
			array('berth_id','require','车位id名必须存在！'),
	);
	
	protected $_auto = array(
        array('end_time','time',3,'function'),
	);
	
	
    protected $_link = array(
        'Park'=>self::BELONGS_TO,
        'Car'=>self::BELONGS_TO,
    );
}