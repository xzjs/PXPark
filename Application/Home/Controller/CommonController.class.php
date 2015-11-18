<?php
/**
 * Created by PhpStorm.
 * User: xzjs
 * Date: 15/11/2
 * Time: 下午10:26
 */
namespace Home\Controller;

use Think\Controller;
use Think\Model;

/**
 * 普通管理员管理员控制器
 * @package Home\Controller
 */
class CommonController extends Controller{
	public function index() {
		$user_id=1;
		$Model = new Model ();
		$last_month=strtotime("last Month")+mktime(0,0,0,date("m"),date("d")+1,date("Y"))-time();
		//var_dump(date("Y-m-d h:i:sa",$last_month));
		$sql_small = "SELECT FROM_UNIXTIME(a.start_time,'%m-%d') atime, COUNT(*) cnt,SUM(a.money) money FROM px_parkrecord AS a,px_user AS b,px_car AS c,px_park AS d 
			WHERE c.type=1 AND b.id=".$user_id." AND b.id=d.user_id AND d.id=a.park_id AND a.car_id=c.id AND a.start_time>".$last_month." GROUP  BY FROM_UNIXTIME(a.start_time,'%m-%d')";
		$result_small = $Model->query ( $sql_small );
		
		$sql_big = "SELECT FROM_UNIXTIME(a.start_time,'%m-%d') atime, COUNT(*) cnt,SUM(a.money) money FROM px_parkrecord AS a,px_user AS b,px_car AS c,px_park AS d
			WHERE c.type=2 AND b.id=".$user_id." AND b.id=d.user_id AND d.id=a.park_id AND a.car_id=c.id AND a.start_time>".$last_month." GROUP  BY FROM_UNIXTIME(a.start_time,'%m-%d') ORDER BY
			FROM_UNIXTIME(a.start_time,'%m-%d') DESC LIMIT 0,30";
		$result_big = $Model->query ( $sql_big );
		
		for($i=0;$i<30;$i++){
			$date=date("m-d", strtotime("-".(29-$i)." Days"));
			$colum_chart[$i]['date']=$date;
			for($j=0;$j<count($result_small);$j++){
				if($result_small[$j]['atime']==$date){
					$colum_chart[$i]['small']=$result_small[$j]['cnt'];
					$colum_chart[$i]['money']=$result_small[$j]['money'];
				}
			}
			for($j=0;$j<count($result_big);$j++){
				if($result_big[$j]['atime']==$date){
					$colum_chart[$i]['big']=$result_big[$j]['cnt'];
					$colum_chart[$i]['money']+=$result_big[$j]['money'];
				}
			}
		}
		//var_dump($colum_chart);
		
		$sql_pie = "SELECT COUNT(*) cnt, c.type,SUM(a.money) money FROM px_parkrecord AS a,px_user AS b,px_car AS c,px_park AS d WHERE b.id=".$user_id." AND b.id=d.user_id AND d.id=a.park_id AND a.car_id=c.id AND a.start_time>".$last_month." GROUP BY c.type,a.money";
		$result_pie = $Model->query ( $sql_pie );
		//var_dump($result_pie);
		$pie=array("small"=>0,"small_free"=>0,"big"=>0,"big_free"=>0);
		for($i=0;$i<count($result_pie);$i++){
			if(($result_pie[$i]['money']!=0)&&($result_pie[$i]['type']==1))
				$pie['small']+=$result_pie[$i]['cnt'];
			elseif (($result_pie[$i]['money']==0)&&($result_pie[$i]['type']==1))
				$pie['small_free']+=$result_pie[$i]['cnt'];
			elseif (($result_pie[$i]['money']!=0)&&($result_pie[$i]['type']==2))
				$pie['big']+=$result_pie[$i]['cnt'];
			elseif (($result_pie[$i]['money']==0)&&($result_pie[$i]['type']==2))
				$pie['big_free']+=$result_pie[$i]['cnt'];
		}
		//var_dump($pie);
		
		$last_day=strtotime("-1 Day")+mktime(date("H")+1,0,0,date("m"),date("d"),date("Y"))-time();
		//var_dump(date("Y-m-d h:i:sa",$last_day));
		$sql_line="SELECT FROM_UNIXTIME(a.start_time,'%H') atime, COUNT(*) cnt FROM px_parkrecord AS a,px_user AS b,px_car AS c,px_park AS d 
			WHERE b.id=".$user_id." AND b.id=d.user_id AND d.id=a.park_id AND a.car_id=c.id AND a.start_time>".$last_day." GROUP  BY FROM_UNIXTIME(a.start_time,'%H')";
		$result_line = $Model->query ( $sql_line );
		$sql_line="SELECT FROM_UNIXTIME(a.end_time,'%H') atime, COUNT(*) cnt FROM px_parkrecord AS a,px_user AS b,px_car AS c,px_park AS d
			WHERE b.id=".$user_id." AND b.id=d.user_id AND d.id=a.park_id AND a.car_id=c.id AND a.end_time>".$last_day." GROUP  BY FROM_UNIXTIME(a.end_time,'%H')";
		$result_line_end = $Model->query ( $sql_line );
		//var_dump($result_line);
		for($i=0;$i<24;$i++){
			$time=date("H", strtotime("-".(23-$i)." Hours"));
			$line_chart[$i]['time']=$time;
			for($j=0;$j<count($result_line);$j++){
				if($result_line[$j]['atime']==$time){
					$line_chart[$i]['cnt']=$result_line[$j]['cnt'];
				}
			}
			for($j=0;$j<count($result_line_end);$j++){
				if($result_line_end[$j]['atime']==$time){
					$line_chart[$i]['cnt_end']=$result_line_end[$j]['cnt'];
				}
			}
		}
		//var_dump($line_chart);
		$this->assign ( 'columInfo', json_encode ( $colum_chart ) );
		$this->assign ( 'pieInfo', json_encode ( $pie ) );
		$this->assign ( 'lineInfo', json_encode ( $line_chart ) );
		$this->display ();
		
	}

	public function park_analyse(){
		$user_id=1;
		$Model = new Model ();
		$last_month=strtotime("last Month")+mktime(0,0,0,date("m"),date("d")+1,date("Y"))-time();
		//var_dump(date("Y-m-d h:i:sa",$last_day));
		$sql_line="SELECT FROM_UNIXTIME(a.start_time,'%m-%d') atime, COUNT(*) cnt,SUM(a.money) money FROM px_parkrecord AS a,px_user AS b,px_car AS c,px_park AS d 
			WHERE b.id=".$user_id." AND b.id=d.user_id AND d.id=a.park_id AND a.car_id=c.id AND a.start_time>".$last_month." GROUP  BY FROM_UNIXTIME(a.start_time,'%m-%d')";
		$result_line = $Model->query ( $sql_line );
		$sql_line="SELECT FROM_UNIXTIME(a.end_time,'%m-%d') atime, COUNT(*) cnt FROM px_parkrecord AS a,px_user AS b,px_car AS c,px_park AS d
			WHERE b.id=".$user_id." AND b.id=d.user_id AND d.id=a.park_id AND a.car_id=c.id AND a.end_time>".$last_month." GROUP  BY FROM_UNIXTIME(a.end_time,'%m-%d')";
		$result_line_end = $Model->query ( $sql_line );
		//var_dump($result_line);
		//var_dump($result_line_end);
		for($i=0;$i<30;$i++){
			$date=date("m-d", strtotime("-".(29-$i)." Days"));
			$line_chart[$i]['date']=$date;
			for($j=0;$j<count($result_line);$j++){
				if($result_line[$j]['atime']==$date){
					$line_chart[$i]['cnt']=$result_line[$j]['cnt'];
					$line_chart[$i]['money']=$result_line[$j]['money'];
				}
			}
			for($j=0;$j<count($result_line_end);$j++){
				if($result_line_end[$j]['atime']==$date){
					$line_chart[$i]['cnt_end']=$result_line_end[$j]['cnt'];
				}
			}
		}
		//var_dump($line_chart);
		$this->assign ( 'columInfo', json_encode ( $colum_chart ) );
		//$this->display ();
	}
	
	/**
	 * 获取用户信息
	 */
	public function user_info() {
		$user = A( 'User' );
		$result=$user->detail(I('param.id',0));
		if($result==-1)
			$this->display('user_register');
		else{
			$this->assign('data',$result);
			$this->display('user_update');
		}
	}
	
	/**
	 * 停车场管理员用户注册
	 */
	public function web_register() {
		$user=A('User');
		$x=I('param.cardfile');
		$result=$user->web_register(I('param.username'),I('param.password'),I('param.factname'),I('param.cardNo'),I('param.phone'),I('param.message'));
	}
	
	/**
	 * 支付账户信息
	 */
	public function pay_info() {
		$Pay=A('Pay');
		//$user_id=$_SESSION['user']['user_id'];//$_SESSION('user')['id'];
		$user_id=1;
		if($user_id){
			$result=$Pay->pay_info($user_id);
			$this->assign('data',$result);
			$this->display();
		}
	}
	
	/**
	 * 新增或者更新支付账户信息
	 */
	public function add_pay() {  
		$Pay = D ( 'Pay' );
		if ($Pay->create ()) {
			//$user_id=$_SESSION['user']['user_id'];
			$user_id=1;
			$Pay->user_id=$user_id;
			$pay=M('Pay');
			if(!$pay->where('user_id='.$user_id)->find()){
				$result = $Pay->add ();
				if ($result) {
					echo "<script>window.alert(\"添加成功！\"),location.href='".U('Common/pay_info?id='.$result)."';</script>";//添加成功
				} else {
					$this->error ( '数据添加错误！' );
				}
			}else{
				$result=$Pay->where('user_id='.$user_id)->save();
				if ($result||$result==0) {
					echo "<script>window.alert(\"修改成功！\"),location.href='".U('Common/pay_info?id='.$result)."';</script>";//添加成功
				} else {
					$this->error ( '数据添加错误！' );
				}
			}
		} else {
			$this->error ( $Pay->getError () );
		}
	}
	
	public function add_message() {
		$Message = D ( 'Message' );
		if ($Message->create ()) {
			//$user_id=$_SESSION['user']['user_id'];
			$user_id=1;
			$Message->user_id=$user_id;
			$result=$Message->add();
			if ($result) {
				echo "<script>window.alert(\"添加成功！\"),location.href='".U('Common/feedback')."';</script>";//添加成功
			} else {
				$this->error ( '数据添加错误！' );
			}
		} else {
			$this->error ( $Message->getError ());
		}
	}

	public function rule_add(){
		$Rule=A('Rule');
		$rule_result=$Rule->add();
		if($rule_result){
			$day_small_start_times=I('post.day_small_start_time[]');
		}
	}
		
}