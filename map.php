<!DOCTYPE HTML>
<html>
<head>
    <title>加载海量点</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <style type="text/css">
        html, body {
            margin: 0;
            width: 100%;
            height: 100%;
            background: #ffffff;
        }

        #map {
            width: 100%;
            height: 100%;
        }

        #panel {
            position: absolute;
            top: 30px;
            left: 10px;
            z-index: 999;
            color: #fff;
        }

        #login {
            position: absolute;
            width: 300px;
            height: 40px;
            left: 50%;
            top: 50%;
            margin: -40px 0 0 -150px;
        }

        #login input[type=password] {
            width: 200px;
            height: 30px;
            padding: 3px;
            line-height: 30px;
            border: 1px solid #000;
        }

        #login input[type=submit] {
            width: 80px;
            height: 38px;
            display: inline-block;
            line-height: 38px;
        }
    </style>
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=ABMyPFHzCuKItIEoAG2FZjtt"></script>
    <script type="text/javascript" src="http://developer.baidu.com/map/jsdemo/data/points-sample-data.js"></script>
</head>
<body>
<div id="map"></div>
<script type="text/javascript">
    var map = new BMap.Map("map", {});                        // 创建Map实例
    map.centerAndZoom(new BMap.Point(105.000, 38.000), 5);     // 初始化地图,设置中心点坐标和地图级别
    map.enableScrollWheelZoom();                        //启用滚轮放大缩小
    if (document.createElement('canvas').getContext) {  // 判断当前浏览器是否支持绘制海量点
        var points = [];  // 添加海量点数据
        <?php
            $myfile = fopen("little.txt", "r") or die("Unable to open file!");
            $data=array();
            while(!feof($myfile)) {
                $str=fgets($myfile);
                $arr=explode(',',$str);
                $arr[1]=substr($arr[1],0,-2);
                if($arr[1]){
                    array_push($data,$arr);
                }
            }
            fclose($myfile);
            echo 'data='.json_encode($data).';';
        ?>
        for (var i = 0; i < data.length; i++) {
            var convertor = new BMap.Convertor();
            var ggPoint = new BMap.Point(data[i][0], data[i][1]);
            var pointArr = [];
            pointArr.push(ggPoint);
            convertor.translate(pointArr, 1, 5, translateCallback);
        }
        var options = {
            size: BMAP_POINT_SIZE_SMALL,
            shape: BMAP_POINT_SHAPE_STAR,
            color: '#d340c3'
        }
        var pointCollection = new BMap.PointCollection(points, options);  // 初始化PointCollection
        pointCollection.addEventListener('click', function (e) {
            alert('单击点的坐标为：' + e.point.lng + ',' + e.point.lat);  // 监听点击事件
        });
        map.addOverlay(pointCollection);  // 添加Overlay

        //坐标转换完之后的回调函数
        translateCallback = function (data1){
            if(data1.status === 0) {
                points.push(new BMap.Point(data1.points[0]));
            }
        }
    } else {
        alert('请在chrome、safari、IE8+以上浏览器查看本示例');
    }
</script>
</body>
</html>


