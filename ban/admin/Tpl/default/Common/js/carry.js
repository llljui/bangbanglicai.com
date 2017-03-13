function modify_carry(id){
	$.ajax({
		url:ROOT+'?m=UserCarry&a=edit&id='+id+"&status="+r_status,
		dataType:"json",
		success:function(result){
			if(result.status==0){
				alert(result.info);
			}
			else{
				$.weeboxs.open(result.info, {contentType:'text',showButton:false,title:"提现申请处理",width:600,height:400});
			}
		}
	});
}
function modify_quota(id){
	$.weeboxs.open(ROOT+'?m=QuotaSubmit&a=edit&id='+id+"&status="+r_status, {contentType:'ajax',showButton:false,title:"额度申请处理",width:600,height:400});
}
