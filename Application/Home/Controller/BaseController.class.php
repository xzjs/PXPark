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
     * 将json数据导出到excel文件中
     * @param unknown $title 表名
     * @param unknown $colum 列名
     * @param unknown $data 数据
     */
    public function excel($title,$colum,$data) {
    	
    }
}