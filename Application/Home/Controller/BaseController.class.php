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
     * 随机获取一个坐标
     * @param string $query 坐标关键字
     * @param string $region 坐标所在城市
     * @return mixed 返回的坐标数组
     */
    public function get_position($query = '自助', $region = '青岛')
    {
        $url = "http://api.map.baidu.com/place/v2/search?ak=ABMyPFHzCuKItIEoAG2FZjtt&output=json&query=" . urlencode($query) . "&page_size=20&page_num=0&scope=1&region=" . urlencode($region);
        $result=json_decode(file_get_contents($url),true);
        $rand=rand(0,20);
        $position= $result['results'][$rand]['location'];
        //var_dump($position);
        return $position;
    }

}