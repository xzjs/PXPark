<?php

namespace Home\Model;

use Think\Model\RelationModel;

/**
 * 停车场模型
 * createtime:2015年10月29日 上午10:04:15
 * @author xiuge
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