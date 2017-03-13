//找到用户
function get_user_list()
{
    var user_data = $("#search_user").val();
    console.log(user_data);
    if (!user_data) {
        alert('请输入要查找的内容');
        return false;
    }
    var html = '<option value="0">平台发布</option>';
    $.ajax({
        url:ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=ajax_get_user_list", 
        data:{'user_data':user_data},
        dataType:"json",
        success:function(result){
            console.log(result);
            if(result.status ==1)
            {
                var data = result.data;
                var len = data.length;
                for(var i= 0;i<len;i++)
                {
                    html += '<option value="'+data[i].id+'">'+data[i].user_name+'-'+data[i]['real_name']+'-'+data[i]['mobile']+'</option>';
                }
            } else {
            }
            $("#user_list").html(html);
        }
    });
}

