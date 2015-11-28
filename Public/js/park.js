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

    /*右下角*/

    /*停车需求分布*/
    $(".right-bottom-park").hover(function(){
        $(this).children('ul').delay(200).stop(true,false).delay(300).slideDown();
    },function(){
        $(this).children('ul').stop(true,false).delay(300).slideUp();
    });

    /*时间线*/
    var mySlider = [],
        timeLine = $(".time-line li");
    /*获取当前日期前30天*/
    var myDate = new Date(); //获取今天日期
    myDate.setDate(myDate.getDate() - 31);
    var dateArray = [],
        dateTemp,
        flag = 1;
    for (var i = 0; i < 31; i++) {
        dateTemp = myDate.getFullYear()+"-"+(myDate.getMonth()+1)+"-"+myDate.getDate();
        dateArray.push(dateTemp);
        myDate.setDate(myDate.getDate() + flag);
    }
    /*获取过去24小时*/
    var myTimePast = new Date();
    myTimePast.setTime(myTimePast.getTime()-(24*60*60*1000));
    var timePastArray = [],
        timePastTemp;
    for(var i=0; i<25; i++){
        timePastTemp =
            myTimePast.getMonth()+"-"+myTimePast.getDate()+"-"+myTimePast.getHours()+":00";
        timePastArray.push(timePastTemp);
        myTimePast.setTime(myTimePast.getTime()+(1*60*60*1000))
    }
    /*获取未来24小时*/
    var myTimeFuture = new Date();
    var timeFutureArray = [],
        timeFutureTemp;
    for(var i=0; i<25; i++){
        timeFutureTemp =
            myTimeFuture.getMonth()+"-"+myTimeFuture.getDate()+"-"+myTimeFuture.getHours()+":00";
        timeFutureArray.push(timeFutureTemp);
        myTimeFuture.setTime(myTimeFuture.getTime()+(1*60*60*1000));
    }

    for(var i=0; i< timeLine.length; i++){
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
        formatter: function(value){
            return dateArray[value];
        }
    });
    mySlider[0].slider('setValue',0);

    /*过去24小时*/
    mySlider[1].slider({
        formatter: function(value){
            return timePastArray[value];
        }
    });
    mySlider[1].slider('setValue',0);

    /*未来24小时*/
    mySlider[2].slider({
        formatter: function(value){
            return timeFutureArray[value];
        }
    });
    mySlider[2].slider('setValue',0);

    for(var i=0; i< $(timeLine).length; i++)
        $(".slider").addClass('hide');
    /*二级菜单*/
    var statusNow = $(".right-bottom-park ul li");
    $(statusNow).each(function(index,ele){
        $(this).on('click',function(){
            $(".second-main-btn").html($(this).html());
            /*弹出实时状态*/
            if(index == 0) {
                for(var i=0; i<$(timeLine).length; i++)
                    $(timeLine[i]).prev().addClass('hide');
                $(".progress-btn").click();
            }
            else {
                for(var i=0; i< $(timeLine).length; i++){
                    if(i==index-1)continue;
                    $(timeLine[i]).prev('div').addClass("hide");
                }
                $(timeLine[index-1]).prev('div').toggleClass('hide');
            }
        });
    });

    /*左下角*/
    $(".progress-btn").on("click",function(){
        $(this).toggleClass("progress-btn-on");
        if($(this).hasClass("progress-btn-on")){
            $(this).children('span').html("隐藏图例");
            $(this).children('i').removeClass("icon-plus").addClass("icon-minus");
        } else {
            $(this).children('span').html("显示图例");
            $(this).children('i').removeClass("icon-minus").addClass("icon-plus");
        }
        $(this).next("ul").stop(true,false).slideToggle();
    });



});
