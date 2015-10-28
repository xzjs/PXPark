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
 * 充值记录模型
 * createtime:2015年10月26日 下午4:15:04
 * @author xiuge
 */
class RechargerecordModel extends Model {
	
	protected $_validate = array(
			array('user_id','require','用户id必须'),
	);
 
	protected $_auto = array (
			array('time','time',1,'function'), // 更新的时候写入当前时间戳
	);
	
	
}