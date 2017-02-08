$(document).ready( function(){

$("#lv3-input button").click(function(){
	var val=$("#lv3-input [name=lv3-form]").val();
	if(val=="trifolium"){
		$("#lv3-dl").html('<br /><p>正解です！<br />希望サイズを選択し、保存してご利用ください。</p><p><a href="/assets/special/newspaper/newgame_wp_3_1.jpg">PC 1366x768</a> <a href="/assets/special/newspaper/newgame_wp_3_2.jpg">PC 1920x1080</a></p><p><a href="/assets/special/newspaper/newgame_wp_3_3.jpg">iPhone 750x1334</a> <a href="/assets/special/newspaper/newgame_wp_3_4.jpg">Android 1440x1280</a></p>');
	}else{
		$("#lv3-dl").html('<br /><p>残念ですが不正解です。<br />もう一度記事を読んでチャレンジしてください！</p>');
	}
});

$("#lv2-input button").click(function(){
	var val=$("#lv2-input [name=lv2-form]").val();
	if(val=="合いの手"){
		$("#lv2-dl").html('<br /><p>正解です！<br />希望サイズを選択し、保存してご利用ください。</p><p><a href="/assets/special/newspaper/newgame_wp_2_1.jpg">PC 1366x768</a> <a href="/assets/special/newspaper/newgame_wp_2_2.jpg">PC 1920x1080</a></p><p><a href="/assets/special/newspaper/newgame_wp_2_3.jpg">iPhone 750x1334</a> <a href="/assets/special/newspaper/newgame_wp_2_4.jpg">Android 1440x1280</a></p>');
	}else{
		$("#lv2-dl").html('<br /><p>残念ですが不正解です。<br />もう一度記事を読んでチャレンジしてください！</p>');
	}
});

$("#lv1-input button").click(function(){
	var val=$("#lv1-input [name=lv1-form]").val();
	if(val=="ライブ"){
		$("#lv1-dl").html('<br /><p>正解です！<br />希望サイズを選択し、保存してご利用ください。</p><p><a href="/assets/special/newspaper/newgame_wp_1_1.jpg">PC 1366x768</a> <a href="/assets/special/newspaper/newgame_wp_1_2.jpg">PC 1920x1080</a></p><p><a href="/assets/special/newspaper/newgame_wp_1_3.jpg">iPhone 750x1334</a> <a href="/assets/special/newspaper/newgame_wp_1_4.jpg">Android 1440x1280</a></p>');
	}else{
		$("#lv1-dl").html('<br /><p>残念ですが不正解です。<br />もう一度記事を読んでチャレンジしてください！</p>');
	}
});

});