<?php
namespace Home\Model;
use Think\Model\RelationModel;
class RuleModel extends RelationModel{
	protected $_validate = array(
			array('name','require','规则名必须存在！'), 
			array('park_id','require','停车场id必须存在！'),  
			array('ruletype_id','require','规则名必须存在！'), 
			array('start_time_day','require','停车规则id！'),  
			array('end_time_day','require','白天开始时间！'),  
			array('start_time_night','require','夜晚开始时间！'),  
			array('end_time_night','require','夜晚结束时间！'),  
			array('free_time_day','require','白天免费时间！'), 
			array('free_time_night','require','夜晚免费时间！'), 
			
			
	);

	protected $_link = array(
		'Ruletype'=> self::BELONGS_TO,
		'Ruletime'=>self::HAS_MANY,
	);
}