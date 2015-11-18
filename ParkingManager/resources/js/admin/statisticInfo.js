$(function() {
	$('.static_right').load('parkingStateInfo.html');
	$('#carManager').on('click', function() {
		pageJump('carManager.html');
	});
	$('#userInfo').on('click', function() {
		pageJump('userInfo.html');
	});
});
