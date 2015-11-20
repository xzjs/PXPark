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
    protected $_link = array(
        'Park'=>self::BELONGS_TO,
        'Car'=>self::BELONGS_TO,
    );
}