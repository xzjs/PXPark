$(document).ready(
		 function(){
		 showpie();
		    coming_zhishu_line();
			 zhishu_line();
			// hh();
          }
		);
function hh(){
	alert("ff");
}
/**
 * 未来24小时停车指数预测曲线
 */
function  coming_zhishu_line(){
	
	  $.ajax({
          url:"../Super/coming_zhishu_line",
          type:"post",
         // data:{stime,etime},
          success:function(data){
        	 // alert("ff");
          	//alert(data);
          // alert(data.length);
             var d=eval("(" + data+ ")");
          	
       //   	alert("dd"+d['time'][0]);

     		
     		
     		
     		var option_line = {
     			    title : {
     			        text: '未来24小时停车指数预测曲线'
     			    },
     			    tooltip : {
     			        trigger: 'axis'
     			    },
     			    calculable : true,
     			    xAxis : [
     			        {
     			            type : 'category',
     			            boundaryGap : false,
     			            data :d['time']
     			        }
     			    ],
     			    yAxis : [
     			        {
     			            type : 'value'
     			        }
     			    ],
     			    series : [
     			        {
     			            name:'停车指数',
     			            type:'line',
     			            data:d['value'],
     			            markPoint : {
     			                data : [
     			                    {type : 'max', name: '最大值'},
     			                    {type : 'min', name: '最小值'}
     			                ]
     			        
     			            }
     			        }
     			    ]
     			};
     		
     		var chart = echarts.init($('#lineChart')[0]); 
     		chart.setOption(option_line);
     		
          }
});
}
/**
 * 停车指数曲线
 * 
 * @returns
 */

function  zhishu_line(){
	var stime;
	var etime;
	
	if(document.getElementById("begintime").value=="")
		{var st=new Date();
	     st.setDate(st.getDate()-10);
	     var sm=st.getMonth()+1;
	     var d=st.getDate()<10?'0'+st.getDate():st.getDate();
	     stime=st.getFullYear()+"-"+sm+"-"+d;
		}

	else
		{
		stime=document.getElementById("begintime").value;
		}
	//alert(stime);
	if(document.getElementById("endtime").value=="")
	    {
		var  et=new Date();
	    var m=et.getMonth()+1;
	    var d=et.getDate()<10?'0'+et.getDate():et.getDate();
          etime=et.getFullYear()+"-"+m+"-"+d;
	   }
	
	  else
	   {
		  etime=document.getElementById("endtime").value;
	   }
	
	if(stime>etime) 
	{
		 alert("开始时间不能大于结束时间，请重新输入");
		 
	}
	else{
		var  et=new Date();
	    var m=et.getMonth()+1;
	    var d=et.getDate()<10?'0'+et.getDate():et.getDate();
	      var now=et.getFullYear()+"-"+m+"-"+d;
		if(etime>now){
		      etime=now;
		      document.getElementById("endtime").value=etime;
		      }
	  $.ajax({
          url:"../Super/zhishu_line",
          type:"post",
          data:{stime,etime},
          success:function(data){
        	 // alert("ff");
          // alert(data);
          // alert(data.length);
             var d=eval("(" + data+ ")");
                //  alert("fsd"+d['time'])  ;
            var option_indexLine = {
       			 title : {
        			        text: ' 停车指数曲线',
        			        x:'center'
        			    },
       		    tooltip : {
       		        trigger: 'axis'
       		    },
       		    calculable : true,
       		    xAxis : [
       		        {
       		            type : 'category',
       		            data : d['time']
       		                    
       		        }
       		    ],
       		            
       		    yAxis:[{
       		    type : 'value'
       		        }
       		    ],
       		    series : [
       		              
       		        {
       		        	 name:'停车指数',
       			            type:'line',
       		            data:d['value']
       		        }
       		    ]
       		};
        		var chart = echarts.init($('#indexLine')[0]); 
        		chart.setOption(option_indexLine);
          }
});
	}
}

function showpie(){
        // 按需加载
           // alert("fff");
            $.ajax({
                url:"../Super/pie_parks",
                type:"post",
               // data:{xx,yy,zz},
                success:function(data){
                // alert(data);
                // alert(data.length);
                   var d=eval("(" + data+ ")");
                	
                	// alert("dd"+d.pu);
                   var pu = d.pu;
                  var lu = d.lu;
                 var ge = d.ge;
                  var  ch = d.ch;
                    
                    var option_pie = {
             			    title : {
             			        text: '各种停车场使用比例',
             			        x:'center'
             			    },
             			    legend: {
            		        	data:['个人停车场','普通营业停车场','充电桩','路侧停车场'],
            		        	x:'right',
            		        	orient:'vertical'
            		    	},
             			    tooltip : {
             			        trigger: 'item',
             			        formatter: "{a} <br/>{b} : {c} ({d}%)"
             			    },
             			    calculable : true,
             			    series : [
             			        {
             			            name:'访问来源',
             			            type:'pie',
             			            radius : '55%',
             			            center: ['50%', '60%'],
             			            data:[
             			                {value:ge, name:'个人停车场'},
             			                {value:pu, name:'普通营业停车场'},
             			                {value:ch, name:'充电桩'},
             			                {value:lu, name:'路侧停车场'}
             			            ]
             			        }
             			    ]
             			};
             		var chart = echarts.init($('#pieChart')[0]); 
             		chart.setOption(option_pie);
                  
            }
            });
        
    }