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
 * 普通管理员控制器
 * createtime:2015年11月19日 下午4:24:17
 * @author xiuge
 */
class CommonController extends BaseController{

	/**
	 * 获取首页图标信息
	 */
	public function index() {
		session_start();
		$_SESSION['user']['user_id']=9;
		$_SESSION['park_id']=20;
		if(I('param.park_id',0)!=0){
			$condition=' where a.park_id='.I('param.park_id') ;
		}elseif(I('param.user_id',0)!=0){
			$user_id=I('param.user_id',0);
			$condition=',px_park AS d WHERE a.park_id=d.id AND d.user_id='.$user_id ;
		}elseif (isset($_SESSION['park_id'])){
			$condition=' where a.park_id='.$_SESSION['park_id'] ;
		}elseif(isset($_SESSION['user']['user_id'])){
			$user_id=$_SESSION['user']['user_id'];
			$condition=',px_park AS d WHERE a.park_id=d.id AND d.user_id='.$user_id ;
		}
		
		$Model = new Model ();
		$last_month=strtotime("last Month")+mktime(0,0,0,date("m"),date("d")+1,date("Y"))-time();//30天前的零点
		$sql_small = "SELECT FROM_UNIXTIME(a.start_time,'%m-%d') atime, COUNT(*) cnt,SUM(a.money) money FROM px_parkrecord AS a,px_car AS c "
				.$condition." AND c.type=1 AND a.car_id=c.id AND a.start_time>".$last_month." GROUP  BY FROM_UNIXTIME(a.start_time,'%m-%d')";
		$result_small = $Model->query ( $sql_small );
		
		$sql_big = "SELECT FROM_UNIXTIME(a.start_time,'%m-%d') atime, COUNT(*) cnt,SUM(a.money) money FROM px_parkrecord AS a,px_car AS c "
				.$condition." AND c.type=2 AND a.car_id=c.id AND a.start_time>".$last_month." GROUP  BY FROM_UNIXTIME(a.start_time,'%m-%d')";
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
		
		$sql_pie = "SELECT COUNT(*) cnt, c.type,SUM(a.money) money FROM px_parkrecord AS a,px_car AS c ".$condition." and a.car_id=c.id AND a.start_time>".$last_month." GROUP BY c.type,a.money";
		$result_pie = $Model->query ( $sql_pie );
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
		
		$last_day=strtotime("-1 Day")+mktime(date("H")+1,0,0,date("m"),date("d"),date("Y"))-time();//24小时前的零点
		$sql_line="SELECT FROM_UNIXTIME(a.start_time,'%H') atime, COUNT(*) cnt FROM px_parkrecord AS a,px_car AS c ".$condition." AND a.car_id=c.id AND a.start_time>".$last_day." GROUP  BY FROM_UNIXTIME(a.start_time,'%H')";
		$result_line = $Model->query ( $sql_line );
		$sql_line="SELECT FROM_UNIXTIME(a.end_time,'%H') atime, COUNT(*) cnt FROM px_parkrecord AS a,px_car AS c ".$condition." AND a.car_id=c.id AND a.end_time>".$last_day." GROUP  BY FROM_UNIXTIME(a.end_time,'%H')";
		$result_line_end = $Model->query ( $sql_line );
		for($i=0;$i<24;$i++){
			$time=date("H", strtotime("-".(23-$i)." Hours"));//24小时前的天前的零分零秒
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
		
		$this->assign ( 'columInfo', json_encode ( $colum_chart ) );
		$this->assign ( 'pieInfo', json_encode ( $pie ) );
		$this->assign ( 'lineInfo', json_encode ( $line_chart ) );
		$this->display ();
	}

	/**
	 * 停车场数据分析
	 */
	public function park_analyse(){
		
		if(I('param.park_id',0)!=0){
			$condition=' where a.park_id='.I('param.park_id') ;
		}elseif(I('param.user_id',0)!=0){
			$user_id=I('param.user_id',0);
			$condition=',px_park AS d WHERE a.park_id=d.id AND d.user_id='.$user_id ;
		}elseif (isset($_SESSION['park_id'])){
			$condition=' where a.park_id='.$_SESSION['park_id'] ;
		}elseif(isset($_SESSION['user']['user_id'])){
			$user_id=$_SESSION['user']['user_id'];
			$condition=',px_park AS d WHERE a.park_id=d.id AND d.user_id='.$user_id ;
		}
		$Model = new Model ();
		$last_month=strtotime("last Month")+mktime(0,0,0,date("m"),date("d")+1,date("Y"))-time();
		$sql_line="SELECT FROM_UNIXTIME(a.start_time,'%m-%d') atime, COUNT(*) cnt,SUM(a.money) money FROM px_parkrecord AS a,px_car AS c".$condition." AND a.car_id=c.id AND a.start_time>".$last_month." GROUP  BY FROM_UNIXTIME(a.start_time,'%m-%d')";
		$result_line = $Model->query ( $sql_line );
		$sql_line="SELECT FROM_UNIXTIME(a.end_time,'%m-%d') atime, COUNT(*) cnt FROM px_parkrecord AS a,px_car AS c ".$condition." AND a.car_id=c.id AND a.end_time>".$last_month." GROUP  BY FROM_UNIXTIME(a.end_time,'%m-%d')";
		$result_line_end = $Model->query ( $sql_line );
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
		$this->assign ( 'lineInfo', json_encode ( $line_chart ) );
		$this->display ("park_analyse");
	}
	
	/**
	 * 获取用户信息
	 */
	public function user_info() {
		if(I('param.user_id',0)==0)
			$user_id=$_SESSION['user']['user_id'];
		else
			$user_id=I('param.user_id',0);
		$user = A( 'User' );
		$result=$user->detail($user_id);
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
		$result=$user->web_register(I('param.username'),I('param.password'),I('param.factname'),I('param.cardNo'),I('param.phone'),I('param.message'));
	}
	
	/**
	 * 支付账户信息
	 */
	public function pay_info() {
		$Pay=A('Pay');
		if(I('param.user_id',0)==0)
			$user_id=$_SESSION['user']['user_id'];
		else
			$user_id=I('param.user_id',0);
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
			if(I('param.user_id',0)==0)
				$user_id=$_SESSION['user']['user_id'];
			else
				$user_id=I('param.user_id',0);
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
	
	/**
	 * 新增用户反馈信息
	 */
	public function add_message() {
		$Message = D ( 'Message' );
		if ($Message->create ()) {
			if(I('param.user_id',0)==0)
				$user_id=$_SESSION['user']['user_id'];
			else
				$user_id=I('param.user_id',0);
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
        $day_small_start_times=I('post.day_small_start_time[]');
		
	}
	
	/**
	 * ajax获取停车场信息和收益状况
	 */
	public function get_park_info() {
		
		$Model = new Model ();
		
		
		if(I('param.park_id',0)!=0){
			$condition=' where px_parkrecord.park_id='.I('param.park_id');
		}elseif(I('param.user_id',0)!=0){
			$user_id=I('param.user_id',0);
			$condition=',px_park where px_parkrecord.park_id=px_park.id and px_park.user_id='.$user_id ;
		}elseif (isset($_SESSION['park_id'])){
			$condition=' where px_parkrecord.park_id='.$_SESSION['park_id'] ;
		}elseif(isset($_SESSION['user']['user_id'])){
			$user_id=$_SESSION['user']['user_id'];
			$condition=',px_park where px_parkrecord.park_id=px_park.id and px_park.user_id='.$user_id ;
		}
		
		$condition_today=$condition." and px_parkrecord.end_time>".strtotime("today");
		$condition_tomonth=$condition." and px_parkrecord.end_time>".mktime(0,0,0,date('m'),1,date('Y'));
		$condition_toyear=$condition." and px_parkrecord.end_time>".mktime(0,0,0,1,1,date('Y'));
		$sql_income="select px_car.type,SUM(px_parkrecord.money) money from px_parkrecord,px_car".$condition_today." and px_car.id=px_parkrecord.car_id GROUP BY px_car.type order by px_car.type asc";
		$result_income[0] = $Model->query ( $sql_income );
		$sql_income="select px_car.type,SUM(px_parkrecord.money) money from px_parkrecord,px_car".$condition_tomonth." and px_car.id=px_parkrecord.car_id GROUP BY px_car.type order by px_car.type asc";
		$result_income[1] = $Model->query ( $sql_income );
		$sql_income="select px_car.type,SUM(px_parkrecord.money) money from px_parkrecord,px_car".$condition_toyear." and px_car.id=px_parkrecord.car_id GROUP BY px_car.type order by px_car.type asc";
		$result_income[2] = $Model->query ( $sql_income );
		$income_info[0]['date']=date("d号");
		$income_info[1]['date']=date("m月");
		$income_info[2]['date']=date("Y年");
		for($i=0;$i<3;$i++){
			$income_info[$i]['small']=$result_income[$i][0]['money'];
			$income_info[$i]['big']=$result_income[$i][1]['money'];
		}
		
		if(I('param.park_id',0)!=0){
			$condition1=' where px_park.id='.I('param.park_id');
		}else{
			if(I('param.user_id',0)==0)
				$user_id=$_SESSION['user']['user_id'];
			else
				$user_id=I('param.user_id',0);
			$condition1=' where px_park.user_id='.$user_id ;
		}
		$sql_park="select sum(px_park.total_num) total,sum(px_park.remain_num) remain from px_park ".$condition1;
		$result = $Model->query ( $sql_park );
		$sql_park="select count(*) cnt from px_parkrecord ".$condition." and px_parkrecord.money is null";
		$result_unpayed = $Model->query ( $sql_park );
		$sql_park="select count(*) cnt from px_parkrecord ".$condition." and px_parkrecord.money is not null";
		$result_payed = $Model->query ( $sql_park );
		if(I('param.park_id',0)!=0){
			$condition2=' where px_berth.park_id='.I('param.park_id');
		}else{
			if(I('param.user_id',0)==0)
				$user_id=$_SESSION['user']['user_id'];
			else
				$user_id=I('param.user_id',0);
			$condition2=' where px_berth.park_id=px_park.id and px_park.user_id='.$user_id ;
		}
		$sql="select count(*) cnt from px_berth ".$condition2." and px_berth.is_null=1";
		$result_used = $Model->query ( $sql);
		$income_info[3]['total']=$result[0]['total'];
		$income_info[3]['remain']=$result[0]['remain'];
		$income_info[3]['unpayed']=$result_unpayed[0]['cnt'];
		$income_info[3]['payed']=$result_payed[0]['cnt'];
		$income_info[3]['used']=$result_used[0]['cnt'];
		echo json_encode($income_info);
	}
	
	/**
	 * 获取天气情况
	 */
	public function get_weather() {
		$Weather=A('Weather');
		$html_str=$Weather->get_weather(I('param.city_code'));
		echo $html_str;
	}
	
	
	/**
	 * 获取地区列表
	 */
	public function get_area(){
		$Area=A('Area');
		$result=$Area->get_area(I('param.user_id'));
		echo $result;
	}
	
	/**
	 * 车辆管理
	 */
	public function car_manage() {
		
		if(I('param.park_id',0)!=0){
			$condition=' where px_parkrecord.park_id='.I('param.park_id') ;
		}elseif(I('param.user_id',0)!=0){
			$user_id=I('param.user_id',0);
			$condition=',px_park WHERE px_parkrecord.park_id=px_park.id AND px_park.user_id='.$user_id ;
		}elseif (isset($_SESSION['park_id'])){
			$condition=' where px_parkrecord.park_id='.$_SESSION['park_id'] ;
		}elseif(isset($_SESSION['user']['user_id'])){
			$user_id=$_SESSION['user']['user_id'];
			$condition=',px_park WHERE px_parkrecord.park_id=px_park.id AND px_park.user_id='.$user_id ;
		}
		
		if (I('param.type', 0) != 0) {
			$condition1 .= " and px_car.type=" . I('param.type');
		}
 		if (I('param.flag', 0) != 0) {
			$condition1 .= " and px_parkrecord.end_time is null";
		}
		if ((I('param.start_time', 0) != 0) || (I('param.end_time', 0) != 0)) {
			$in_time = strtotime((I('param.start_time', 0) != 0)?I('param.start_time'):'2015-12-1');
			$out_time = (I('param.end_time', 0) != 0)?strtotime(I('param.end_time')):time();
			$condition1 .= ' and px_parkrecord.start_time between ' . $in_time . ' and ' . $out_time;
		}
		if ((I('param.page', 0) != 0) && (I('param.num', 0) != 0)) {
			 $condition1 .="order by px_parkrecord.start_time desc".' limit ' . $page * ($num - 1) . ',' . $page;
		}
		$Model = new Model ();
		$sql="select px_car.no as car_no,px_car.type,px_parkrecord.end_time,px_parkrecord.money,px_parkrecord.start_time,px_berth.no as park_no,unix_timestamp(now())-px_parkrecord.start_time as time,px_user.member_id 
				from px_parkrecord,px_berth,px_user,px_car,px_user_car ".$condition." and px_car.id=px_parkrecord.car_id and px_parkrecord.berth_id=px_berth.id 
				and px_car.id=px_user_car.car_id and px_user.id=px_user_car.user_id and px_berth.is_null=1 AND px_parkrecord.end_time IS null order by px_parkrecord.start_time desc";
		
		$sql1="select px_car.no as car_no,px_car.type,px_parkrecord.end_time,px_parkrecord.money,px_parkrecord.start_time,px_berth.no as park_no,unix_timestamp(now())-px_parkrecord.start_time as time,px_user.member_id
				from px_parkrecord,px_berth,px_user,px_car,px_user_car ".$condition." and px_car.id=px_parkrecord.car_id and px_parkrecord.berth_id=px_berth.id
				and px_car.id=px_user_car.car_id and px_user.id=px_user_car.user_id ".$condition1;
		if(!$condition1)
			$result = $Model->query( $sql);
		else 
			$result = $Model->query( $sql1);
			
        $json =array("total"=>0,"rows"=>array(),"in_num"=>0,"finish_num"=>0,"money"=>0);//easyUI表格的固定格式
		for($i=0;$i<count($result);$i++){
			if (($result[$i]['start_time'] >= $in_time) && ($result[$i]['start_time'] <= $out_time))
				$json['in_num']++;
			if (($result[$i]['money']))
				$json['finish_num']++;
			$json['money'] += $result[$i]['money'];
			$json['rows'][$i]['car_no']=$result[$i]['car_no'];
			if($result[$i]['type']==1){
				$json['rows'][$i]['type']="小型车";
			}elseif($result[$i]['type']==2){
				$json['rows'][$i]['type']="大型车";
			}
			$json['rows'][$i]['start_time']=date("Y-m-d h:i:sa", $result[$i]['start_time']);
			if(!$result[$i]['end_time']){
				$json['rows'][$i]['end_time']="";
				$json['rows'][$i]['time']=$this->time_tran($result[$i]['time']);
			}else{
				$json['rows'][$i]['end_time']=date("Y-m-d h:i:sa", $result[$i]['end_time']);
				$time_num=$result[$i]['end_time']-$result[$i]['start_time'];
				$json['rows'][$i]['time']=$this->time_tran($time_num);
			}
			$json['rows'][$i]['park_no']=$result[$i]['park_no'];
			$json['rows'][$i]['cost']=$result[$i]['money']+'元';
			switch ($result[$i]['member_id']) {
				case 1:
					$json['rows'][$i]['member_id']='普通会员';
					break;
				case 2:
					$json['rows'][$i]['member_id']='白银会员';
					break;
				case 3:
					$json['rows'][$i]['member_id']='黄金会员';
					break;
				default:
					$json['rows'][$i]['member_id']='';
					break;
			}
		}
		
		$json['total']=count($json['rows']);
		if(!$condition1){
			if(I('param.qore')=='export'){//导出数据到excel
				$this->data_export($json['rows'],'history');
				return;
			}else{
				$this->assign('total',$json['total']);
				$this->assign('info',json_encode($json));
				$this->display();
			}
		
		}
		else{
			if(I('param.qore')=='export'){//导出数据到excel
				$this->data_export($json['rows'],'history');
				return;
			}else{
				echo json_encode($json);
			}
		}
	}

	
	
	
	/**
	 * 时间戳转换成时间长度
	 * @param unknown $the_time 时间戳
	 */
	private function time_tran($the_time) {
		 
		$dur=$the_time;
		if($dur < 0){
			return $the_time;
		}else{
			if($dur < 60){
				return $dur.'秒';
			}else{
				if($dur < 3600){
					return floor($dur/60).'分钟';
				}else{
					if($dur < 86400){
						return floor($dur/3600).'小时';
					}else{
						if($dur < 259200000){ //3天内
							return floor($dur/86400).'天';
						}else{
							return $the_time;
						}
					}
				}
			}
		}
	}
		
}