/**
 * 页面跳转
 * 
 * @param page
 */
function pageJump(flag) {
    $('.'+flag[flag.length-1]).addClass('nav_selected');
}
function pageJump1(page){
	window.location.href=page;
}