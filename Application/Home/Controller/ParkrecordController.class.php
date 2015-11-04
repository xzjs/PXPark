<?php

namespace Home\Controller;

use Think\Controller;

class ParkrecordController extends Controller {
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
		 $cut_time = $now - $time*60*60;
		 $now_h=date("H",$now)+1;
		//$cut_time = 1446642120;
		//$now_h = date( "H", 1446728520 ) + 1;
		 //echo "cut".$cut_time;
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
			$shour = date( "H", $resut_stime [$i] ['start_time'] );
			if($shour>$now_h)$num=$shour-$now_h;
			else $num=$shour-$now_h+24;
			$in [$num]=$in [$num]+1;
			//echo "shour" . $shour . "<br>";
			//echo "num" . $num . "<br>";
			//echo "svalue" . $in [abs ( $now_h - $shour )] . "<br>";
		}
		for($i = 0; $i < count ( $resut_etime ); $i ++) {
			$ehour = date( "H", $resut_etime [$i] ['end_time'] );
			if($ehour>$now_h)$num=$ehour-$now_h;
			else $num=$ehour-$now_h+24;
			$out [$num]=$out [$num]+1;
			//echo "shour" . $shour . "<br>";
			//echo "num" . abs ( $now_h - $shour ) . "<br>";
			//echo "evalue" . $out [abs ( $now_h - $ehour )] . "<br>";
		}
		$array=array("in"=>$in,"out"=>$out);
		echo json_encode($array);
	}
	
}
	
