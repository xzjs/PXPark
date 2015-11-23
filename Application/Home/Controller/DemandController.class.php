<?php 

/**
 *  Created by PhpStorm.
 * User: syx
 * Date: 15/11/23
 * Time: 10:26am
 */
 namespace  Home\Controller;
 
use think\Controller;
use Think\Model;

class  DemandController extends  Controller
{
	function add(){
		$lon=I('param.lon');
		$lat=I('param.lat');
		$pid=I('param.park_id');
		$result=M()->execute("
				INSERT INTO  px_demand 
				(lon,lat,TIME,park_id)VALUES 
				($lon,$lat,UNIX_TIMESTAMP(NOW()) ,$pid)
				");
		
		echo $result;
			
			
	}
}
