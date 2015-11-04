<?php
namespace Home\Model;
use Think\Model;
class MessageModel extends Model{
	protected $_validate = array(
			array('user_id','require','用户id不能为空'),
			array('content','require','反馈内容不能为空'),
	);
}