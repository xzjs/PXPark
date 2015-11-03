$(function() {
	$('#carManager').on('click', function() {
		pageJump('carManager.html');
	});
	$('#statisticInfo').on('click', function() {
		pageJump('statisticInfo.html');
	});
});
function ruleAdd(index) {
	$('#addRuleWin').modal();
}

function ruleEdit(index) {
	$('#addRuleWin').modal();
}

function ruleAdd(index) {
	$('#addRuleWin').modal();
}

function edits(){
	$('#parkingInfoWin').modal();
}

function ruleDelete() {
	$("#deleteInfoDiv").css("display", "block");
	$("#tishi").html("确定要删除" + $("#parkCompany").val() + "?");
}

function deletes() {
	$("#deleteInfoDiv").css("display", "block");
	$("#tishi").html("确定要删除" + $("#parkCompany").val() + "?");
}

function yesBtn() {
	$("#deleteInfoDiv").css("display", "none");
	$("#parkInfoDiv").css("display", "none");
	$("#parkCompany").val("");
	$("#isNullDiv").css("display", "block");
}
function noBtn() {
	$("#deleteInfoDiv").css("display", "none");
}