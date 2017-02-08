<?php
//	检测登录
function chickLogin ($odb){
	if(!cookie('uid') || !cookie('passwd'))
	{
		return false;
	}
	else
	{
		if(!$userInfo=getUserInfo($odb))
		{
			return false;
			exit;
		}
		elseif($userInfo['maxnum']==0 || $userInfo['remainder']==0)
		{
			$userInfo['percent'] = 0;
		}else
		{
			$userInfo['percent'] = $userInfo['remainder']/$userInfo['maxnum']*100;
		}
			
	}
	return $userInfo;
}

//	密码加密
function cpwd ($pwd){
	return md5(config('pwdhead').md5($pwd).config('pwdfoot'));
}
//获取用户信息
function getUserInfo($odb,$arr=null)
{
	if(!$arr)
	{
		$uid = cookie('uid');
		$passwd = cookie('passwd');
		$sql = "id='{$uid}' and passwd='{$passwd}'";
	}
	else
	{
		$user = $arr['user'];
		$passwd = $arr['passwd'];
		$sql = "(username='{$user}' or email='{$user}') and passwd='{$passwd}'";
	}
	return $odb->where($sql)->find();
}
//历史记录查询
function bootHistory($odb,$limit=20)
{
	$uid=cookie('uid');
	return $odb->where(['uid'=>cookie('uid')])->order('start_time desc')->limit($limit)->select();
}

//获取正在进行中的攻击数量
function getBooting($odb,$uid=null){
	if($uid==null) {
		$uid = cookie('uid');
	}
	return $odb->where("uid='{$uid}' and stop = 0 and start_time+time > UNIX_TIMESTAMP()")->count();
}

//是否为管理员
function isAdmin($odb=null){
	if(!$odb)
	{
		$odb = db('users');
	}
	if(!$userInfo=$odb->where(['id'=>cookie('uid'),'passwd'=>cookie('passwd')])->find())
	{
		return false;
	}
	elseif($userInfo['type']!=2)
	{
		return false;
	}
	else
	{
		return $userInfo;
	}
	
}
//get请求
function getRequest($url,$arr){
	$url = $url.'?'.http_build_query($arr);
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HEADER, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	$data = curl_exec($curl);
	curl_close($curl);
	return $data;
}
//html2text
function html2text($str)
{
 $str = preg_replace("/<sty(.*)\/style>|<scr(.*)\/script>|<!--(.*)-->/isU","",$str);
 $alltext = "";
 $start = 1;
 for($i=0;$i<strlen($str);$i++)
 {
  if($start==0 && $str[$i]==">")
  {
   $start = 1;
  }
  else if($start==1)
  {
   if($str[$i]=="<")
   {
    $start = 0;
    $alltext .= " ";
   }
   else if(ord($str[$i])>31)
   {
    $alltext .= $str[$i];
   }
  }
 }
 $alltext = str_replace("　"," ",$alltext);
 $alltext = preg_replace("/&([^;&]*)(;|&)/","",$alltext);
 $alltext = preg_replace("/[ ]+/s"," ",$alltext);
 return $alltext;
}
//获取随机字符
function getCode($len) {
    $srcstr = "1a2s3d4f5g6hj8k9qwertyupzxcvbnm";
    mt_srand();
    $strs = "";
    for ($i = 0; $i < $len; $i++) {
        $strs .= $srcstr[mt_rand(0, 30)];
    }
    return $strs;
}
//获取用户ip
function getIP() { 
	if (getenv('HTTP_CLIENT_IP')) { 
	$ip = getenv('HTTP_CLIENT_IP'); 
	} 
	elseif (getenv('HTTP_X_FORWARDED_FOR')) { 
	$ip = getenv('HTTP_X_FORWARDED_FOR'); 
	} 
	elseif (getenv('HTTP_X_FORWARDED')) { 
	$ip = getenv('HTTP_X_FORWARDED'); 
	} 
	elseif (getenv('HTTP_FORWARDED_FOR')) { 
	$ip = getenv('HTTP_FORWARDED_FOR'); 

	} 
	elseif (getenv('HTTP_FORWARDED')) { 
	$ip = getenv('HTTP_FORWARDED'); 
	} 
	else { 
	$ip = $_SERVER['REMOTE_ADDR']; 
	} 
	return $ip; 
} 