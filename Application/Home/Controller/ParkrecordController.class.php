<?php

namespace Home\Controller;

use Think\Controller;
use Think\Model;

class ParkrecordController extends Controller {
	public function index() {
		$this->show ( 'hello world', 'utf-8' );
	}
	
	/**
	 * 统计一定时间内车流量(当停车场id为0时，查询该用户的所有停车场的数据，不为0时查询指定的停车场的数据。)
	 * para i
	 */
	function count_flow() {
		$user_id = I ( 'post.user_id', 0 );
		if (! I ( 'post.park_id' ))
			$park_id = 0;
		else
			$park_id = I ( 'post.park_id' );
	
		if (! I ( 'post.time' ))
			$time = 24;
		else
			$time = I ( 'post.time' );
		for($i = 0; $i < 24; $i ++) {
			$in [$i] = 0;
		}
		for($j = 0; $j < 24; $j ++) {
			$out [$j] = 0;
		}
		$now = strtotime ( "now " );
	
		$cout_start;
		$cut_time = $now - $time * 60 * 60;
		$now_h = date ( "H", $now ) + 1;
		// $cut_time = 1446642120;
		// $now_h = date( "H", 1446728520 ) + 1;
		// echo "cut".$cut_time;
		if ($park_id == 0) {
				
			$resut_stime = M ()->query ( "SELECT px_parkrecord.start_time FROM px_park,px_parkrecord
					WHERE px_park.user_id=$user_id AND px_park.id=px_parkrecord.park_id
					AND px_parkrecord.start_time>$cut_time ORDER BY px_parkrecord.start_time asc " );
			$resut_etime = M ()->query ( "SELECT px_parkrecord.end_time FROM px_park,px_parkrecord
					WHERE px_park.user_id=$user_id AND px_park.id=px_parkrecord.park_id
					AND px_parkrecord.end_time>$cut_time ORDER BY px_parkrecord.end_time " );
		} else {
			$resut_stime = M ()->query ( "SELECT start_time FROM px_parkrecord
					WHERE park_id =$park_id AND start_time>$cut_time
					ORDER BY start_time " );
			$resut_etime = M ()->query ( "SELECT end_time FROM px_parkrecord
					WHERE park_id =$park_id AND end_time>$cut_time
					ORDER BY end_time " );
		}
	
		for($i = 0; $i < count ( $resut_stime ); $i ++) {
			$shour = date ( "H", $resut_stime [$i] ['start_time'] );
			if ($shour > $now_h)
				$num = $shour - $now_h;
			else
				$num = $shour - $now_h + 24;
			$in [$num] = $in [$num] + 1;
			// echo "shour" . $shour . "<br>";
			// echo "num" . $num . "<br>";
			// echo "svalue" . $in [abs ( $now_h - $shour )] . "<br>";
		}
		for($i = 0; $i < count ( $resut_etime ); $i ++) {
			$ehour = date ( "H", $resut_etime [$i] ['end_time'] );
			if ($ehour > $now_h)
				$num = $ehour - $now_h;
			else
				$num = $ehour - $now_h + 24;
			$out [$num] = $out [$num] + 1;
			// echo "shour" . $shour . "<br>";
			// echo "num" . abs ( $now_h - $shour ) . "<br>";
			// echo "evalue" . $out [abs ( $now_h - $ehour )] . "<br>";
		}
		$array = array (
				"in" => $in,
				"out" => $out
		);
		$this->assign ( 'flow_detail', json_encode ( $array ));
		$this->display ( );
	}

	/**
	 * 停车场车次分析
	 */
	public function park_analyse() {
		$park_id = I ( 'post.park_id' );
		
		if (! I ( 'post.start_time' ))
			$t = 24;
		else {
			$start_time = I ( 'post.start_time' );
			$end_time = I ( 'post.end_time' );
		}
		$now = strtotime ( "now " );
		
		$cout_start;
		$cut_time = $now - $t * 60 * 60;
		$now_h = date ( "H", $now )+1 ;
		
		if ($park_id == 0) {
			if ($t == 24) {
				for($i = 0; $i < 24; $i ++) {
					$business [$i] = 0;
				}
				for($j = 0; $j < 24; $j ++) {
					$side[$j] = 0;
				}
				for($h = 0; $h < 24; $h ++) {
					$private [$h] = 0;
				}
				for($h = 0; $h < 24; $h ++) {
					$total [$h] = 0;
				}
				$p=0;
				$n=$now_h-1;
			//	$cut_time = $now - $time * 60 * 60;
				for($h = 23; $h >=0; $h --) {
					
					if($n-$p>=0)
					$ti[$h] = $n-$p;
					else 
					$ti[$h] = $n-$p+24;
					$p++;
					
				}
				for ($j=0;$j< 24; $j ++){
					$time[$j]=$ti[$j];
				
				}
			//	sort($time);
				$resut_btime = M ()->query //商业车厂(1)查询结果
				( "SELECT px_parkrecord.start_time  FROM px_parkrecord,px_park WHERE px_park.type=1
                  AND px_parkrecord.park_id=px_park.id 
				AND px_parkrecord.start_time>$cut_time
                   ORDER BY start_time asc " );
				
				for($i = 0; $i < count ( $resut_btime ); $i ++) {
					$shour = date ( "H", $resut_btime [$i] ['start_time'] );
					if ($shour > $now_h)
						$num = $shour - $now_h;
					else
					{
						$num = $shour - $now_h + 24;
					}
					
					$business[$num] = $business[$num] + 1;
					$total[$num] = $total[$num] + 1;
					// echo "shour" . $shour . "<br>";
					// echo "num" . $num . "<br>";
					// echo "svalue" . $in [abs ( $now_h - $shour )] . "<br>";
				}
				
				
				
				$resut_stime = M ()->query //路测（2）车厂查询结果
				( "SELECT px_parkrecord.start_time  FROM px_parkrecord,px_park WHERE px_park.type=2
                  AND px_parkrecord.park_id=px_park.id
						AND px_parkrecord.start_time>$cut_time
                   ORDER BY start_time asc " );
				
				for($i = 0; $i < count ( $resut_stime ); $i ++) {
					$shour = date ( "H", $resut_stime [$i] ['start_time'] );
					if ($shour > $now_h)
						$num = $shour - $now_h;
					else
					{
						$num = $shour - $now_h + 24;
					}
						
					$side[$num] = $side[$num] + 1;
					$total[$num] = $total[$num] + 1;
					// echo "shour" . $shour . "<br>";
					// echo "num" . $num . "<br>";
					// echo "svalue" . $in [abs ( $now_h - $shour )] . "<br>";
				}
			
				$resut_ptime = M ()->query //私家车（3）车厂查询结果
				( "SELECT px_parkrecord.start_time  FROM px_parkrecord,px_park WHERE px_park.type=3
                  AND px_parkrecord.park_id=px_park.id
						AND px_parkrecord.start_time>$cut_time
                   ORDER BY start_time asc " );
				
				for($i = 0; $i < count ( $resut_ptime ); $i ++) {
					$shour = date ( "H", $resut_ptime [$i] ['start_time'] );
					if ($shour > $now_h)
						$num = $shour - $now_h;
					else
					{
						$num = $shour - $now_h + 24;
					}
				
					$private[$num] = $private[$num] + 1;
					$total[$num] = $total[$num] + 1;
					
				}
			//	echo "".$cut_time;
				//print_r($business);
			
				$array=array(
						"time" => $time,
						"business" => $business ,
						"side" => $side,
						"private" => $private ,
						"total"=>$total,
				);
				/* $d=strtotime('2015-7-2');
				$as=strtotime('2015-7-1');
				echo "ffffffffff".$d; 
				$ns=(int)(($d-$as)/86400);
				echo "d".$ns;*/
				echo json_encode ( $array );
			}
			if($start_time ){
				$start_day=date ( "d",strtotime( $start_time) );
				$start_month=date ( "m",strtotime( $start_time) );
				$end_day=date ( "d", strtotime($end_time ));
				$end_month=date ( "m", strtotime($end_time ));
				$s=strtotime($start_time);
				$e=strtotime($end_time );
				$i=0;
				$num=0;
				while($s<=$e){
					
					$time[$i]=date ( "d",$s);
					$s=$s+86400;
				//	echo "i".$time[$i]."<br>";
					$i++;
					$num++;
					//echo "nuddd".$num."<br>";
					
				}
				
				//echo "nuddd".$num;
				
				for($i = 0; $i < $num; $i ++) {
					$business [$i] = 0;
				}
				for($j = 0; $j < $num; $j ++) {
					$side[$j] = 0;
				}
				for($h = 0; $h < $num; $h ++) {
					$private [$h] = 0;
				}
				for($h = 0; $h < $num; $h ++) {
					$total [$h] = 0;
				}
				//echo"fse".$end_time;
				$as=strtotime($start_time);
				$ae=strtotime($end_time);
			    $resut_btime = M ()->query //商业车厂(1)查询结果
				( "SELECT px_parkrecord.start_time  FROM px_parkrecord,px_park WHERE px_park.type=1
                  AND px_parkrecord.park_id=px_park.id
				AND px_parkrecord.start_time>=$as
					AND px_parkrecord.start_time<=$ae
                   ORDER BY start_time asc " );
					for($j=0;$j<count ( $resut_btime );$j++)
					{  $d=date('Y-m-d', $resut_btime [$j] ['start_time']);
					
				     	$dd=strtotime(date('Y-m-d',$as));
						$ns=(int)((strtotime($d)-$dd)/86400);
						$business[$ns]=$business[$ns]+1;
						$total[$ns] = $total[$ns] + 1;
					}
					
					$resut_stime = M ()->query //路测车厂(2)查询结果
					( "SELECT px_parkrecord.start_time  FROM px_parkrecord,px_park WHERE px_park.type=2
							AND px_parkrecord.park_id=px_park.id
							AND px_parkrecord.start_time>=$as
							AND px_parkrecord.start_time<=$ae
							ORDER BY start_time asc " );
					//echo "uoo".count( $resut_stime );
					for($j=0;$j<count ( $resut_stime );$j++)
					{  $d=date('Y-m-d', $resut_stime [$j] ['start_time']);
						
					$dd=strtotime(date('Y-m-d',$as));
					$ns=(int)((strtotime($d)-$dd)/86400);
					$side[$ns]=$side[$ns]+1;
					$total[$ns] = $total[$ns] + 1;
					}
						
						$resut_ptime = M ()->query //路测车厂(2)查询结果
						( "SELECT px_parkrecord.start_time  FROM px_parkrecord,px_park WHERE px_park.type=3
							AND px_parkrecord.park_id=px_park.id
							AND px_parkrecord.start_time>=$as
							AND px_parkrecord.start_time<=$ae
							ORDER BY start_time asc " );
						/* echo "as".$as;
						echo "ae".$ae;
						echo "uu".count( $resut_ptime ); */
						for($j=0;$j<count( $resut_ptime );$j++)
						{
							$d=date('Y-m-d', $resut_ptime [$j] ['start_time']);						
						$dd=strtotime(date('Y-m-d',$as));
						$ns=(int)((strtotime($d)-$dd)/86400);
						$private[$ns]=$private[$ns]+1;
						$total[$ns] = $total[$ns] + 1;
					//	echo "dd".$private[$ns];
						}
						
						$array=array(
								"time" => $time,
								"business" => $business ,
								"side" => $side,
								"private" => $private ,
								"total"=>$total,
						);
						
						echo json_encode ( $array );
			
			}
		} 
		
		else {
			if ($t == 24) {
				for($i = 0; $i < 24; $i ++) {
					$business [$i] = 0;
				}
				for($j = 0; $j < 24; $j ++) {
					$side[$j] = 0;
				}
				for($h = 0; $h < 24; $h ++) {
					$private [$h] = 0;
				}
				for($h = 0; $h < 24; $h ++) {
					$total [$h] = 0;
				}
				$p=0;
				$n=$now_h-1;
				//	$cut_time = $now - $time * 60 * 60;
				for($h = 23; $h >=0; $h --) {
						
					if($n-$p>=0)
						$ti[$h] = $n-$p;
					else
						$ti[$h] = $n-$p+24;
					$p++;
						
				}
				for ($j=0;$j< 24; $j ++){
					$time[$j]=$ti[$j];
			
				}
				//	sort($time);
				$resut_btime = M ()->query //商业车厂(1)查询结果
				( "SELECT px_parkrecord.start_time  FROM px_parkrecord,px_park WHERE px_park.type=1
						AND px_parkrecord.park_id=px_park.id
						AND px_parkrecord.start_time>$cut_time
						AND px_parkrecord.park_id=$park_id
						ORDER BY start_time asc " );
			
				for($i = 0; $i < count ( $resut_btime ); $i ++) {
					$shour = date ( "H", $resut_btime [$i] ['start_time'] );
					if ($shour > $now_h)
						$num = $shour - $now_h;
					else
					{
						$num = $shour - $now_h + 24;
					}
						
					$business[$num] = $business[$num] + 1;
					$total[$num] = $total[$num] + 1;
					// echo "shour" . $shour . "<br>";
					// echo "num" . $num . "<br>";
					// echo "svalue" . $in [abs ( $now_h - $shour )] . "<br>";
				}
			
			
			
				$resut_stime = M ()->query //路测（2）车厂查询结果
				( "SELECT px_parkrecord.start_time  FROM px_parkrecord,px_park WHERE px_park.type=2
						AND px_parkrecord.park_id=px_park.id
						AND px_parkrecord.start_time>$cut_time
						AND px_parkrecord.park_id=$park_id
						ORDER BY start_time asc " );
			
				for($i = 0; $i < count ( $resut_stime ); $i ++) {
					$shour = date ( "H", $resut_stime [$i] ['start_time'] );
					if ($shour > $now_h)
						$num = $shour - $now_h;
					else
					{
						$num = $shour - $now_h + 24;
					}
			
					$side[$num] = $side[$num] + 1;
					$total[$num] = $total[$num] + 1;
					// echo "shour" . $shour . "<br>";
					// echo "num" . $num . "<br>";
					// echo "svalue" . $in [abs ( $now_h - $shour )] . "<br>";
				}
					
				$resut_ptime = M ()->query //私家车（3）车厂查询结果
				( "SELECT px_parkrecord.start_time  FROM px_parkrecord,px_park WHERE px_park.type=3
						AND px_parkrecord.park_id=px_park.id
						AND px_parkrecord.start_time>$cut_time
						AND px_parkrecord.park_id=$park_id
						ORDER BY start_time asc " );
			
				for($i = 0; $i < count ( $resut_ptime ); $i ++) {
					$shour = date ( "H", $resut_ptime [$i] ['start_time'] );
					if ($shour > $now_h)
						$num = $shour - $now_h;
					else
					{
						$num = $shour - $now_h + 24;
					}
			
					$private[$num] = $private[$num] + 1;
					$total[$num] = $total[$num] + 1;
						
				}
				//echo "".$cut_time;
				//print_r($business);
					
				$array=array(
						"time" => $time,
						"business" => $business ,
						"side" => $side,
						"private" => $private ,
						"total"=>$total,
				);
				/* $d=strtotime('2015-7-2');
				 $as=strtotime('2015-7-1');
				 echo "ffffffffff".$d;
				 $ns=(int)(($d-$as)/86400);
				echo "d".$ns;*/
				echo json_encode ( $array );
			}
			if($start_time ){
				$start_day=date ( "d",strtotime( $start_time) );
				$start_month=date ( "m",strtotime( $start_time) );
				$end_day=date ( "d", strtotime($end_time ));
				$end_month=date ( "m", strtotime($end_time ));
				$s=strtotime($start_time);
				$e=strtotime($end_time );
				$i=0;
				$num=0;
				while($s<=$e){
						
					$time[$i]=date ( "d",$s);
					$s=$s+86400;
					//echo "i".$time[$i]."<br>";
					$i++;
					$num++;
					//echo "nuddd".$num."<br>";
						
				}
			
				//echo "nuddd".$num;
			
				for($i = 0; $i < $num; $i ++) {
					$business [$i] = 0;
				}
				for($j = 0; $j < $num; $j ++) {
					$side[$j] = 0;
				}
				for($h = 0; $h < $num; $h ++) {
					$private [$h] = 0;
				}
				for($h = 0; $h < $num; $h ++) {
					$total [$h] = 0;
				}
				//echo"fse".$end_time;
				$as=strtotime($start_time);
				$ae=strtotime($end_time);
				$resut_btime = M ()->query //商业车厂(1)查询结果
				( "SELECT px_parkrecord.start_time  FROM px_parkrecord,px_park WHERE px_park.type=1
						AND px_parkrecord.park_id=px_park.id
						AND px_parkrecord.start_time>=$as
						AND px_parkrecord.start_time<=$ae
						AND px_parkrecord.park_id=$park_id
						ORDER BY start_time asc " );
				for($j=0;$j<count ( $resut_btime );$j++)
				{  $d=date('Y-m-d', $resut_btime [$j] ['start_time']);
					
				$dd=strtotime(date('Y-m-d',$as));
				$ns=(int)((strtotime($d)-$dd)/86400);
				$business[$ns]=$business[$ns]+1;
				$total[$ns] = $total[$ns] + 1;
				}
					
				$resut_stime = M ()->query //路测车厂(2)查询结果
				( "SELECT px_parkrecord.start_time  FROM px_parkrecord,px_park WHERE px_park.type=2
						AND px_parkrecord.park_id=px_park.id
						AND px_parkrecord.start_time>=$as
						AND px_parkrecord.start_time<=$ae
						AND px_parkrecord.park_id=$park_id
						ORDER BY start_time asc " );
				//echo "uoo".count( $resut_stime );
				for($j=0;$j<count ( $resut_stime );$j++)
				{  $d=date('Y-m-d', $resut_stime [$j] ['start_time']);
			
				$dd=strtotime(date('Y-m-d',$as));
				$ns=(int)((strtotime($d)-$dd)/86400);
				$side[$ns]=$side[$ns]+1;
				$total[$ns] = $total[$ns] + 1;
				}
			
				$resut_ptime = M ()->query //路测车厂(2)查询结果
				( "SELECT px_parkrecord.start_time  FROM px_parkrecord,px_park WHERE px_park.type=3
						AND px_parkrecord.park_id=px_park.id
						AND px_parkrecord.start_time>=$as
						AND px_parkrecord.start_time<=$ae
						AND px_parkrecord.park_id=$park_id
						ORDER BY start_time asc " );
				/* echo "as".$as;
				 echo "ae".$ae;
				echo "uu".count( $resut_ptime ); */
				for($j=0;$j<count( $resut_ptime );$j++)
				{
					$d=date('Y-m-d', $resut_ptime [$j] ['start_time']);
					$dd=strtotime(date('Y-m-d',$as));
					$ns=(int)((strtotime($d)-$dd)/86400);
					$private[$ns]=$private[$ns]+1;
					$total[$ns] = $total[$ns] + 1;
					//	echo "dd".$private[$ns];
				}
			
				$array=array(
						"time" => $time,
						"business" => $business ,
						"side" => $side,
						"private" => $private ,
						"total"=>$total,
				);
			
				echo json_encode ( $array );
					
			}
		}
		$this->display();
		
	}
	/**
	 * 获取近n天的停车和收费数据
	 */
	
	public function count_car( ) {
		if (I('param.park_id',0)!= 0) {
			$condition=" and a.park_id=".I('param.park_id');
		}
		if (I('param.user_id',0)!= 0) {
			$condition=" and b.user_id=".I('param.user_id');
		}
		$Model = new Model ();
		$time=strtotime("-".I('param.time',30)." day");
		$sql = 'SELECT a.money,c.type FROM px_parkrecord AS a,px_park AS b,px_car AS c
				WHERE b.id=a.park_id AND c.id=a.car_id and a.end_time>'.$time.$condition.' ORDER BY a.id desc';
		echo  $sql;
		$result = $Model->query ( $sql );
		$detail['small']=array("num"=>0,"money"=>0);
		$detail['small_free']=array("num"=>0,"money"=>0);
		$detail['big']=array("num"=>0,"money"=>0);
		$detail['big_free']=array("num"=>0,"money"=>0);
			
		foreach ($result as $value){
			if($value['type']==1&&$value['money']>0){
				$detail['small']['num']++;
				$detail['small']['money']+=$value['money'];
			}elseif($value['type']==1&&$value['money']==0){
				$detail['small_free']['num']++;
				$detail['small_free']['money']+=$value['money'];
			}elseif($value['type']==2&&$value['money']>0){
				$detail['big']['num']++;
				$detail['big']['money']+=$value['money'];
			}elseif($value['type']==2&&$value['money']==0){
				$detail['big_free']['num']++;
				$detail['big_free']['money']+=$value['money'];
			}
		}
		echo json_encode($detail);
	}
	
	/**
	 * 获取某时间段内的车辆和收费情况
	 */
	public function count_income() {
		$condition="";
		if (I('param.park_id',0)!= 0) {
			$condition.=" and px_parkrecord.park_id=".I('param.park_id');
		}
		if(I('param.type',0)!=0){
			$condition.="and px_car.type=".I('param.type');
		}
		if(I('param.flag',0)!=0){
			$condition.=" and px_parkrecord.end_time is null";
		}
		if(I('param.user_id',0)!=0){
			$condition.=" and px_park.user_id=".I('param.user_id');
		}
		if((I('param.start_time',0)!=0)&&(I('param.end_time',0)!=0)){
			$in_time=strtotime(I('param.start_time'));
			$out_time=strtotime(I('param.end_time'));
			$condition.=' and (px_parkrecord.start_time between '.$in_time.' and '.$out_time.' OR px_parkrecord.end_time between '.$in_time.' and '.$out_time.')';
		}
		if((I('param.page',0)!=0)&&(I('param.num',0)!=0)){
			$condition.=' limit '.$page*($num-1).','.$page;
		}
		$json_array=array();
		if($condition!=""){
			$Model = new Model ();
			$time=strtotime("-".$time." day");
			$sql= 'SELECT px_parkrecord.id,px_parkrecord.start_time,px_parkrecord.end_time,px_parkrecord.money,px_car.no,px_user.member_id,
					px_parkrecord.end_time-px_parkrecord.start_time as time FROM px_parkrecord,px_car,px_park,px_user,px_user_car WHERE px_park.id=px_parkrecord.park_id
					and px_parkrecord.car_id=px_car.id and px_car.id=px_user_car.car_id and px_user_car.user_id=px_user.id '.$condition.'order by px_parkrecord.id desc';
			$result = $Model->query ( $sql );
			for($i=0;$i<count($result);$i++){
				if(($result[$i]['start_time']>=$in_time)&&($result[$i]['start_time']>=$in_time))
					$json_array['in_num']++;
				if(($result[$i]['end_time']>=$in_time)&&($result[$i]['end_time']>=$in_time))
					$json_array['finish_num']++;
				$json_array['money']+=$result[$i]['money'];
				$json_array['cars'][$i]['car_no']=$result[$i]['no'];
				$json_array['cars'][$i]['start_time']=$result[$i]['start_time'];
				$json_array['cars'][$i]['end_time']=$result[$i]['end_time'];
				$json_array['cars'][$i]['time']=$result[$i]['time'];
				$json_array['cars'][$i]['money']=$result[$i]['money'];
				switch ($result[$i]['member_id']) {
					case 1:
						$json_array['cars'][$i]['member_id']='普通会员';
						break;
					case 2:
						$json_array['cars'][$i]['member_id']='白银会员';
						break;
					case 3:
						$json_array['cars'][$i]['member_id']='黄金会员';
						break;
					default:
						$json_array['cars'][$i]['member_id']='';
						break;
				}
			}
		}
		$this->assign ( 'income_detail', json_encode($json_array));
		$this->display ( );
	
	}
	
	/**
	 * 获取停车记录
	 *
	 * @param number $use_id
	 *        	用户id
	 */
	public function getRecord($user_id,$page,$num) {
		$Model = new Model ();
		$sql = 'select c.name as park_name,d.no as car_no,a.start_time,a.end_time,a.money from px_parkrecord as a,
				px_user_car as b,px_park as c,px_car as d where a.car_id=b.car_id and b.user_id=' . $user_id . '
						and b.status=1 and a.park_id=c.id and b.car_id=d.id order by a.id limit '.$page*($num-1).','.$page;
		$result = $Model->query ( $sql );
		return  $result;
	}
	
}

