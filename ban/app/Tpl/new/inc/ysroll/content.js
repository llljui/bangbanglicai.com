

	
	/**
	 *首页公告
	 */
	function getContentList() {
		$.get($_path +"/getContentList.do", function(data, status) {
			var channelsHtml = '';
			var videoHtml = '';
			var aqbzHtml = '';
			var borrowNewsHtml = '';
			var collFriendHtml = '';
			var hyzxHtml = '';
			var webOne = '';
			var borrowOne = '';
			var bottomPictureHtml = '';
			
			$.each(data.bottomPicture, function(i, bottomPicture) {
				bottomPictureHtml += getBottomPictureHtml(i, bottomPicture);
			});
			
			$.each(data.vodeo, function(i, vodeo) {
				videoHtml += getVideoHtml(i, vodeo);
			});
			
			$.each(data.zygg, function(i, zygg) {
				aqbzHtml += getContentHtml(i, zygg);
			});
			
			$.each(data.channels, function(i, channels) {
				if (i == 0) {
					webOne += getChannelsHtml(channels);
				}
				channelsHtml += getChannelsHtml(channels);
			});
		
			
			$.each(data.borrowNews, function(i, borrowNews) {
				if (i == 0) {
					borrowOne += getContentHtml(i, borrowNews);
				}
				borrowNewsHtml += getContentHtml(i, borrowNews);
			});
			$.each(data.collFriend, function(i, collFriend) {
				collFriendHtml += getCollFriendHtml(i, collFriend);
			});
			$.each(data.hyqy, function(i, hyqy) {
				hyzxHtml += gethyCenterHtml(i, hyqy);
			});
			$("#con_borrowNews_1").html(borrowNewsHtml);
			$("#channels").html(channelsHtml);
			$("#aqbz").html(aqbzHtml);
			//$("#con_videoTab_1").html(videoHtml);
			$("#collFriend").html(collFriendHtml);
			$("#hycenter").html(hyzxHtml);
			$("#webOne").html(webOne);
			$("#borrowOne").html(borrowOne);
		    $("#bottomPicture").html(bottomPictureHtml);
		    $("#video").html(videoHtml);
			 /*MyMar1 = setInterval(Marquee1, speed); */
		});
	}
	function getBottomPictureHtml(i, data) {
		var title = data.content_title;
		var titles = "";
		if (title.length > 10) {
			titles = title.subString(0, 10) + "......";
		} else {
			titles = title;
		}
		var channels = '';
		var path = data.attach_path;
		if (typeof (path) != 'undefined') {
			path = $_path + path;
		} else {
			path = $_frontPath + "/images/mintaiLOGO.png";
		}
		var cont = data.content_txt;
		cont = cont.replace(/<[^>]+>/g, "").replace(/&nbsp;/g, "");
		if (cont.length > 30) {
			cont = cont.subString(0, 30) + "...";
		}
		if (typeof (data.external_link_title) != 'undefined'
			&& data.external_link_title != '') {
			channels = '<a  class="reports-img" target="_blank" ' + 'href="'
			+ data.external_link_title + '"><img src="' + path
			+ '" style="max-height:65px ;max-width:170px " /></a>';
		} else {
			channels = '<a alt="' + title + '" title="' + title
			+ '" class="reports-img" href="javascript:;"><img src="' + path
			+ '" style="max-height:65px ;max-width:170px " /></a>';
		}
		
		channelss = '<li class="img'+i+'">'+channels+'</li>';
		return channelss;
	}
	
	function getVideoHtml(i, data) {
		var title = data.content_title;
		var titles = "";
		if (title.length > 10) {
			titles = title.subString(0, 37);
		} else {
			titles = title;
		}
		var channels = '';
		var content = '';
		var path = data.attach_path;
		if (typeof (path) != 'undefined') {
			path = $_path + path;
		} else {
			path = $_frontPath + "/images/mintaiLOGO.png";
		}
		var cont = data.content_txt;
		var cont2 = data.content_txt;
		cont = cont.replace(/<[^>]+>/g, "").replace(/&nbsp;/g, "");
		if (cont.length > 30) {
			cont = cont.subString(0, 20) + "...";
		}
		if (typeof (data.external_link_title) != 'undefined'
			&& data.external_link_title != '') {
			channels = '<a  class="reports-img" target="_blank" ' + 'href="'
			+ data.external_link_title + '"><img src="' + path
			+ '" style="max-height:65px ;max-width:170px " /></a>';
		} else {
			channels = '<a alt="' + title + '" title="' + title
			+ '" class="reports-img" href="javascript:;"><img src="' + path
			+ '" style="max-height:65px ;max-width:170px " /></a>';
		}
		channelss= '<li><a  href="' + $_path
		+ '/content/contentDetails/' + data.channel_id + '/' + data.id
		+ '.do">● '+titles+'</a></li>';
		return channelss;
	}
	
	function getContentHtml(index, data) {
		var title = data.content_title;
		var titles = "";
		if (title.length > 10) {
			titles = title.subString(0, 36);
		} else {
			titles = title;
		}
		var channels = '';
		var content = '';
		var path = data.attach_path;
		if (typeof (path) != 'undefined') {
			path = $_path + path;
		} else {
			path = $_frontPath + "/images/mintaiLOGO.png";
		}
		var cont = data.content_txt;
		cont = cont.replace(/<[^>]+>/g, "").replace(/&nbsp;/g, "");
		if (cont.length > 20) {
			cont = cont.subString(0, 20) + "...";
		}
		if (typeof (data.external_link_title) != 'undefined'
			&& data.external_link_title != '') {
			channels = '<a  class="reports-img" target="_blank" ' + 'href="'
			+ data.external_link_title + '"><img src="' + path
			+ '" style="max-height:65px ;max-width:170px " /></a>';
		} else {
			channels = '<a alt="' + title + '" title="' + title
			+ '" class="reports-img" href="javascript:;"><img src="' + path
			+ '" style="max-height:65px ;max-width:170px " /></a>';
		}
		content = '<a alt="' + title + '" title="' + title + '"  href="' + $_path
		+ '/content/contentDetails/' + data.channel_id + '/' + data.id
		+ '.do" class="reports-text">' + cont + '</a>';
		var s = '';
		
		channelss = '<li><a  href="' + $_path
		+ '/content/contentDetails/' + data.channel_id + '/' + data.id
		+ '.do">● '+titles+'</a></li>';
		return channelss;
	}
	function getChannelsHtml(data) {
		var title = data.content_title;
		var titles = "";
		if (title.length > 18) {
			titles = title.subString(0, 18) + "......";
		} else {
			titles = title;
		}
		var channels = '';
		if (data.external_link_title != undefined
				&& data.external_link_title != '') {
			channels = '<a alt="'+title+'" title="'+title+'" target="_blank" href="'+data.external_link_title +'">'
					+ titles + '</a>';
		} else {
			channels = '<a alt="'+title+'" title="'+title+'" href="${path }/content/contentDetails/'+data.channel_id+'/'+data.id+'.do">'
					+ titles + '</a>';
		}
		var channelss = '<li class=\"hkct-fbgg\">' + channels + '</a></li>';
		return channelss;
	}
	function getChannelsHtmlOne(data) {
		var title = data.content_title;
		var titles = "";
		if (title.length > 18) {
			titles = title.subString(0, 18) + "......";
		} else {
			titles = title;
		}
		var channels = '';
		if (data.external_link_title != undefined
				&& data.external_link_title != '') {
			channels = '<a alt="'+title+'" title="'+title+'" target="_blank" href="'+data.external_link_title +'">'
					+ titles + '</a>';
		} else {
			channels = '<a alt="'+title+'" title="'+title+'" href="${path }/content/contentDetails/'+data.channel_id+'/'+data.id+'.do">'
					+ titles + '</a>';
		}
		var channelss = '<li class=\"hkct-fbgg\">' + channels + '</a></li>';
		return channelss;
	}
	function getCollFriendHtml(i, data) {
		var title = data.content_title;
		var titles = "";
		if (title.length > 10) {
			titles = title.subString(0, 10) + "......";
		} else {
			titles = title;
		}
		var channels = '';
		var path = data.attach_path;
		if (typeof (path) != 'undefined') {
			path = $_path + path;
		} else {
			path = $_frontPath + "/images/mintaiLOGO.png";
		}
		var cont = data.content_txt;
		cont = cont.replace(/<[^>]+>/g, "").replace(/&nbsp;/g, "");
		if (cont.length > 30) {
			cont = cont.subString(0, 30) + "...";
		}
		if (typeof (data.external_link_title) != 'undefined'
			&& data.external_link_title != '') {
			channels = '<a  class="reports-img" target="_blank" ' + 'href="'
			+ data.external_link_title + '"><img src="' + path
			+ '" style="max-height:65px ;max-width:170px " /></a>';
		} else {
			channels = '<a alt="' + title + '" title="' + title
			+ '" class="reports-img" href="javascript:;"><img src="' + path
			+ '" style="max-height:65px ;max-width:170px " /></a>';
		}
		channelss ='<li><a>'+channels+'</a></li>';
		return channelss;
	}
	function gethyCenterHtml(i, data) {
		var html = '';
		if (data.external_link_title) {
			html += '<li style=" width:210px; height:123px;"><a  target="_blank" href="'+data.external_link_title+'"><img alt="'+data.content_title+'" title="'+data.content_title+'" width="196" height="123" src="${path}'+data.attach_path+'"/></a>'
					+ '<p style="text-align:center; color:#666666; line-height:25px; font-size:13px;">'
					+ data.content_title + '</p></li>';
		} else {
			html += '<li style=" width:210px; height:123px;"><img alt="'+data.content_title+'" title="'+data.content_title+'" width="196" height="123" src="${path}'+data.attach_path+'"/>'
					+ '<p style="text-align:center; color:#666666; line-height:25px; font-size:13px;">'
					+ data.content_title + '</p></li>';
		}
		return html;
	}
	    