//var deal_sender;
//function deal_sender_fun()
//{
//	window.clearInterval(deal_sender);
//	if (to_send_msg == true) {
//		$.ajax({
//			url: APP_ROOT + "/msg_send.php?act=deal_msg_list",
//			dataType:"json",
//			success: function(data){
//				
//				if(data.DEAL_MSG_COUNT ==0)
//				{
//					to_send_msg = false;
//				}
//				else{
//					to_send_msg = true;
//				}
//			}
//		});
//	}
//	
//	deal_sender = window.setInterval("deal_sender_fun()", send_span);
//}
//
//$(document).ready(function(){
//	
//	//关于队列群发检测
//	deal_sender = window.setInterval("deal_sender_fun()",send_span);
//});

var deal_sender;
var wx_sender;
function deal_sender_fun()
{
	window.clearInterval(deal_sender);
	$.ajax({
		url: APP_ROOT+"/msg_send.php?act=deal_msg_list",
		complete:function(data)
		{
			deal_sender = window.setInterval("deal_sender_fun()",send_span);
		}
	});
}

function wx_sender_fun()
{
	window.clearInterval(wx_sender);
	$.ajax({
		url: APP_ROOT+"/msg_send.php?act=wx_msg_list",
		complete:function(data)
		{
			wx_sender = window.setInterval("wx_sender_fun()",send_span);
		}
	});
}

$(document).ready(function(){
	//关于队列群发检测
	deal_sender = window.setInterval("deal_sender_fun()",send_span);
	wx_sender = window.setInterval("wx_sender_fun()",send_span);
});

