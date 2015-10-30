<?php
/**
 * Created by PhpStorm.
 * User: xzjs
 * Date: 15/10/30
 * Time: 下午2:59
 */
namespace Home\Model;
use Think\Model;

/**
 * 验证码模型
 * @package Home\Model
 */
class CaptchaModel extends Model {
    protected $_auto = array (
        array('time','time',1,'function'),
    );
}