/**
 * 页面跳转
 * 
 * @param page
 */
function pageJump() {
	//$('#content').load(page);
	var me= $.cookie('me');
    var flag= $.cookie('flag');
	if(flag == '0') {
		removeNavBG();
		$('#'+me).addClass('nav_selected');
	} else if(flag == 1){
		removeNavBG();
		$('#'+me).parent().parent().addClass('nav_selected');
		$('#'+me).addClass('nav_selected');
		$('#'+me).parent().parent().blur();
	} else if(flag == 3){
		removeNavBG();
		$('#'+me).parent().addClass('nav_selected');
		$('#'+me).parent().children().eq(1).children().eq(0).addClass('nav_selected');
		$('#'+me).blur();
	}
}
function pageJump1(page,me,flag){
    $.cookie('me',me);
    $.cookie('flag',flag);
	window.location.href=page;
}
function removeNavBG() {
	$('.nav_item').each(function(i, ele) {
		$(ele).removeClass('nav_selected');
	});
}