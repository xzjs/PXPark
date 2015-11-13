<?php

namespace Home\Model;
use Think\Model\RelationModel;
/**
 * 支付模型
 * createtime:2015年10月29日 上午10:04:15
 * @author xiuge
 */
class PayModel extends RelationModel {
	protected $_validate = array(
			/* array('total_num','require','总车位数不能为空'),
			array('type','require','停车场种类不能为空'),
			array('address','require','地址不能为空'),
			array('name','require','停车场不能为空'),
			array('area_code','require','地址不能为空'),
			array('licence_image','require','许可证不能为空'),
			array('legal_person','require','法人不能为空'),
			array('legal_person_no','require','法人编号不能为空'), */
	);

}