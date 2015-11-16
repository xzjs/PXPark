<?php
/**
 * Created by PhpStorm.
 * User: xzjs
 * Date: 15/11/16
 * Time: 下午1:22
 */
namespace Home\Model;
use Think\Model;
class ResponseModel extends Model{
    protected $_auto = array (
        array('is_read',0),
        array('time','time',3,'function'),
    );
}