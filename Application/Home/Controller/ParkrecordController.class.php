<?php

namespace Home\Controller;

use Think\Controller;
use Think\Model;

class ParkrecordController extends BaseController
{
    public function index()
    {
        $this->show('hello world', 'utf-8');
    }

    /**
     * 获取停车记录
     *
     * @param $user_id 用户id
     * @return array 结果数组
     */
    public function get_record($user_id)
    {
        $result = M()->query("SELECT prd.start_time as start,prd.end_time as end ,prd.money,pk.name as park_name FROM px_user_car AS uc,px_park AS pk,px_parkrecord AS prd WHERE ( prd.car_id=uc.car_id AND pk.id=prd.park_id AND uc.user_id=$user_id)  ORDER BY prd.start_time desc ");

        $UserModel=D('User');
        $user=$UserModel->relation(true)->find($user_id);
        $car_id=$user['Car'][0]['id'];
        $ParkrecordModel=D('Parkrecord');
        $parkrecords=$ParkrecordModel->where('money is not null')->relation(true)->select();
        $result=array();
        foreach ($parkrecords as $parkrecord) {
            array_push($result,array(
                'start'=>date('Y-m-d H:i:s',$parkrecord['start_time']),
                'end'=>date('Y-m-d H:i:s',$parkrecord['end_time']),
                'money'=>$parkrecord['money'],
                'park_name'=>$parkrecord['Park']['name']
            ));
        }
        echo json_encode($result);
        //var_dump($parkrecords);

        /*$arry = array();
        for ($i = 0; $i < count($result); $i++) {
            // $remain=$result[$i]['total_num']-$result[$i]['remain_num'];

            $arry [$i] = array(
                'start' => $result [$i] ['start'] == '' ? '' : date('Y.m.d H:i:s', $result [$i] ['start']),
                'end' => $result [$i] ['end'] == '' ? '' : date('Y.m.d H:i:s', $result [$i] ['end']),
                'money' => $result [$i]['money'],
                'park_name' => $result [$i]['park_name'],
            );
        }

        echo json_encode($arry);*/
    }


    /**
     * 统计一定时间内车流量(当停车场id为0时，查询该用户的所有停车场的数据，不为0时查询指定的停车场的数据。)
     * para i
     */
    function count_flow()
    {
        $user_id = I('post.user_id', 0);
        if (!I('post.park_id'))
            $park_id = 0;
        else
            $park_id = I('post.park_id');

        if (!I('post.time'))
            $time = 24;
        else
            $time = I('post.time');
        for ($i = 0; $i < 24; $i++) {
            $in [$i] = 0;
        }
        for ($j = 0; $j < 24; $j++) {
            $out [$j] = 0;
        }
        $now = strtotime("now ");

        $cout_start;
        $cut_time = $now - $time * 60 * 60;
        $now_h = date("H", $now) + 1;
        // $cut_time = 1446642120;
        // $now_h = date( "H", 1446728520 ) + 1;
        // echo "cut".$cut_time;
        if ($park_id == 0) {

            $resut_stime = M()->query("SELECT px_parkrecord.start_time FROM px_park,px_parkrecord
					WHERE px_park.user_id=$user_id AND px_park.id=px_parkrecord.park_id
					AND px_parkrecord.start_time>$cut_time ORDER BY px_parkrecord.start_time asc ");
            $resut_etime = M()->query("SELECT px_parkrecord.end_time FROM px_park,px_parkrecord
					WHERE px_park.user_id=$user_id AND px_park.id=px_parkrecord.park_id
					AND px_parkrecord.end_time>$cut_time ORDER BY px_parkrecord.end_time ");
        } else {
            $resut_stime = M()->query("SELECT start_time FROM px_parkrecord
					WHERE park_id =$park_id AND start_time>$cut_time
					ORDER BY start_time ");
            $resut_etime = M()->query("SELECT end_time FROM px_parkrecord
					WHERE park_id =$park_id AND end_time>$cut_time
					ORDER BY end_time ");
        }

        for ($i = 0; $i < count($resut_stime); $i++) {
            $shour = date("H", $resut_stime [$i] ['start_time']);
            if ($shour > $now_h)
                $num = $shour - $now_h;
            else
                $num = $shour - $now_h + 24;
            $in [$num] = $in [$num] + 1;
            // echo "shour" . $shour . "<br>";
            // echo "num" . $num . "<br>";
            // echo "svalue" . $in [abs ( $now_h - $shour )] . "<br>";
        }
        for ($i = 0; $i < count($resut_etime); $i++) {
            $ehour = date("H", $resut_etime [$i] ['end_time']);
            if ($ehour > $now_h)
                $num = $ehour - $now_h;
            else
                $num = $ehour - $now_h + 24;
            $out [$num] = $out [$num] + 1;
            // echo "shour" . $shour . "<br>";
            // echo "num" . abs ( $now_h - $shour ) . "<br>";
            // echo "evalue" . $out [abs ( $now_h - $ehour )] . "<br>";
        }
        $array = array(
            "in" => $in,
            "out" => $out
        );
        $this->assign('flow_detail', json_encode($array));
        $this->display();
    }

    /**
     * 停车场车次分析
     */
    function park_analyse()
    {
        // $park_id = I ( 'post.park_id' );
        $park_id = 0; // 所有停车厂

        $t = I('post.time');

        $start_time = I('post.start_time');
        $end_time = I('post.end_time');

        // echo $start_time;
        $now = strtotime("now ");

        $cout_start;
        $cut_time = $now - $t * 60 * 60;
        $now_h = date("H", $now) + 1;

        if ($park_id == 0) {
            if ($t == 24) {
                for ($i = 0; $i < 24; $i++) {
                    $business [$i] = 0;
                }
                for ($j = 0; $j < 24; $j++) {
                    $side [$j] = 0;
                }
                for ($h = 0; $h < 24; $h++) {
                    $private [$h] = 0;
                }
                for ($h = 0; $h < 24; $h++) {
                    $total [$h] = 0;
                }
                $p = 0;
                $n = $now_h - 1;
                // $cut_time = $now - $time * 60 * 60;
                for ($h = 23; $h >= 0; $h--) {

                    if ($n - $p >= 0)
                        $ti [$h] = $n - $p;
                    else
                        $ti [$h] = $n - $p + 24;
                    $p++;
                }
                for ($j = 0; $j < 24; $j++) {
                    $time [$j] = $ti [$j] . ":00";
                }
                // sort($time);
                $resut_btime = M()->query( // 商业车厂(1)查询结果
                    "SELECT px_parkrecord.start_time  FROM px_parkrecord,px_park WHERE px_park.type=1
                  AND px_parkrecord.park_id=px_park.id 
				AND px_parkrecord.start_time>$cut_time
                   ORDER BY start_time asc ");

                for ($i = 0; $i < count($resut_btime); $i++) {
                    $shour = date("H", $resut_btime [$i] ['start_time']);
                    if ($shour > $now_h)
                        $num = $shour - $now_h;
                    else {
                        $num = $shour - $now_h + 24;
                    }

                    $business [$num] = $business [$num] + 1;
                    $total [$num] = $total [$num] + 1;
                }
                $resut_stime = M()->query( // 路测（2）车厂查询结果
                    "SELECT px_parkrecord.start_time  FROM px_parkrecord,px_park WHERE px_park.type=2
                  AND px_parkrecord.park_id=px_park.id
						AND px_parkrecord.start_time>$cut_time
                   ORDER BY start_time asc ");
                // echo "f".count ( $resut_stime );
                for ($i = 0; $i < count($resut_stime); $i++) {
                    $shour = date("H", $resut_stime [$i] ['start_time']);
                    if ($shour > $now_h)
                        $num = $shour - $now_h;
                    else {
                        $num = $shour - $now_h + 24;
                    }

                    $side [$num] = $side [$num] + 1;
                    $total [$num] = $total [$num] + 1;
                }

                $resut_ptime = M()->query( // 私家车（3）车厂查询结果
                    "SELECT px_parkrecord.start_time  FROM px_parkrecord,px_park WHERE px_park.type=3
                  AND px_parkrecord.park_id=px_park.id
						AND px_parkrecord.start_time>$cut_time
                   ORDER BY start_time asc ");

                for ($i = 0; $i < count($resut_ptime); $i++) {
                    $shour = date("H", $resut_ptime [$i] ['start_time']);
                    if ($shour > $now_h)
                        $num = $shour - $now_h;
                    else {
                        $num = $shour - $now_h + 24;
                    }

                    $private [$num] = $private [$num] + 1;
                    $total [$num] = $total [$num] + 1;
                }
                // echo "".$cut_time;
                // print_r($business);

                $array = array(
                    "time" => $time,
                    "business" => $business,
                    "side" => $side,
                    "private" => $private,
                    "total" => $total
                );

                echo json_encode($array);
            }
            if ($start_time) {
                // echo "fse";
                $start_day = date("d", strtotime($start_time));
                $start_month = date("m", strtotime($start_time));
                $end_day = date("d", strtotime($end_time));
                $end_month = date("m", strtotime($end_time));
                $s = strtotime($start_time);
                $e = strtotime($end_time);
                $i = 0;
                $num = 0;
                while ($s <= $e) {

                    $time [$i] = date("d", $s);
                    $s = $s + 86400;
                    // echo "i".$time[$i]."<br>";
                    $i++;
                    $num++;
                    // echo "nuddd".$num."<br>";
                }

                // echo "nuddd".$num;

                for ($i = 0; $i < $num; $i++) {
                    $business [$i] = 0;
                }
                for ($j = 0; $j < $num; $j++) {
                    $side [$j] = 0;
                }
                for ($h = 0; $h < $num; $h++) {
                    $private [$h] = 0;
                }
                for ($h = 0; $h < $num; $h++) {
                    $total [$h] = 0;
                }
                // echo"fse".$end_time;
                $as = strtotime($start_time);
                $ae = strtotime($end_time) + 86400;
                $resut_btime = M()->query( // 商业车厂(1)查询结果
                    "SELECT px_parkrecord.start_time  FROM px_parkrecord,px_park WHERE px_park.type=1
                  AND px_parkrecord.park_id=px_park.id
				AND px_parkrecord.start_time>=$as
					AND px_parkrecord.start_time<=$ae
                   ORDER BY start_time asc ");
                for ($j = 0; $j < count($resut_btime); $j++) {
                    $d = date('Y-m-d', $resut_btime [$j] ['start_time']);

                    $dd = strtotime(date('Y-m-d', $as));
                    $ns = ( int )((strtotime($d) - $dd) / 86400);
                    $business [$ns] = $business [$ns] + 1;
                    $total [$ns] = $total [$ns] + 1;
                }

                $resut_stime = M()->query( // 路测车厂(2)查询结果
                    "SELECT px_parkrecord.start_time  FROM px_parkrecord,px_park WHERE px_park.type=2
							AND px_parkrecord.park_id=px_park.id
							AND px_parkrecord.start_time>=$as
							AND px_parkrecord.start_time<=$ae
							ORDER BY start_time asc ");
                // echo "uoo".count( $resut_stime );
                for ($j = 0; $j < count($resut_stime); $j++) {
                    $d = date('Y-m-d', $resut_stime [$j] ['start_time']);

                    $ns = ( int )((strtotime($d) - $dd) / 86400);
                    $side [$ns] = $side [$ns] + 1;
                    $total [$ns] = $total [$ns] + 1;
                }

                $resut_ptime = M()->query( // 私人车厂(3)查询结果
                    "SELECT px_parkrecord.start_time  FROM px_parkrecord,px_park WHERE px_park.type=3
							AND px_parkrecord.park_id=px_park.id
							AND px_parkrecord.start_time>=$as
							AND px_parkrecord.start_time<=$ae
							ORDER BY start_time asc ");

                /*
                 * echo "as".$as;
                 * echo "ae".$ae;
                 * echo "uu".count( $resut_ptime );
                 */
                for ($j = 0; $j < count($resut_ptime); $j++) {
                    $d = date('Y-m-d', $resut_ptime [$j] ['start_time']);
                    $dd = strtotime(date('Y-m-d', $as));
                    $ns = ( int )((strtotime($d) - $dd) / 86400);
                    $private [$ns] = $private [$ns] + 1;
                    $total [$ns] = $total [$ns] + 1;
                    // echo "dd".$private[$ns];
                }

                $array = array(
                    "time" => $time,
                    "business" => $business,
                    "side" => $side,
                    "private" => $private,
                    "total" => $total
                );

                echo json_encode($array);
            }
        } else {
            if ($t == 24) {
                for ($i = 0; $i < 24; $i++) {
                    $business [$i] = 0;
                }
                for ($j = 0; $j < 24; $j++) {
                    $side [$j] = 0;
                }
                for ($h = 0; $h < 24; $h++) {
                    $private [$h] = 0;
                }
                for ($h = 0; $h < 24; $h++) {
                    $total [$h] = 0;
                }
                $p = 0;
                $n = $now_h - 1;
                // $cut_time = $now - $time * 60 * 60;
                for ($h = 23; $h >= 0; $h--) {

                    if ($n - $p >= 0)
                        $ti [$h] = $n - $p;
                    else
                        $ti [$h] = $n - $p + 24;
                    $p++;
                }
                for ($j = 0; $j < 24; $j++) {
                    $time [$j] = $ti [$j];
                }
                // sort($time);
                $resut_btime = M()->query( // 商业车厂(1)查询结果
                    "SELECT px_parkrecord.start_time  FROM px_parkrecord,px_park WHERE px_park.type=1
						AND px_parkrecord.park_id=px_park.id
						AND px_parkrecord.start_time>$cut_time
						AND px_parkrecord.park_id=$park_id
						ORDER BY start_time asc ");

                for ($i = 0; $i < count($resut_btime); $i++) {
                    $shour = date("H", $resut_btime [$i] ['start_time']);
                    if ($shour > $now_h)
                        $num = $shour - $now_h;
                    else {
                        $num = $shour - $now_h + 24;
                    }

                    $business [$num] = $business [$num] + 1;
                    $total [$num] = $total [$num] + 1;
                    // echo "shour" . $shour . "<br>";
                    // echo "num" . $num . "<br>";
                    // echo "svalue" . $in [abs ( $now_h - $shour )] . "<br>";
                }

                $resut_stime = M()->query( // 路测（2）车厂查询结果
                    "SELECT px_parkrecord.start_time  FROM px_parkrecord,px_park WHERE px_park.type=2
						AND px_parkrecord.park_id=px_park.id
						AND px_parkrecord.start_time>$cut_time
						AND px_parkrecord.park_id=$park_id
						ORDER BY start_time asc ");

                for ($i = 0; $i < count($resut_stime); $i++) {
                    $shour = date("H", $resut_stime [$i] ['start_time']);
                    if ($shour > $now_h)
                        $num = $shour - $now_h;
                    else {
                        $num = $shour - $now_h + 24;
                    }

                    $side [$num] = $side [$num] + 1;
                    $total [$num] = $total [$num] + 1;
                    // echo "shour" . $shour . "<br>";
                    // echo "num" . $num . "<br>";
                    // echo "svalue" . $in [abs ( $now_h - $shour )] . "<br>";
                }

                $resut_ptime = M()->query( // 私家车（3）车厂查询结果
                    "SELECT px_parkrecord.start_time  FROM px_parkrecord,px_park WHERE px_park.type=3
						AND px_parkrecord.park_id=px_park.id
						AND px_parkrecord.start_time>$cut_time
						AND px_parkrecord.park_id=$park_id
						ORDER BY start_time asc ");

                for ($i = 0; $i < count($resut_ptime); $i++) {
                    $shour = date("H", $resut_ptime [$i] ['start_time']);
                    if ($shour > $now_h)
                        $num = $shour - $now_h;
                    else {
                        $num = $shour - $now_h + 24;
                    }

                    $private [$num] = $private [$num] + 1;
                    $total [$num] = $total [$num] + 1;
                }
                // echo "".$cut_time;
                // print_r($business);

                $array = array(
                    "time" => $time,
                    "business" => $business,
                    "side" => $side,
                    "private" => $private,
                    "total" => $total
                );
                /*
                 * $d=strtotime('2015-7-2');
                 * $as=strtotime('2015-7-1');
                 * echo "ffffffffff".$d;
                 * $ns=(int)(($d-$as)/86400);
                 * echo "d".$ns;
                 */
                echo json_encode($array);
            }
            if ($start_time) {
                $start_day = date("d", strtotime($start_time));
                $start_month = date("m", strtotime($start_time));
                $end_day = date("d", strtotime($end_time));
                $end_month = date("m", strtotime($end_time));
                $s = strtotime($start_time);
                $e = strtotime($end_time);
                $i = 0;
                $num = 0;
                while ($s <= $e) {

                    $time [$i] = date("d", $s);
                    $s = $s + 86400;
                    // echo "i".$time[$i]."<br>";
                    $i++;
                    $num++;
                    // echo "nuddd".$num."<br>";
                }

                // echo "nuddd".$num;

                for ($i = 0; $i < $num; $i++) {
                    $business [$i] = 0;
                }
                for ($j = 0; $j < $num; $j++) {
                    $side [$j] = 0;
                }
                for ($h = 0; $h < $num; $h++) {
                    $private [$h] = 0;
                }
                for ($h = 0; $h < $num; $h++) {
                    $total [$h] = 0;
                }
                // echo"fse".$end_time;
                $as = strtotime($start_time);
                $ae = strtotime($end_time) + 86400;
                $resut_btime = M()->query( // 商业车厂(1)查询结果
                    "SELECT px_parkrecord.start_time  FROM px_parkrecord,px_park WHERE px_park.type=1
						AND px_parkrecord.park_id=px_park.id
						AND px_parkrecord.start_time>=$as
						AND px_parkrecord.start_time<=$ae
						AND px_parkrecord.park_id=$park_id
						ORDER BY start_time asc ");
                for ($j = 0; $j < count($resut_btime); $j++) {
                    $d = date('Y-m-d', $resut_btime [$j] ['start_time']);

                    $dd = strtotime(date('Y-m-d', $as));
                    $ns = ( int )((strtotime($d) - $dd) / 86400);
                    $business [$ns] = $business [$ns] + 1;
                    $total [$ns] = $total [$ns] + 1;
                }

                $resut_stime = M()->query( // 路测车厂(2)查询结果
                    "SELECT px_parkrecord.start_time  FROM px_parkrecord,px_park WHERE px_park.type=2
						AND px_parkrecord.park_id=px_park.id
						AND px_parkrecord.start_time>=$as
						AND px_parkrecord.start_time<=$ae
						AND px_parkrecord.park_id=$park_id
						ORDER BY start_time asc ");
                // echo "uoo".count( $resut_stime );
                for ($j = 0; $j < count($resut_stime); $j++) {
                    $d = date('Y-m-d', $resut_stime [$j] ['start_time']);

                    $dd = strtotime(date('Y-m-d', $as));
                    $ns = ( int )((strtotime($d) - $dd) / 86400);
                    $side [$ns] = $side [$ns] + 1;
                    $total [$ns] = $total [$ns] + 1;
                }

                $resut_ptime = M()->query( // 路测车厂(2)查询结果
                    "SELECT px_parkrecord.start_time  FROM px_parkrecord,px_park WHERE px_park.type=3
						AND px_parkrecord.park_id=px_park.id
						AND px_parkrecord.start_time>=$as
						AND px_parkrecord.start_time<=$ae
						AND px_parkrecord.park_id=$park_id
						ORDER BY start_time asc ");
                /*
                 * echo "as".$as;
                 * echo "ae".$ae;
                 * echo "uu".count( $resut_ptime );
                 */
                for ($j = 0; $j < count($resut_ptime); $j++) {
                    $d = date('Y-m-d', $resut_ptime [$j] ['start_time']);
                    $dd = strtotime(date('Y-m-d', $as));
                    $ns = ( int )((strtotime($d) - $dd) / 86400);
                    $private [$ns] = $private [$ns] + 1;
                    $total [$ns] = $total [$ns] + 1;
                    // echo "dd".$private[$ns];
                }

                $array = array(
                    "time" => $time,
                    "business" => $business,
                    "side" => $side,
                    "private" => $private,
                    "total" => $total
                );

                echo json_encode($array);
            }
        }
    }

    /**
     * 获取某时间段内的驶入驶出流量及收益
     */
    public function count_flow_info()
    {
        $condition = "";
        if (I('param.park_id', 0) != 0) {
            $condition .= " and px_parkrecord.park_id=" . I('param.park_id');
        }
        if (I('param.type', 0) != 0) {
            $condition .= "and px_car.type=" . I('param.type');
        }
        if (I('param.flag', 0) != 0) {
            $condition .= " and px_parkrecord.end_time is null";
        }
        if (I('param.user_id', 0) != 0) {
            $condition .= " and px_park.user_id=" . I('param.user_id');
        }else{
        	$condition .= " and px_park.user_id=" . $_SESSION['user']['user_id'];
        }
        if ((I('param.start_time', 0) != 0) && (I('param.end_time', 0) != 0)) {
            $date = $this->prDates(I('param.start_time', 0), I('param.end_time', 0));
            $in_time = strtotime(I('param.start_time'));
            $out_time = strtotime(I('param.end_time'));
            $in_condition = $condition . ' and px_parkrecord.start_time between ' . $in_time . ' and ' . $out_time;
            $out_condition = $condition . ' and px_parkrecord.end_time between ' . $in_time . ' and ' . $out_time;
        }
        if ($condition != "") {
            $Model = new Model ();
            $sql_in_money = 'SELECT FROM_UNIXTIME(px_parkrecord.start_time,"%m-%d") atime, COUNT(*) cnt,SUM(px_parkrecord.money) money  FROM px_parkrecord,px_car,px_park WHERE px_park.id=px_parkrecord.park_id and px_parkrecord.car_id=px_car.id ' . $in_condition . ' GROUP  BY FROM_UNIXTIME(px_parkrecord.start_time,"%m-%d")';
            $result_line = $Model->query($sql_in_money);
            $sql_out = 'SELECT FROM_UNIXTIME(px_parkrecord.start_time,"%m-%d") atime, COUNT(*) cnt FROM px_parkrecord,px_car,px_park WHERE px_park.id=px_parkrecord.park_id and px_parkrecord.car_id=px_car.id ' . $out_condition . ' GROUP  BY FROM_UNIXTIME(px_parkrecord.start_time,"%m-%d")';
            $result_line_end = $Model->query($sql_out);
            for ($i = 0; $i < count($date); $i++) {
                $line_chart [$i] ['date'] = $date [$i];
                for ($j = 0; $j < count($result_line); $j++) {
                    if ($result_line [$j] ['atime'] == $date [$i]) {
                        $line_chart [$i] ['cnt'] = $result_line [$j] ['cnt'];
                        $line_chart [$i] ['money'] = $result_line [$j] ['money'];
                    }
                }
                for ($j = 0; $j < count($result_line_end); $j++) {
                    if ($result_line_end [$j] ['atime'] == $date [$i]) {
                        $line_chart [$i] ['cnt_end'] = $result_line_end [$j] ['cnt'];
                    }
                }
            }
            echo json_encode($line_chart);
        }
    }

    /**
     * 获取近n天的停车和收费数据
     */
    public function count_car()
    {
        if (I('param.park_id', 0) != 0) {
            $condition = ' where a.park_id=' . I('param.park_id');
        } else {
            if (I('param.user_id', 0) == 0)
                $user_id = $_SESSION ['user'] ['user_id'];
            else
                $user_id = I('param.user_id', 0);
            $condition = ',px_park AS d WHERE a.park_id=d.id AND d.user_id=' . $user_id;
        }
        $Model = new Model ();
        $time = strtotime("-" . I('param.time', 30) . " day");
        $sql = 'SELECT a.money,c.type FROM px_parkrecord AS a,px_car AS c ".$condition." AND c.id=a.car_id and a.end_time>' . $time . ' ORDER BY a.id desc';
        echo $sql;
        $result = $Model->query($sql);
        $detail ['small'] = array(
            "num" => 0,
            "money" => 0
        );
        $detail ['small_free'] = array(
            "num" => 0,
            "money" => 0
        );
        $detail ['big'] = array(
            "num" => 0,
            "money" => 0
        );
        $detail ['big_free'] = array(
            "num" => 0,
            "money" => 0
        );

        foreach ($result as $value) {
            if ($value ['type'] == 1 && $value ['money'] > 0) {
                $detail ['small'] ['num']++;
                $detail ['small'] ['money'] += $value ['money'];
            } elseif ($value ['type'] == 1 && $value ['money'] == 0) {
                $detail ['small_free'] ['num']++;
                $detail ['small_free'] ['money'] += $value ['money'];
            } elseif ($value ['type'] == 2 && $value ['money'] > 0) {
                $detail ['big'] ['num']++;
                $detail ['big'] ['money'] += $value ['money'];
            } elseif ($value ['type'] == 2 && $value ['money'] == 0) {
                $detail ['big_free'] ['num']++;
                $detail ['big_free'] ['money'] += $value ['money'];
            }
        }
        echo json_encode($detail);
    }

    /**
     * 获取某时间段内的车辆和收费情况
     */
    public function count_income()
    {
        $condition = "";
        if (I('param.park_id', 0) != 0) {
            $condition = ' where px_parkrecord.park_id=' . I('param.park_id');
        } else {
            if (I('param.user_id', 0) == 0)
                $user_id = $_SESSION ['user'] ['user_id'];
            else
                $user_id = I('param.user_id', 0);
            $condition = ',px_park WHERE px_parkrecord.park_id=px_park.id AND px_park.user_id=' . $user_id;
        }
        
        if(I('param.park_id',0)!=0){
        	$condition = ' where px_parkrecord.park_id=' . I('param.park_id');
        }elseif(I('param.user_id',0)!=0){
        	$user_id = I('param.user_id', 0);
            $condition = ',px_park WHERE px_parkrecord.park_id=px_park.id AND px_park.user_id=' . $user_id;
        }elseif (isset($_SESSION['park_id'])){
        	$condition = ' where px_parkrecord.park_id='.$_SESSION['park_id'] ;
        }elseif(isset($_SESSION['user']['user_id'])){
        	$user_id=$_SESSION['user']['user_id'];
        	$condition = ',px_park WHERE px_parkrecord.park_id=px_park.id AND px_park.user_id=' . $user_id;
        }
        
        if (I('param.type', 0) != 0) {
            $condition .= " and px_car.type=" . I('param.type');
        }
        if (I('param.flag', 0) != 0) {
            $condition .= " and px_parkrecord.end_time is null";
        }
        if (I('param.start_time', 0) != 0){
        	$in_time = strtotime(I('param.start_time'));
        }else{
        	$in_time = strtotime('2015-12-1');
        }
        if(I('param.end_time', 0) != 0){
            $out_time = strtotime(I('param.end_time'));
        }else{
        	$out_time = time();
        }
        $condition .= ' and (px_parkrecord.start_time between ' . $in_time . ' and ' . $out_time . ' OR px_parkrecord.end_time between ' . $in_time . ' and ' . $out_time . ')';
        
        	/* 	&& ) {
            $in_time = strtotime(I('param.start_time'));
            }else if((I('param.start_time', 0) != 0) && (I('param.end_time', 0) == 0)){
        	$in_time = strtotime('2015-12-1');
        	$out_time = time();
        	$condition .= ' and (px_parkrecord.start_time between ' . $in_time . ' and ' . $out_time . ' OR px_parkrecord.end_time between ' . $in_time . ' and ' . $out_time . ')';
        } */
        if ((I('param.page', 0) != 0) && (I('param.num', 0) != 0)) {
            $page_info = ' limit ' . $page * ($num - 1) . ',' . $page;
        }
        $json_array = array(
            "total" => 0,
            "rows" => array(),
            "in_num" => 0,
            "finish_num" => 0,
            "money" => 0
        ); // easyUI表格的固定格式
        if ($condition != "") {
            $Model = new Model ();
            $time = strtotime("-" . $time . " day");
            $sql = 'SELECT px_parkrecord.id,px_parkrecord.start_time,px_parkrecord.end_time,px_parkrecord.money,px_car.no,px_user.member_id,
					px_parkrecord.end_time-px_parkrecord.start_time as time FROM px_parkrecord,px_car,px_user,px_user_car ' . $condition . '
					and px_parkrecord.car_id=px_car.id and px_car.id=px_user_car.car_id and px_user_car.user_id=px_user.id ' . $page_info;
            $result = $Model->query($sql);
            $json_array ['rows'] = array();
            for ($i = 0; $i < count($result); $i++) {
                if (($result [$i] ['start_time'] >= $in_time) && ($result [$i] ['start_time'] >= $in_time))
                    $json_array ['in_num']++;
                if (($result [$i] ['end_time'] >= $in_time) && ($result [$i] ['end_time'] >= $in_time))
                    $json_array ['finish_num']++;
                $json_array ['money'] += $result [$i] ['money'];
                $json_array ['rows'] [$i] ['car_no'] = $result [$i] ['no'];
                $json_array ['rows'] [$i] ['start_time'] = ($result [$i] ['start_time']) ? date("Y-m-d h:i:s", $result [$i] ['start_time']) : '';
                $json_array ['rows'] [$i] ['end_time'] = ($result [$i] ['end_time']) ? date("Y-m-d h:i:s", $result [$i] ['end_time']) : '';
                $time_num = time() - $result [$i] ['start_time'];
                $json_array ['rows'] [$i] ['time'] = $result [$i] ['time'] ? ($this->time_tran($result [$i] ['time'])) : ($this->time_tran($time_num));
                $json_array ['rows'] [$i] ['money'] = $result [$i] ['money']+'元';
                switch ($result [$i] ['member_id']) {
                    case 1 :
                        $json_array ['rows'] [$i] ['member_id'] = '普通会员';
                        break;
                    case 2 :
                        $json_array ['rows'] [$i] ['member_id'] = '白银会员';
                        break;
                    case 3 :
                        $json_array ['rows'] [$i] ['member_id'] = '黄金会员';
                        break;
                    default :
                        $json_array ['rows'] [$i] ['member_id'] = '未知会员';
                        break;
                }
            }
        }
        if(I('param.qore')=='export'){
        	$this->data_export($json_array['rows'],'income');
        	return;
        }
        $json_array ['total'] = count($json_array ['rows']);
        echo json_encode($json_array);
    }

    /**
     * 获取停车记录
     *
     * @param $user_id 用户id
     * @return array 结果数组
     */
    public function getRecord($user_id)
    {
        $User = D('User');
        $user_data = $User->relation(true)->find($user_id);
        $data = array();
        foreach ($user_data ['Car'] as $car) {
            $Parkrecord = D('Parkrecord');
            $list = $Parkrecord->where('car_id=' . $car ['id'])->select();
            foreach ($list as $l) {
                array_push($data, $l);
            }
        }
        usort($data, function ($a, $b) {
            return $a ['time'] > $b ['time'] ? -1 : 1;
        });
        return $data;
    }

    /**
     * 获取完成交易的停车记录
     *
     * @param $id_array 车辆id数组
     * @return mixed 停车记录数组
     */
    public function complete_list($id_array)
    {
        $Parkrecord = D('Parkrecord');
        $condition = array(
            'money' => array(
                'neq',
                ''
            ),
            'car_id' => array(
                'in',
                $id_array
            )
        );
        return $Parkrecord->where($condition)->order('start_time')->relation(true)->select();
    }

    /**
     * 车辆驶离停车场
     *
     * @param unknown $car_no
     *            车牌号
     * @param unknown $berth_no
     *            车位机器编码
     * @param unknown $money
     *            停车费
     */
    public function leave($car_no, $berth_no, $money)
    {
        $Model = new Model ();
        $now = time();

        $sql = "select r.berth_id,r.id,r.park_id,u.id as user_id from px_parkrecord as r,px_car as c,px_berth as b,px_user as u,px_user_car as uc
			 where c.no='" . $car_no . "' and b.no=" . $berth_no . " and r.start_time is not null and r.end_time is null and c.id=r.car_id and
			  c.id=uc.car_id and uc.user_id=u.id and r.berth_id=b.id and b.no=" . $berth_no;

        /* /* $sql_id = "select px_parkrecord.berth_id,px_parkrecord.id,px_parkrecord.park_id,px_user.id as user_id,
                max(px_parkrecord.start_time) from px_parkrecord,px_car,px_berth,px_user,px_user_car
                where px_car.no='" . $car_no . "' and px_car.id=px_parkrecord.car_id and px_parkrecord.berth_id=px_berth.id
                        and px_parkrecord.start_time is not null
                and px_parkrecord.end_time is null and px_car.id=px_user_car.car_id and px_user_car.user_id=px_user.id"; */
        //var_dump($sql); */
        $id = $Model->query($sql);
        if ($id [0] ['id']) {
            $sql_update = "update px_parkrecord set end_time=" . $now . ",money=" . $money . " where id=" . $id [0] ['id'];
            $result1 = $Model->execute($sql_update);
            $park_id = $id [0] ['park_id'];
            $result2 = M('Park')->where('id=' . $park_id)->setInc('remain_num', 1);
            $berth_id = $id [0] ['berth_id'];
            $sql_berth = "update px_berth set is_null=0 where id=" . $berth_id;
            $result3 = $Model->execute($sql_berth);
            $result4 = M('Park')->where('id=' . $park_id)->field('total_num,remain_num')->find();
            $num = ($result4 ['total_num'] - $result4 ['remain_num']) / $result4 [total_num];

            $Target = A('Target');
            $Target->add($park_id, $num);
            $User = A('User');
            $User->cost($id [0] ['user_id'], $money);
            echo "车牌号为" . $car_no . "的车驶离id为" . $park_id . "的停车场，驶离车位的id是" . $berth_id;
        } else {
            echo "车辆全部驶离停车场";
        }
    }


    /**
     * 车辆驶入停车场
     * @param unknown $park_id 停车场id
     * @param unknown $car_id 车辆id
     * @param unknown $type 车辆类型
     * @param unknown $berth_id 车位Id
     */
    public function add($park_id, $car_id, $type, $berth_id)
    {

        $Parkrecord = D('Parkrecord');
        $parkrecord = array("park_id" => $park_id, "car_id" => $car_id, "type" => $type, "berth_id" => $berth_id);
        if (!$Parkrecord->create($parkrecord)) {
            exit($Parkrecord->getError());
        } else {
            $result = $Parkrecord->add();
        }

        $data['id'] = $berth_id;
        $data ['is_null'] = 1;
        M('Berth')->data($data)->save();//车位有车

        $condition_target ['id'] = $park_id;
        $result2 = M('Park')->where($condition_target)->setDec('remain_num', 1);//剩余车位减少一个

        $result4 = M('Park')->where($condition_target)->field('total_num,remain_num')->find();
        $num = ($result4 ['total_num'] - $result4 ['remain_num']) / $result4 ['total_num'];//计算车位使用率
        $Target = A('Target');
        $Target->add($park_id, $num);


    }


    private function prDates($start, $end)
    {
        $date = array();
        $dt_start = strtotime($start);
        $dt_end = strtotime($end);
        while ($dt_start <= $dt_end) {
            array_push($date, date('m-d', $dt_start));
            $dt_start = strtotime('+1 day', $dt_start);
        }
        return $date;
    }

    private function time_tran($the_time)
    {
        $dur = $the_time;
        if ($dur < 0) {
            return $the_time;
        } else {
            if ($dur < 60) {
                return $dur . '秒';
            } else {
                if ($dur < 3600) {
                    return floor($dur / 60) . '分钟';
                } else {
                    if ($dur < 86400) {
                        return floor($dur / 3600) . '小时';
                    } else {
                        if ($dur < 259200000) { // 3天内
                            return floor($dur / 86400) . '天';
                        } else {
                            return $the_time;
                        }
                    }
                }
            }
        }
    }
}


