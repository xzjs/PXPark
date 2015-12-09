<?php
/**
 * Created by PhpStorm.
 * User: xzjs
 * Date: 15/11/28
 * Time: 下午3:54
 */
namespace Home\Controller;

use Think\Controller;

class BaseController extends Controller
{
    /**
     * @var string 百度地图API
     */
    private $_ak='ABMyPFHzCuKItIEoAG2FZjtt';

    /**
     * 随机获取一个坐标
     * @param string $query 坐标关键字
     * @param string $region 坐标所在城市
     * @return mixed 返回的坐标数组
     */
    public function get_position($query = '自助', $region = '青岛')
    {
        $url='http://api.map.baidu.com/place/v2/search?ak='.$this->_ak.'&output=json&query=' . urlencode($query) . "&page_size=20&page_num=0&scope=1&region=" . urlencode($region);
        $result=json_decode(file_get_contents($url),true);
        $rand=rand(0,19);
        $position= $result['results'][$rand]['location'];
        //var_dump($position);
        return $position;
    }

    /**
     * 获取商圈
     * @param float $lon 经度
     * @param float $lat 纬度
     * @return mixed 商圈名称
     */
    public function get_business($lon=120.435829,$lat=36.166998){
        $url="http://api.map.baidu.com/geocoder/v2/?ak=$this->_ak&location=$lat,$lon&output=json";
        $result=json_decode(file_get_contents($url),true);
        $businesses=explode(',',$result['result']['business']);
        return $businesses[0]==''?'未知商圈':$businesses[0];
    }
    
    
    /**
     * 导出数据
     * @param unknown $info_list
     */
    protected function data_export($info_list=array(),$file_name)
    {
    	
    	$data = $info_list;
    	
    
    	foreach ($data[0] as $field=>$v){
    
    		if($field == 'car_no'){
    			$headArr[]='车牌号码';
    		}
    
    		if($field == 'type'){
    			$headArr[]='车辆类型';
    		}
    
    		if($field == 'start_time'){
    			$headArr[]='驶入时间';
    		}
    
    		if($field == 'end_time'){
    			$headArr[]='驶出时间';
    		}
    
    		if($field == 'time'){
    			$headArr[]='停车时间';
    		}
    		if($field == 'park_no'){
    			$headArr[]='所停车位号';
    		}
    
    		if($field == 'cost'){
    			$headArr[]='共消费';
    		}
    		
    		if($field == 'money'){
    			$headArr[]='共消费';
    		}
    		
    		if($field == 'member_id'){
    			$headArr[]='会员等级（折扣）';
    		}
    		
    	}
    
    
    	$this->getExcel($file_name,$headArr,$data);
    }
    
    /**
     * 将json数据导出到excel文件中
     * @param unknown $title 表名
     * @param unknown $colum 列名
     * @param unknown $data 数据
     */
    public function getExcel($title,$column,$data) {
    	
    	
    	
    	
    	
    	import("Org.Util.PHPExcel");
        import("Org.Util.PHPExcel.Writer.Excel5");
        import("Org.Util.PHPExcel.IOFactory.php");

        $date = date("Y_m_d",time());
        $title .= "_{$date}.xls";

        //创建PHPExcel对象，注意，不能少了\
        $objPHPExcel = new \PHPExcel();
        $objProps = $objPHPExcel->getProperties();

        //设置表头
        $key = ord("A");
        //print_r($headArr);exit;
        foreach($column as $v){
            $colum = chr($key);
            $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
            $objPHPExcel->setActiveSheetIndex(0) ->setCellValue($colum.'1', $v);
            $key += 1;
        }

        $column = 2;
        $objActSheet = $objPHPExcel->getActiveSheet();

        //print_r($data);exit;
        foreach($data as $key => $rows){ //行写入
            $span = ord("A");
            foreach($rows as $keyName=>$value){// 列写入
                $j = chr($span);
                $objActSheet->setCellValue($j.$column, $value);
                $span++;
            }
            $column++;
        }

        $title = iconv("utf-8", "gb2312", $title);

        //重命名表
        //$objPHPExcel->getActiveSheet()->setTitle('test');
        //设置活动单指数到第一个表,所以Excel打开这是第一个表
        $objPHPExcel->setActiveSheetIndex(0);
        ob_end_clean();//清除缓冲区,避免乱码
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=\"$title\"");
        header('Cache-Control: max-age=0');

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output'); //文件通过浏览器下载
        exit;
    }
}