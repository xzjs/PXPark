/**
 * 页面跳转
 * 
 * @param page
 */
function pageJump(page, me, flag) {
	$('#content').load(page);
	
	if(flag == '0') {
		removeNavBG();
		$(me).addClass('nav_selected');
		$(me).blur();
	} else if(flag == 1){
		removeNavBG();
		$(me).parent().parent().addClass('nav_selected');
		$(me).addClass('nav_selected');
		$(me).parent().parent().blur();
		$(me).blur();
	}
}
function removeNavBG() {
	$('.nav_item').each(function(i, ele) {
		$(ele).removeClass('nav_selected');
	});
}