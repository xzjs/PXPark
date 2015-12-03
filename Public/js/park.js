/**
 * Created by waydrow on 15-11-26.
 * 爱特工作室  Waydrow
 * Mail: waydrow@163.com
 */

/*平行车辆页面*/
$(document).ready(function () {

    /*右上角*/
    $(".right-top-park").hover(function(){
        $(this).children('ul').stop(true,false).fadeIn();
    },function(){
        $(this).children('ul').stop(true,false).delay(300).fadeOut();
    });

    var rightTopSecondList = $(".main-list-park li");   /*右上方主菜单*/
    var rightTopMainContent = ['R','P','R&P','B'];
    var rightTopSecondListLen = rightTopSecondList.length;  /*主菜单长度*/
    var leftBottomPark = $(".left-bottom-park");    /*左下方排行*/
    var rightBottomBtn = $(".right-bottom-park");   /*右下方按钮*/

    /*右下方回归初始状态*/
    function clearRightBottom(){
        for(var i=0; i<rightBottomBtn.length; i++){
            $(rightBottomBtn[i]).addClass('hide');
            if(i==1){
                $(rightBottomBtn[1]).find('i').addClass('icon-circle');
            } else {
                $(rightBottomBtn[i]).find('p')
                    .html($(rightBottomBtn[i]).find('li:first-child').html());
            }
        }
        $(rightBottomBtn).find('li').removeClass('on');    //默认选中样式回归初始状态
        $(rightBottomBtn).find('li:first-child').addClass('on');
        $(rightBottomBtn[1]).find('li:first-child').removeClass('on');
        $(rightBottomBtn[1]).find('li+li').addClass('on');
    }
    /*左下方回归初始状态*/
    function clearLeftBottom(){
        for(var i=0; i<leftBottomPark.length; i++)
            $(leftBottomPark[i]).addClass('hide');
    }
    $(rightTopSecondList).each(function(index,ele){
        $(this).on('click',function(){
            /*主按钮内容变化*/
            $('.main-list-park+p').html(rightTopMainContent[index]);
            for(var i=0; i<rightTopSecondList.length; i++)
                $(rightTopSecondList[i]).removeClass('on');
            $(this).addClass('on');
            $(this).parent().fadeOut('fast');
            /*左下方不显示，右下方显示图标按钮*/
            clearRightBottom();     /*右下方先隐藏全部按钮*/
            clearTimeLineAndState(); /*隐藏右下角时间线*/
            clearRecommanAndBuild(); /*隐藏模拟建设中的弹出窗*/
            $(rightBottomBtn[index]).removeClass('hide');
            if(index==0||index==2)
                $(rightBottomBtn).toggleClass('right-bottom-time-control');
            clearLeftBottom();      /*左下方按钮先全部隐藏*/
            if(index==1){   /*停车场分布左下方无显示*/
                $('.parking-lot-state').removeClass('hide');
                return;
            }
            else if(index==0)$(leftBottomPark[0]).removeClass('hide');
            else $(leftBottomPark[index-1]).removeClass('hide');
            /*模拟建设右下角大头针P默认打开*/
            if(index==3){
                $('.park-pin').removeClass('hide');
                $('.build-window').removeClass('hide');
            }
            /*自动打开排行*/
            if(index!=1){
                if(index==0){
                    if ($(leftBottomPark[index]).children('p.progress-btn').hasClass('progress-btn-off'))
                        $(leftBottomPark[index]).children('p.progress-btn').click();
                } else {
                    if ($(leftBottomPark[index - 1]).children('p.progress-btn').hasClass('progress-btn-off'))
                        $(leftBottomPark[index - 1]).children('p.progress-btn').click();
                }
            }
        });
    });

    /*右下角主按钮*/
    $(rightBottomBtn).hover(function(){
        $(this).children('ul').delay(200).stop(true,false).delay(300).slideDown();
    },function(){
        $(this).children('ul').stop(true,false).delay(300).slideUp();
    });
    $(".right-bottom-parking-lot").hover(function(){
        if($(this).children('ul').is(':hidden'))
            $(this).children('ul').stop(true,false).delay(300).animate({width:'toggle'});
    },function(){
        if(!$(this).children('ul').is(':hidden'))
            $(this).children('ul').stop(true,false).delay(300).animate({width:'toggle'});
    });

    /************************/
    /*时间线创建开始*/
    /************************/
    var mySlider = [],
        timeLine = $(".time-line li");
    /*获取当前日期前30天*/
    var myDate = new Date(); //获取今天日期
    myDate.setDate(myDate.getDate() - 31);
    var dateArray = [],
        dateTemp,
        flag = 1;
    for (var i = 0; i < 31; i++) {
        dateTemp = myDate.getFullYear() + "-" + (myDate.getMonth() + 1) + "-" + myDate.getDate();
        dateArray.push(dateTemp);
        myDate.setDate(myDate.getDate() + flag);
    }
    /*获取过去24小时*/
    var myTimePast = new Date();
    myTimePast.setTime(myTimePast.getTime() - (24 * 60 * 60 * 1000));
    var timePastArray = [],
        timePastTemp;
    for (var i = 0; i < 25; i++) {
        timePastTemp =
            myTimePast.getMonth() + "-" + myTimePast.getDate() + "-" + myTimePast.getHours() + ":00";
        timePastArray.push(timePastTemp);
        myTimePast.setTime(myTimePast.getTime() + (1 * 60 * 60 * 1000))
    }
    /*获取未来24小时*/
    var myTimeFuture = new Date();
    var timeFutureArray = [],
        timeFutureTemp;
    for (var i = 0; i < 25; i++) {
        timeFutureTemp =
            myTimeFuture.getMonth() + "-" + myTimeFuture.getDate() + "-" + myTimeFuture.getHours() + ":00";
        timeFutureArray.push(timeFutureTemp);
        myTimeFuture.setTime(myTimeFuture.getTime() + (1 * 60 * 60 * 1000));
    }

    for (var i = 0; i < timeLine.length; i++) {
        mySlider[i] = $(timeLine).eq(i).slider();
        mySlider[i].slider({
            'max': 24
        });
        mySlider[0].slider({
            'max': 30
        });
    }
    /*过去30天*/
    mySlider[0].slider({
        formatter: function (value) {
            return dateArray[value];
        }
    });
    mySlider[0].slider('setValue', 0);

    /*过去24小时*/
    mySlider[1].slider({
        formatter: function (value) {
            return timePastArray[value];
        }
    });
    mySlider[1].slider('setValue', 0);

    /*未来24小时*/
    mySlider[2].slider({
        formatter: function (value) {
            return timeFutureArray[value];
        }
    });
    mySlider[2].slider('setValue', 0);

    for (var i = 0; i < $(timeLine).length; i++)
        $(".slider").addClass('hide');
    /************************/
    /*时间线创建结束*/
    /************************/


    /*隐藏时间线和图标*/
    function clearTimeLineAndState(){
        $('.parking-lot-state').addClass('hide');
        for(var i=0; i< $(timeLine).length; i++)
            $(timeLine[i]).prev().addClass('hide');
    }

    /*停车需求分析和需求消化比二级菜单*/
    var statusNow = $(".right-bottom-time-control li");
    $(statusNow).each(function(index,ele){
        $(this).on('click',function(){
            $(this).parent('ul').next('p').html($(this).html());
            for(var i=0; i<statusNow.length; i++)
                $(statusNow[i]).removeClass('on');
            $(this).addClass('on');
            $(this).parent().slideUp('fast');
            /*弹出实时状态*/
            if(index>=4)index-=4;
            if(index == 0) {
                //clearTimeLineAndState();
                if($(".progress-btn").hasClass('progress-btn-off'))
                    $(".progress-btn").click();
            }
            else {
                for(var i=0; i< $(timeLine).length; i++){
                    if(i==index-1)continue;
                    $(timeLine[i]).prev('div').addClass("hide");
                }
                $(timeLine[index-1]).prev('div').removeClass('hide');
            }
        });
    });

    /*停车场分布*/
    var parkingLot = $('.right-bottom-parking-lot li');
    $(parkingLot).each(function(index){
        $(this).on('click',function(){
            for(var i=0; i<parkingLot.length; i++)
                $(parkingLot[i]).removeClass('on');
            $(this).addClass('on');
            $(this).parent().animate({width:'toggle'},200);
            if(index==0){
                $('.parking-lot-state').addClass('hide');
                $(this).parent().next('p').children('i').removeClass('icon-circle');
            } else {
                $('.parking-lot-state').removeClass('hide');
                $(this).parent().next('p').children('i').addClass('icon-circle');
            }
        })
    });

    /*推荐方案与模拟建设*/
    /*创建滑动条*/
    var recommandWay = $(".recommand-way");
    var recommandWayContent = $(".recommand-way ul div");
    var myRecommandArray = [];
    for(var i=0; i<recommandWayContent.length; i++){
        myRecommandArray[i] = $(recommandWayContent[i]).slider({
            'max':30,
            'tooltip':'hide'
        });
        myRecommandArray[i].on('slide',function(){
            $(".recommand-way .progress-bar").css({'width':0}); //进度条重置为0
        });
    }
    /*推荐方案弹出窗中的关闭按钮*/
    $(".recommand-way i.icon-remove").on('click',function(){
        $(recommandWay).addClass('hide');
    });
    var rightBottomSetting = $(".right-bottom-set li");
    /*隐藏推荐方案与模拟建设弹出窗*/
    function clearRecommanAndBuild(){
        $(".park-pin").addClass('hide');    //隐藏大头针 P
        $(".build-window").addClass('hide');    //隐藏建设窗口
        $('.build-window input.build-confirm-btn').addClass('hide'); //隐藏建设按钮
        $(".build-window div.progress").addClass('hide'); //隐藏进度条
        $(".recommand-way .progress").addClass('hide');
        $(".build-window .progress-bar").css({'width':0}); //进度条重置为0
        $(".recommand-way .progress-bar").css({'width':0}); //进度条重置为0
        $(".build-window select").attr('disabled',true);    //不可编辑状态
        $(".build-window ul input").attr('disabled',true);
        $(".recommand-way").addClass('hide');   //隐藏推荐弹出窗口
        $(".build-window-confirm").addClass('hide'); //隐藏点击建设后的确认窗
    }
    $(rightBottomSetting).each(function (index) {
       $(this).on('click',function(){
           $(this).parent().next().html($(this).html());
           for(var i=0; i<rightBottomSetting.length; i++)
               $(rightBottomSetting[i]).removeClass('on');
           $(this).addClass('on');
           $(this).parent().slideUp('fast');
           if(index==1){
               clearRecommanAndBuild();
               $(recommandWay).removeClass('hide');
           }
           else {
               clearRecommanAndBuild();
               $('.park-pin').removeClass('hide');
               $('.build-window').removeClass('hide');
               if($('.recommand-ranking p').hasClass('progress-btn-off'))
                $('.recommand-ranking p').click();
           }
       });
    });


    /*推荐方案点击确定后*/
    $(".submit-recommand").on('click', function () {
        var temp = 0,
            dataValue = [], //红色条中的数字
            redLine = $(".recommand-ranking ul .progress-bar"), //红色条
            greenLine = $(".recommand-ranking ul .progress-bar+.progress-bar"); //绿色条
        for(var i=0; i< $(this).prev('ul').find('div.decrease-num').length; i++)
            temp+=$(this).prev('ul').find('div.decrease-num')[i].value;
        temp=temp*0.5;
        for(var i=0; i<redLine.length; i++)
            dataValue[i] = $(redLine[i]).data('value');
        //进度条增加
        $(".recommand-way .progress").removeClass('hide');
        $(".recommand-way .progress-bar").animate({
            width: '100%'
        },500);
        /*绿色条增加*/
        $(greenLine).each(function(index){
            $(this).animate({
                width: temp+'%'
            },500,function(){
                /*回调函数 条中的数字变化*/
                $(this).children('.progress-num').html($(this).width());
            });
        });
        /*红色条减小*/
        $(redLine).each(function(index){
            $(this).animate({
                width: dataValue[index]-temp+'%'
            },500,function(){
                $(this).children('.progress-num').html($(this).width());
            });
        });
    });

    /*左下角列表赋值*/
    !function () {
        var tempArray = [],
            temp = $('.left-bottom-park .progress .progress-bar:first-child .progress-num');
        for(var i=temp.length-1; i>=0; i--){
            tempArray[i] = $(temp[i]).parent().width();
            tempArray[i]=$(window).width()*($($('.left-bottom-park')[1]).width()/100)
                *(tempArray[i]/100);
        }
        for(var j=temp.length-1; j>=0; j--) {
            $(temp[j]).html(Math.floor(tempArray[j]));
        }
    }();

    $(".progress-btn").hover(function(){
        $(this).children('span').css({display: 'inline-block'});
    }, function () {
        $(this).children('span').css({display: 'none'});
    });
    $(".progress-btn").on("click",function(){
        $(this).toggleClass("progress-btn-off");
        var progressBtnSpan = $(this).children('span');
        for(var i=0; i<progressBtnSpan.length; i++)
            $(progressBtnSpan[i]).toggleClass('hide');
        if($(this).hasClass("progress-btn-off")){
            $(this).children('i').removeClass("icon-minus").addClass("icon-plus");
        } else {
            $(this).children('i').removeClass("icon-plus").addClass("icon-minus");
        }
        $(this).next("ul").stop(true,false).slideToggle();
    });

    /*模拟建设相关*/
    /*可编辑状态切换*/
    $(".icon-pencil").on('click',function(){
        if($(this).next().find('select').is(":disabled")){
            $(this).next().find('select').attr('disabled',false);
            $(".build-window ul input").attr('disabled',false);
        } else {
            $(this).next().find('select').attr('disabled', true);
            $(".build-window ul input").attr('disabled',true);
        }
        $(this).siblings('input.build-confirm-btn').toggleClass('hide');
        $(".build-window .progress").addClass('hide');
        $(".build-window .progress-bar").css({'width':0});
    });
    /*点击建设后*/
    $(".build-confirm-btn").on('click',function(){
        $(".build-window .progress").delay(300).removeClass('hide');
        /*加载进度条*/
        $(".build-window .progress-bar").animate({
            width: '100%'
        },1500,function(){
            /*进度条加载完毕，弹出确认窗口*/
            $('.build-window-confirm').delay(300).removeClass('hide');
        });
    });
    $('.build-window-confirm input').on('click', function () {
        $(this).parent().addClass('hide');
    });
});