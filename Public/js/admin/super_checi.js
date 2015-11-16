$(document).ready(
		 function(){
			park_analyse();
			finished_park_analyse();
          }
		);
function finished_park_analyse(){
	var time=24;
	 $.ajax({
         url:"../Parkrecord/park_analyse",
         type:"post",
         data:{time},
         success:function(data){
       	 // alert("ff");
         	//alert(data);
         // alert(data.length);
            var d=eval("(" + data+ ")");
         	
        	//alert("dd"+d['time'][0]);


     		var option2 = {
     				title : {
     				        text: '过去24小时各个停车场停车车次'
     				    },
     			    tooltip : {
     			        trigger: 'axis'
     			    },
     			    calculable : true,
     			    legend: {
     			        data:['普通停车场','路侧停车场','个人停车场','全部停车场']
     			    },
     			    xAxis : [
     			        {
     			            type : 'category',
     			            data :d['time']
     			                    
     			        }
     			    ],
     			    yAxis : [
     			        {
     			            type : 'value',
     			            name : '车次',
     			            axisLabel : {
     			                formatter: '{value} 辆'
     			            }
     			        },
     			        {
     			            type : 'value',
     			            name : '车次',
     			            axisLabel : {
     			                formatter: '{value} 辆'
     			            }
     			        }
     			    ],
     			    series : [

     			        {
     			            name:'普通停车场',
     			            type:'bar',
     			            data:d['business']
     			        },
     			        {
     			            name:'路侧停车场',
     			            type:'bar',
     			            data:d['side']
     			        },
     			        {
     			            name:'个人停车场',
     			            type:'bar',
     			            data:d['private']
     			        },
     			        {
     			            name:'全部停车场',
     			            type:'line',
     			            yAxisIndex: 1,
     			            data:d['total']
     			        }
     			    ]
     			};
     	 		var chart = echarts.init($('#lineChart2')[0]); 
     	 		chart.setOption(option2);
         }
});
}
function  park_analyse(){
	var start_time;
	var end_time;
	if(document.getElementById("begintime").value=="")
		{var st=new Date();
	     st.setDate(st.getDate()-10);
	     var sm=st.getMonth()+1;
	     start_time=st.getFullYear()+"-"+sm+"-"+st.getDate();
		}

	else
		{
		start_time=document.getElementById("begintime").value;
		}
	//alert(start_time);
	if(document.getElementById("endtime").value=="")
	    {var  et=new Date();
	    var m=et.getMonth()+1;
	    end_time=et.getFullYear()+"-"+m+"-"+et.getDate();
	   }
	  else
	   {
		  end_time=document.getElementById("endtime").value;
	   }
	//alert(new Date());
	  $.ajax({
          url:"../Parkrecord/park_analyse",
          type:"post",
          data:{start_time,end_time},
          success:function(data){
        	// alert("ff");
       //  alert(data);
          // alert(data.length);
             var d=eval("(" + data+ ")");
                //  alert("fsd"+d['time'])  ;
             var option1 = {
         			title : {
         			        text: '各个停车场停车车次'
         			    },
         		    tooltip : {
         		        trigger: 'axis'
         		    },
         		    calculable : true,
         		    legend: {
         		        data:['普通停车场','路侧停车场','个人停车场','全部停车场']
         		    },
         		    xAxis : [
         		        {
         		            type : 'category',
         		            data : d['time']
         		                    
         		        }
         		    ],
         		    yAxis : [
         		      {
         		            type : 'value',
         		            name : '车次',
         		            axisLabel : {
         		                formatter: '{value} 辆'
         		            }
         		        },
         		        {
         		            type : 'value',
         		            name : '车次',
         		            axisLabel : {
         		                formatter: '{value} 辆'
         		            }
         		        }
         		    ],
         		    series : [

         		        {
         		            name:'普通停车场',
         		            type:'bar',
         		            data:d['business']
         		        },
         		        {
         		            name:'路侧停车场',
         		            type:'bar',
         		            data:d['side']
         		        },
         		        {
         		            name:'个人停车场',
         		            type:'bar',
         		            data:d['private']
         		        },
         		        {
         		            name:'全部停车场',
         		            type:'line',
         		            yAxisIndex: 1,
         		            data:d['total']
         		        }
         		    ]
         		};
          		var chart = echarts.init($('#lineChart1')[0]); 
          		chart.setOption(option1);
          		
          }
});
}