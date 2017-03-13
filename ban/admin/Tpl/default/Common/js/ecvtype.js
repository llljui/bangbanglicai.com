	$(document).ready(function(){
		load_exchange_row();
		$("select[name='send_type']").bind("change",function(){load_exchange_row();});
	});
	function load_exchange_row()
	{
		var send_type = $("select[name='send_type']").val();
		if(send_type==1)
		{
			$("input[name='exchange_sn']").val("");
			$("input[name='exchange_limit_bonus']").val("");
			$("#bonus_row").hide();
			$("#exchange_row").show();
		}
		else if(send_type==2)
		{
			$("input[name='exchange_score']").val("");
			$("input[name='exchange_limit_score']").val("");	
			$("#exchange_row").hide();
			$("#bonus_row").show();
		}
		else
		{			
			$("input[name='exchange_score']").val("");
			$("input[name='exchange_limit_score']").val("");	
			$("input[name='exchange_sn']").val("");
			$("input[name='exchange_limit_bonus']").val("");	
			$("#exchange_row").hide();
			$("#bonus_row").hide();
		}
	}