var map; // 地图
$(function() {
	/* S 初始化地图 */
	map = new BMap.Map("map");
	map.centerAndZoom(new BMap.Point(118.207244, 36.018531), 12);
	map.enableScrollWheelZoom();
	var labelTop = {
		normal: {
			label: {
				show: true,
				position: 'center',
				formatter: '{b}',
				textStyle: {
					baseline: 'bottom'
				}
			},
			labelLine: {
				show: false
			}
		}
	};
	var labelFromatter = {
		normal: {
			label: {
				formatter: function(params) {
					return 100 - params.value + '%'
				},
				textStyle: {
					baseline: 'top'
				}
			}
		},
	}
	var labelBottom = {
		normal: {
			color: '#ccc',
			label: {
				show: true,
				position: 'center'
			},
			labelLine: {
				show: false
			}
		},
		emphasis: {
			color: 'rgba(0,0,0,0)'
		}
	};
	var radius = [40, 55];
	option = {
		series: [{
			type: 'pie',
			center: ['50%', '50%'],
			radius: radius,
			x: '0%', // for funnel
			itemStyle: labelFromatter,
			data: [{
				name: 'other',
				value: 46,
				itemStyle: labelBottom
			}, {
				name: '车位余量',
				value: 54,
				itemStyle: labelTop
			}]
		}]
	};
	var chart = echarts.init($('#chart')[0]);
	chart.setOption(option);
	$('#showToggo').on('click', function() {
		var tag = $(this).attr('data-tag');
		if(tag == '0') {
			$('#chartCon').hide();
			$(this).attr('data-tag', '1');
			$('#showText').html('显示');
		} else {
			$('#chartCon').show();
			$(this).attr('data-tag', '0');
			$('#showText').html('隐藏');
		}
	});
	$('#conditionBtn').on('click', function() {
		$('#condition').show();
	});
	$('#mapWinClose').on('click', function() {
		$('#condition').hide();
	});
});
