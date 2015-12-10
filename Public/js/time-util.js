


/**
 * ajax图表统计获取数据
 * 
 * @param pageName
 *            页面名字
 * @param url
 *            访问链接
 * @param start
 *            开始时间
 * @param end
 *            结束时间
 * @param car_category
 *            车的类型
 */
function getInfo(pageName, url, start, end, car_category) {
	if ((!start) && (!end)) {
		start = "2015-12-1";
		end = getNowFormatDate();
	}else if((!start) || (!end)){
		return;
	}

	$.post(url, {
		qore:'query',
		start_time : start,
		end_time : end,
		type : car_category,
	}, function(datas, status) {
		if (status == 4 || status == "success") {
			if (pageName == 'car_manage') {
				var data = JSON.parse(datas);
				table1=JSON.stringify(data.rows);
				$('#personGrid').datagrid('loadData', data).datagrid(
						'clientPaging');
				$("span#in_num").text(data.in_num);
				$("span#out_num").text(data.finish_num);
				$("span#money").text(data.money);
			} else if (pageName == 'count_income') {
				var data = JSON.parse(datas);
				table1=JSON.stringify(data.rows);
				$('#personGrid').datagrid('loadData', data).datagrid(
						'clientPaging');
				$("span#in_num").text(data.in_num);
				$("span#out_num").text(data.finish_num);
				$("span#money").text(data.money);
			} else if (pageName == 'park_analyse') {
				var lineInfo = eval(datas);
				drawChart(lineInfo);
			}
		}
	});
}


function exportTable(tableId, url) {
	if(tableId==1){
		var json = table1;
	}else if(tableId==2){
		var json = table2;
	}
	var param=new Array();
	param['json']=json;
	param['file_name']='abc';
	
	post(url,param);
	
	//window.location.href=url+"/qore/export/start_time/"+start+"/end_time/"+end+"/type/"+car_category;
}

function post(URL, PARAMS) {        
    var temp = document.createElement("form");        
    temp.action = URL;        
    temp.method = "post";        
    temp.style.display = "none";        
    for (var x in PARAMS) {        
        var opt = document.createElement("textarea");        
        opt.name = x;        
        opt.value = PARAMS[x];        
        // alert(opt.name)        
        temp.appendChild(opt);        
    }        
    document.body.appendChild(temp);        
    temp.submit(); 
}

/**
 * 根据一个时间插件的值，生成对另一个时间插件的选择限制
 * 
 * @param sore
 */
function timeLimit(sore) {
	if (sore == 'start') {
		$('#income_end_time')
				.attr(
						"onclick",
						"laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss',min:getStartTime(),max:getNowFormatDate()})")
	} else if (sore == 'end') {
		$('#income_start_time')
				.attr("onclick",
						"laydate({istime: true, format: 'YYYY-MM-DD hh:mm:ss',max:getEndTime()})")
	}

}

/**
 * 获取开始插件的时间
 * 
 * @returns 开始时间
 */
function getStartTime() {
	return $('input#income_start_time').val();
}

/**
 * 获取结束插件的时间
 * 
 * @returns 结束时间
 */
function getEndTime() {
	return $('input#income_end_time').val();
}

/**
 * 获取当前时间 格式为：yyyy-MM-dd HH:MM:SS
 */
function getNowFormatDate() {
	var date = new Date();
	var seperator1 = "-";
	var seperator2 = ":";
	var month = date.getMonth() + 1;
	var strDate = date.getDate();
	if (month >= 1 && month <= 9) {
		month = "0" + month;
	}
	if (strDate >= 0 && strDate <= 9) {
		strDate = "0" + strDate;
	}
	var currentdate = date.getFullYear() + seperator1 + month + seperator1
			+ strDate + " " + date.getHours() + seperator2 + date.getMinutes()
			+ seperator2 + date.getSeconds();
	return currentdate;
}