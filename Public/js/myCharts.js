/**
 * Created by waydrow on 15-11-20.
 */
function createEarth(ec) {
    // 开场旋转地球
    var myChart = ec.init(document.getElementById('echarts-x-content'));
    var earthOption = {
        title: {
            text: '',
            x: 'center',
            textStyle: {
                color: 'white'
            }
        },
        tooltip: {
            formatter: '{b}'
        },
        series: [{
            type: 'map3d',
            mapType: 'world',

            background: public+'/images/starfield.jpg',
            // Have a try to change an environment
            // background: 'asset/background.jpg',

            baseLayer: {
                backgroundColor: '',
                backgroundImage: public+'/images/earth.jpg',
                quality: 'medium',

                heightImage: public+'/images/elev_bump.jpg'
            },

            light: {
                show: true,
                // Use the system time
                // time: '2013-08-07 18:09:09',
                sunIntensity: 1
            },

            itemStyle: {
                normal: {
                    label: {
                        show: true
                    },
                    borderWidth: 1,
                    borderColor: 'yellow',
                    areaStyle: {
                        color: 'rgba(0, 0, 0, 0)'
                    }
                }
            },
            data: [{}]
        }]
    };
    myChart.setOption(earthOption);

}

function createRadar(data) {
    // 左上角雷达图
    var myRadar = echarts.init(document.getElementById('radar-chart'));
    myRadar.showLoading({
        text: 'Loading',
        effect: 'whirling',
        textStyle: {
            fontSize: 20,

        }
    });
    var radarOption = {
        title: {
            text: ''
        },
        tooltip: {
            trigger: 'axis'
        },
        polar: [
            {
                indicator: [
                    {text: '收藏优先', max: 100},
                    {text: '价\n格\n低\n优\n先', max: 100},
                    {text: '距离近', max: 100},
                    {text: '车位多优先', max: 100},
                    {text: '评\n价\n优\n先', max: 100},
                ],
                name: {
                    show: true,
                    textStyle: {
                        color: '#FF6600'
                    }
                },
                radius: 130
            }
        ],
        series: [
            {
                name: '完全实况球员数据',
                type: 'radar',
                itemStyle: {
                    normal: {
                        areaStyle: {
                            type: 'default'
                        }
                    }
                },
                data: [
                    {
                        value: data,
                        name: '舍普琴科'
                    }
                ]
            }
        ]
    };
    clearTimeout(loadingTicket);
    var loadingTicket = setTimeout(function () {
        myRadar.hideLoading();
        myRadar.setOption(radarOption);
        $(".left-top-text").show();
    }, 1200);
    window.onresize = myRadar.resize;
};

function createGauge(data) {
    var myGauge = echarts.init(document.getElementById("car-bottom-board"));
    myGauge.showLoading({
        text: 'Loading',
        effect: 'whirling',
        textStyle: {
            fontSize: 20,
        }
    });
    var gaugeOption = {
        tooltip: {
            formatter: "{a} <br/>{b} : {c}%",
        },
        series: [
            {
                name: '业务指标',
                type: 'gauge',
                radius: [0,'90%'],
                detail: {formatter: '{value}%'},
                data: [{value: data, name: '并发量',}],
                title: {
                    show: true,
                    offsetCenter: [0, '-30%'],       // x, y，单位px
                    textStyle: {       // 其余属性默认使用全局文本样式，详见TEXTSTYLE
                        color: '#fff',
                        fontSize: 18,
                        fontWeight: 'bolder'
                    }
                },
            }
        ],
    };
    clearTimeout(loadingTimer);
    var loadingTimer = setTimeout(function () {
        myGauge.hideLoading();
        myGauge.setOption(gaugeOption);
    }, 2200);
    window.onresize = myGauge.resize;
};

function createPie() {
    var myPie1 = echarts.init(document.getElementById("car-charts1"));
    var myPie2 = echarts.init(document.getElementById("car-charts2"));

    var myColor = ['#10C460', '#DE4949', '#CEC51A', '#16A2EF'];

    var labelFromatter = {
        normal: {
            label: {
                formatter: function(params) {
                    return 100 - params.value + '%'
                },
                textStyle: {
                    baseline: 'top',
                    color: '#fff'
                }
            }
        }
    };
    var labelBottom = {
        normal: {
            color: '#ddd',
            label: {
                show: true,
                position: 'center',
                textStyle: {
                    fontSize: 18,
                    fontFamily: 'Microsoft YaHei, sans-serif',
                    color: '#fff'
                }
            },
            labelLine: {
                show: false
            }
        },
        emphasis: {
            color: 'rgba(0,0,0,0)'
        }
    };
    var pieOption1 = {
        title: {
            x: 'center',
            y: 'center',
            text: '停车成功率\n',
            textStyle: {
                fontFamily: 'Microsoft YaHei, sans-serif',
                fontSize: 12,
                color: '#FF7F50',
                textAlign: 'center'
            }
        },
        series: [{
            name: '停车成功率',
            type: 'pie',
            radius: ['50%', '70%'],
            itemStyle: labelFromatter,
            data: [{
                value: 56,
                name: '设备总量',
                itemStyle: labelBottom
            }, {

                value: 44,
                name: '正在工作设备量',
                itemStyle: {
                    normal: {
                        color: myColor[0],
                        label: {
                            show: false
                        },
                        labelLine: {
                            show: false
                        }
                    }
                }
            }]
        }]
    };
    var pieOption2 = {
        title: {
            x: 'center',
            y: 'center',
            text: '剩余车位\n',
            textStyle: {
                fontFamily: 'Microsoft YaHei, sans-serif',
                fontSize: 14,
                color: '#FF7F50',
                textAlign: 'center'
            }
        },

        series: [{
            name: '剩余车位',
            type: 'pie',
            radius: ['50%', '70%'],
            itemStyle: {
                normal: {
                    label: {
                        formatter: function(params) {
                            return 100 - params.value
                        },
                        textStyle: {
                            baseline: 'top',
                            color: '#fff'
                        }
                    }
                }
            },
            data: [{
                value: 70,
                name: '设备总量',
                itemStyle: labelBottom
            }, {

                value: 30,
                name: '正在工作设备量',
                itemStyle: {
                    normal: {
                        color: myColor[1],
                        label: {
                            show: false
                        },
                        labelLine: {
                            show: false
                        }
                    }
                }
            }]
        }]
    };
    myPie1.setOption(pieOption1);
    myPie2.setOption(pieOption2);

}

