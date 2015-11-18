var type
var parkingList;
var nowPage = 1;
var sumPage = 2;
var pageSize = 15;
// 各种车数量情况对应的颜色
var carNumType = {};
carNumType['ENOUGH'] = ['car_num_enu_title', 'car_num_enu_tag'];
carNumType['FIT'] = ['car_num_fit_title', 'car_num_fit_tag'];
carNumType['LESS'] = ['car_num_less_title', 'car_num_less_tag'];
carNumType['SHORT'] = ['car_num_short_title', 'car_num_short_tag'];
$(function() {
	//alert("ffs");
	
	//alert("ffs");
	sendtype();

	/*$('.parking_content').on('click',function(){
		var id = $(this).attr('data-id');
		alert("ffs");
		 pageJump('statisticInfo.html');
	});*/
})
function sendtype(){
// 获取被选中的option标签
 type = $('select  option:selected').val();

   $('#nowPage').html(nowPage);
	$('#sumPage').html(sumPage);
	park();
	
	renderParking();
	
}

function park(){
// parkingList = 2;
	// alert("park");
	$.ajax({
        url:"../Super/chewei_status",
        type:"post",
        ContentType:"application/json",
        data:{type},
        async :false,
        success:function(data){
        // alert(data.length);
           var d=eval("(" + data+ ")");
        parkingList = d;
       // alert("d"+parkingList);
         
}
        });
}

/**
 * 上一页
 */
function gotoPrePage() {
	if(nowPage == 1) {
		return;
	}
	$('#nowPage').html(parseInt(nowPage) - 1);
	nowPage--;
	renderParking();
}
/**
 * 下一页
 */
function gotoNextPage() {
	if(nowPage == sumPage) {
		return;
	}
	$('#nowPage').html(parseInt(nowPage) + 1);
	nowPage++;
	renderParking();
}
/**
 * 根据停车场使用情况获取相应的颜色
 * 
 * @param usingNum
 * @param SumNum
 */
function calculateType(usingNum, sumNum) {
	var sum = parseInt(sumNum);
	var using = parseInt(usingNum);
	var residue = sum - using;
	if(residue >= 40) {
		return 'ENOUGH';
	} else if(residue < 40 && residue >= 20) {
		return 'FIT';
	} else if(residue < 20 && residue >= 10) {
		return 'LESS';
	} else {
		return 'SHORT';
	}
}
/**
 * 渲染停车场列表
 */
function renderParking() {
	// alert("ff");
	$('#parkingList').children(':gt(0)').remove();
	var beginPage = (nowPage - 1) * pageSize;
	var endPage = beginPage + pageSize - 1;
	//alert("edn"+endPage);
    // alert("渲染"+parkingList);
	for( var i = beginPage; i <endPage; i++) {
		var demoItem = $('#demoItem').clone();
		var parking = parkingList[i];
		
		if(parking!='' && parking!=undefined && parking!=null){
		var usingType = calculateType(parking.usingNum, parking.sumNum);
		$(demoItem).find('.parking_content').attr('data-id', parking.id);
		$(demoItem).find('.title').html(parking.title).addClass(carNumType[usingType][0]);
		$(demoItem).find('#usingNum').html(parking.usingNum).addClass(carNumType[usingType][1]);
		$(demoItem).find('#sumNum').html(parking.sumNum);
		$(demoItem).find('#parkingImg').attr('src', parking.img);
		$(demoItem).show();
		$('#parkingList').append($(demoItem));
	}
		}
	$('.parking_content').on('click',function(){
		var id = $(this).attr('data-id');
		
		 window.location.href='/PXPark/index.php/Home/Super/statisticInfo.html';
	});
}