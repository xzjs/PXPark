$(function() {
	$('.static_right').load('parkingStateInfo.html');
	$('.sel').on('click', function() {
		 window.location.href='/PXPark/index.php/Home/Super/statisticInfo.html';
		
	});
	$('#carManager').on('click', function() {
		 window.location.href='/PXPark/index.php/Home/Super/carManager.html';
		
	});
	$('#userInfo').on('click', function() {
		window.location.href='/PXPark/index.php/Home/Super/userInfo.html';
		
	});

	
});
