<?php
namespace Home\Model;
use Think\Model;
class MessageModel extends Model{
	protected $_validate = array(
			array('content','require','反馈内容不能为空'),
	);
	
	protected $_auto = array (
			array('time','time',1,'function'), // 新增的时候写入当前时间戳
	);
}