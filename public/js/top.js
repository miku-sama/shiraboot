var tp_length;
var tp_count;
$(document).ready( function(){
	//news headline
	$.ajax({
		url: "/index/index/news/",
		success : function(data){
			$("#news-hd dl").remove();
			
			var entry_list= $("#EntryList",data);
			var count=0;
			//var entries=[];
			$(".entry",entry_list).slice(0,5).each(function() {
				var entry_date=$(".entry-date",this).text();
				var entry_id=$(this).attr("id");
				var entry_title=$(".entry-title",this).text();
				
				var cnt='<dl><dt>'+entry_date+'</dt><dd><a href="/index/index/news/#'+ entry_id +'">'+entry_title+'</a></dt></dl>'
				
				$("#news-hd").append(cnt);
				
				//console.log(cnt)
			});
		},
		error: function(data){newsErr();}
	});
	//topic
	tp_length = $("#Topic li").length;
	tp_count = 0;
	topicjump()
	
	//disable navigation
	/*
	$("#TopNavi ul li:eq(3)").addClass("cs");
	$('#TopNavi .cs a').click(function(){
		return false;
	})
	*/
	//animation
	//scn_ex();
	scn201606();
});
function scn_ex(){
	setTimeout(function(){
		$('.kvex').animate({top:-100},0).show().animate({top:-140},{duration:800,easing:"easeOutBack"});
	},500);
	setTimeout(function(){
		$('#Page_Top #Catch1').animate({top:0,left:970 ,opacity:0},0).show().animate({opacity:1},100);
		$('#Page_Top #Catch2').animate({top:50,left:900 ,opacity:0},0).show().animate({opacity:1},100);
	},500);
	setTimeout(function(){
		$('#Page_Top #Catch1 span').animate({height:379},{duration:700});
	},1500);
	setTimeout(function(){
		$('#Page_Top #Catch2 span').animate({height:223},{duration:700});
	},2200);
	$('.kvex').click(function(){
		$(this).fadeOut(200);
		setTimeout(function(){
			scn3();
		},500);
	});
}
function scn201607(){
	setTimeout(function(){
		$('.k201607').animate({top:-50},0).show().animate({top:40},{duration:800,easing:"easeOutBack"});
	},500);
	setTimeout(function(){
		$('#Page_Top #Catch1').animate({top:0,left:970 ,opacity:0},0).show().animate({opacity:1},100);
		$('#Page_Top #Catch2').animate({top:50,left:900 ,opacity:0},0).show().animate({opacity:1},100);
	},500);
	setTimeout(function(){
		$('#Page_Top #Catch1 span').animate({height:379},{duration:700});
	},1500);
	setTimeout(function(){
		$('#Page_Top #Catch2 span').animate({height:223},{duration:700});
	},2200);
	$('.k201607').click(function(){
		$(this).fadeOut(200);
		setTimeout(function(){
			scn3();
		},500);
	});
}
function scn201606(){
	setTimeout(function(){
		$('.k201606').animate({top:-100},0).show().animate({top:-140},{duration:800,easing:"easeOutBack"});
	},500);
	setTimeout(function(){
		$('#Page_Top #Catch1').animate({top:0,left:970 ,opacity:0},0).show().animate({opacity:1},100);
		$('#Page_Top #Catch2').animate({top:50,left:900 ,opacity:0},0).show().animate({opacity:1},100);
	},500);
	setTimeout(function(){
		$('#Page_Top #Catch1 span').animate({height:379},{duration:700});
	},1500);
	setTimeout(function(){
		$('#Page_Top #Catch2 span').animate({height:223},{duration:700});
	},2200);
	$('.k201606').click(function(){
		$(this).fadeOut(200);
		setTimeout(function(){
			scn3();
		},500);
	});
}
function scn2(){
	setTimeout(function(){
		$('#Page_Top #Catch1 span').animate({height:379},{duration:700});
	},500);
	setTimeout(function(){
		$('#Page_Top #Catch2 span').animate({height:223},{duration:700});
	},1200);
	setTimeout(function(){
		$('#Page_Top #Catch1').fadeOut(200);
		$('#Page_Top #Catch2').fadeOut(200);
	},4000);
	setTimeout(function(){
		$('#Page_Top #Catch1').animate({top:0,left:970 ,opacity:0},0).show().animate({opacity:1},100);
		$('#Page_Top #Catch2').animate({top:50,left:900 ,opacity:0},0).show().animate({opacity:1},100);
	},4500);
	setTimeout(function(){
		scn3();
	},3900);
}
function scn3(){
	setTimeout(function(){
		$('.k1').animate({top:30},0).show().animate({top:70},{duration:800,easing:"easeOutBack"});
	},1500);
		setTimeout(function(){
	$('.k2').animate({top:229},0).show().animate({top:279},{duration:1000,easing:"easeOutBack"});
	},1300);
		setTimeout(function(){
	$('.k3').animate({top:236},0).show().animate({top:286},{duration:1000,easing:"easeOutBack"});
	},1100);
	setTimeout(function(){
		$('.k4').animate({top:89},0).show().animate({top:139},{duration:1000,easing:"easeOutBack"});
	},900);
	setTimeout(function(){
		$('.k5').animate({top:81},0).show().animate({top:131},{duration:1000,easing:"easeOutBack"});
	},700);
	setTimeout(function(){
		$('.k6').animate({top:-33},0).show().animate({top:17},{duration:1000,easing:"easeOutBack"});
	},500);
	setTimeout(function(){
		$('.k7').animate({top:287},0).show().animate({top:333},{duration:1000,easing:"easeOutBack"});
	},300);
	setTimeout(function(){
		$('.k8').animate({top:29},0).show().animate({top:79},{duration:1000,easing:"easeOutBack"});
	},100);
	setTimeout(function(){
		//prpscr();
	},2500);
}

function topicjump(){
	//console.log("tp_length",tp_length);
	//console.log("tp_count",tp_count);
	//loopT=setInterval(function(){
		$("#Topic").animate({top:430-5},100,"easeOutQuad").animate({top:430},300,"easeOutBack");
		setTimeout(function(){
			$("#Topic").animate({top:430-5},100,"easeOutQuad").animate({top:430},300,"easeOutBack");
		},1500);
		setTimeout(function(){
			$("#Topic").animate({top:430-5},100,"easeOutQuad").animate({top:430},300,"easeOutBack");
		},3000);
		setTimeout(function(){
			if(tp_length>1){
				tp_count++;
				if(tp_count+1>tp_length){
					tp_count=0;
				}
				$("#Topic li").hide().eq(tp_count).show();
			}
			topicjump();
		},4500);
		
	//},1000);
}
function prpscr(){
	$(".top-page #BG .sub0 .prp_ck1").snowfall({
	    flakeCount : 1,
	    flakeIndex : "-5",
	    minSize : 49,
	    maxSize : 98,
	    minSpeed : 0.5,
	    maxSpeed : 1,
	    image : "/images/common_bgprop_ck1.png"});
	
	$(".top-page #BG .sub0 .prp_ck2").snowfall({
	    flakeCount : 3,
	    flakeIndex : "-5",
	    minSize : 29,
	    maxSize : 59,
	    minSpeed : 0.5,
	    maxSpeed : 1,
	    image : "/images/common_bgprop_ck2.png"});
	
	$(".top-page #BG .sub0 .prp_ck3").snowfall({
	    flakeCount : 3,
	    flakeIndex : "-5",
	    minSize : 38,
	    maxSize : 75,
	    minSpeed : 0.5,
	    maxSpeed : 1,
	    image : "/images/common_bgprop_ck3.png"});
	
	$(".top-page #BG .sub0 .prp_ctrl1").snowfall({
	    flakeCount : 1,
	    flakeIndex : "-5",
	    minSize : 68,
	    maxSize : 135,
	    minSpeed : 0.5,
	    maxSpeed : 1,
	    image : "/images/common_bgprop_ctrl1.png"});
	
	$(".top-page #BG .sub0 .prp_ctrl1").snowfall({
	    flakeCount : 2,
	    flakeIndex : "-5",
	    minSize : 46,
	    maxSize : 92,
	    minSpeed : 0.5,
	    maxSpeed : 1,
	    image : "/images/common_bgprop_ctrl2.png"});
	
	$(".top-page #BG .sub0 .prp_ctrl1").snowfall({
	    flakeCount : 2,
	    flakeIndex : "-5",
	    minSize : 49,
	    maxSize : 99,
	    minSpeed : 0.5,
	    maxSpeed : 1,
	    image : "/images/common_bgprop_ctrl3.png"});
	
}