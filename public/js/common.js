$(document).ready( function(){
	//colorbox
	var ua = checkAgent();
	if(ua == "smp" || ua == "tab"){
		//
	}else if(ua == "pc"){
		$('.cb').colorbox();
		$('.youtube').colorbox({iframe:true, innerWidth:853, innerHeight:480});
	}
	//disable navigation
	/*
	$("#MainNavi ul li:eq(3)").addClass("cs");
	$('#MainNavi .cs a').click(function(){
		return false;
	})
	*/
	
	//background fade
	//$('#BG .l0').fadeIn(700);
	$('#BG .l0').animate({top:-50},0).show().animate({top:0},{duration:1000,easing:"easeOutBack"});
	
	//star
	$(".sub-page #BG .sub0 .prp_star").snowfall({
	    flakeCount : 15,
	    flakeIndex : "-5",
	    minSize : 5,
	    maxSize : 40,
	    minSpeed : 0.5,
	    maxSpeed : 1,
	    image : "/images/common_bgprop_star.png"});
    
	rollOverInit();
	//hoverPointerInit();
	//blinkInit();
	footerRet();
});
/* ◆フッターバナー ------------------------------ */
function footerRet(){
	$("#Footer #FooterBanner").html('<a href="http://www.dokidokivisual.com/" target="_blank"><img src="/images/common_fbn_kirara.jpg"></a> <a href="http://www.kadokawa.co.jp/" target="_blank"><img src="/images/common_fbn_kadokawa.jpg"></a> ');
}
/* ◆ソーシャルブックマーク ------------------------------ */
function socShare(typ){
	var url="http://newgame-anime.com/";
	var title="TVアニメ『NEW GAME!』オフィシャルサイト";
	switch(typ){
		case "tw":
			window.open("http://twitter.com/intent/tweet?url="+url+"&text="+title, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
			break;
		case "fb":
			window.open("http://www.facebook.com/sharer.php?u="+url+"&amp;t="+title, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
			break;
		case "gp":
			window.open("https://plus.google.com/share?url="+url, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');
			break;
	}
}
/* ◆ニュース記事スクレイプ ------------------------------ */
function newsGet(data){
	$("#news-hd dt").remove();
	
	var entry_list= $("#EntryList",data);
	var count=0;
	//var entries=[];
	$(".entry",entry_list).slice(0,6).each(function() {
		var entry_date=$(".entry-date",this).text();
		var entry_id=$(this).attr("id");
		var entry_title=$(".entry-title",this).text();
		
		var cnt='<dt class="hd"><a href="/index/index/news/#'+ entry_id +'">'+entry_title+'</a></dt>'
		
		$("#news-hd").append(cnt);
		
		console.log(cnt)
	});
}
function newsErr(){
	$("dl#news-hd").text("データの読み込みに失敗しました。");
}
/* ◆アンカー削除 ------------------------------ */
function unwrapAnchor(){
		$('.dis > a > span').each(function(){
			$(this).unwrap();
		});
}
/* ◆縮尺計算 ------------------------------ */
function resizeScale(maxWidth){
	var windowWidth = Math.max( $(window).innerWidth(), window.innerWidth);
	//IE8以下対応
	if(jQuery.browser.msie && jQuery.browser.version<=8){
		windowWidth = $(window).innerWidth();
	}
	
	var scaleD = 1;
	
	if(windowWidth < maxWidth){
		//指定フレームサイズより画面が小さい場合
		scaleD=windowWidth / maxWidth;
	}
	
	return scaleD;
}

/* ◆機種判別 ------------------------------ */
function checkAgentVer(){
	var agent = navigator.userAgent;
	var os = "";
	var size="";
	var version=0;
	
	if(agent.search(/iPhone/) != -1){
		//iPhone
		os="iPhone";
		size="small";
		var versionStr = agent.substr(agent.indexOf('OS')+3, 3);
		version = Number(versionStr.substr(0,1));
		version = version + (Number(versionStr.substr(2,1)))*0.1;
	}else if(agent.search(/iPod/) != -1){
		//iPod
		os="iPod";
		size="small";
		var versionStr = agent.substr(agent.indexOf('OS')+3, 3);
		version = Number(versionStr.substr(0,1));
		version = version + (Number(versionStr.substr(2,1)))*0.1;
	}else if(agent.search(/iPad/) != -1){
		//iPad
		os="iPad";
		size="large";
		var versionStr = agent.substr(agent.indexOf('OS')+3, 3);
		version = Number(versionStr.substr(0,1));
		version = version + (Number(versionStr.substr(2,1)))*0.1;
	}else if(agent.search(/Android/) != -1 && agent.search(/Mobile/) != -1){
		//Android smart
		os="Android";
		size="small";
		var versionStr = agent.substr(agent.indexOf('Android')+8, 3);
		version = Number(versionStr.substr(0,1));
		version = version + (Number(versionStr.substr(2,1)))*0.1;
	}else if(agent.search(/Android/) != -1){
		//Android tablet
		os="Android";
		size="large";
		var versionStr = agent.substr(agent.indexOf('Android')+8, 3);
		version = Number(versionStr.substr(0,1));
		version = version + (Number(versionStr.substr(2,1)))*0.1;
	}else{
		//PCその他
		os="PC";
		size="large";
	}
	return {"os":os,"version":version,"size":size};
}

function checkAgent(){
	var agent = navigator.userAgent;
	if(agent.search(/iPhone/) != -1 || agent.search(/iPod/) != -1){
		//iPhone or iPod
		return("smp");
	}else if(agent.search(/iPad/) != -1){
		//iPad
		return("tab");
	}else if(agent.search(/Android/) != -1 && agent.search(/mobile/) != -1){
		//Android tablet
		return("tab");
	}else if(agent.search(/Android/) != -1){
		//Android smartphone
		return("smp");
	}else{
		//PCその他
		return("pc");
	}
}

/* ◆スマートフォンのアドレスバーを隠す ------------------------------ */
function hideAdBar(){
	setTimeout("scrollTo(0,1)", 100);
}

/* ◆ポインター ------------------------------ */
// .pointer のhover時マウスカーソルを指に
function hoverPointerInit(){
	$('.pointer').hover(function(){
		$(this).css("cursor","pointer");
	},function(){
		$(this).css("cursor","default");
	});
}
/* ◆マウスオーバー ------------------------------ */
// img.btn のsrcをhoverで変更 マウスカーソルも指にする
function rollOverInit(){
	//ロールオーバーを削除して、ボタンをONに固定
	//$('img.btn_crt').attr('src', $('img.btn_crt').attr('src').replace('_off', '_on'));
	//別画像版
	$('img.btn').hover(function(){
		$(this).css("cursor","pointer");
		$(this).attr('src', $(this).attr('src').replace('_off', '_on'));
	}, function(){
		$(this).css("cursor","default");
		$(this).attr('src', $(this).attr('src').replace('_on', '_off'));
	});
	//透明度版
	$("img.btn_fade").hover(function(){
		$(this).css("cursor","pointer");
		$(this).fadeTo(100, 0.6); // マウスオーバー時にmormal速度で、透明度を60%にする
	},function(){
		$(this).css("cursor","default");
		$(this).fadeTo(100, 1.0); // マウスアウト時にmormal速度で、透明度を100%に戻す
	});
}
/* ◆マウスオーバー ------------------------------ */
// img.btn のsrcをhoverで変更 マウスカーソルも指にする
function blinkInit(){
	setInterval(function(){
		$('.blink').fadeOut(500,function(){$(this).fadeIn(500)});
	},1000);
}
/* ◆画像プリロード ------------------------------ */
jQuery.preloadImages = function(){
    for(var i = 0; i<arguments.length; i++){
        jQuery("<img>").attr("src", arguments[i]);
    }
};
/* ◆ページ初期設定 ------------------------------ */
//メインナビに現在のページを反映
function mainNaviCurrent(num){
	$("#mainNavi ul li:nth-child("+num+")").addClass("current");
}
//bodyにIDを反映
function bodyId(idstr){
	$("body").attr("id",idstr);
}
//boxの高さをrootと同じ（height100%）にする
function justifyBoxHeight(trg){
	var rootH = $(document).height();
	$(trg).css("height",rootH);
}
