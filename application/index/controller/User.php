<?php
namespace app\index\controller;

use think\Controller;
use think\Request;

class User extends Controller
{
    public function index(){
		$user = db('users'); //用户表
        if(!$userInfo = chickLogin($user))
		{
			$this->error('未登录','User/login');
			exit;
		}
		$history = db('history'); //历史表
		$bootInfo = [
			'server' => db('server')->count(), //节点数量
			'history' => $history->count(), //总攻击量
			'booting' => $history->where("start_time+time > UNIX_TIMESTAMP()")->count(), //进行中的数量
			'user' => $user->count(), //总用户
		];
		$bootHistory = bootHistory($history,8);
		$this->assign('userInfo',$userInfo);
		$this->assign('bootInfo',$bootInfo);
		$this->assign('bootHistory',$bootHistory);
		$this->assign('articleList',db('article')->where("type <> 0 ")->order('type desc,time desc')->select());
		return $this->fetch();
		
    }
    public function login(){
		if(input('param.mode')=='logout')
		{
			cookie('uid',null);
			cookie('passwd',null);
			$this->success('登出成功','User/login');
			exit;
		}
		if(Request::instance()->isPost())
		{
			$result = $this->validate(input('post.'),'User.login');
			if(true !== $result)
			{
				$this->error($result);
				exit;
			}
			
			$User = db('users');
			$tmp = [
				'user'=>input('post.user'),
				'passwd'=>cpwd(input('post.passwd'))
			];
			
			if(!$userInfo=getUserInfo($User,$tmp))
			{
				$this->error('用户不存在或您输入的信息有误!');
			}
			else
			{
				cookie('uid',$userInfo['id']);
				cookie('passwd',$tmp['passwd']);
				$this->success('登录成功!','User/index');
			}
		}
		else
		{
			return $this->fetch();
		}
    }
    public function register(){
        if(Request::instance()->isPost())
		{
			$result = $this->validate(input('post.'),'User.register');
			if(true !== $result)
			{
				$this->error($result);
				exit;
			}
			$userInfo = [
				'username'=>input('post.user'),
				'passwd'=>cpwd(input('post.passwd')),
				'email'=>input('post.email'),
				'register_time'=>time(),
				'status'=>1,
				'type'=>1,
				'plan'=>0,
				'maxnum'=>0,
				'maxboot'=>0,
				'remainder'=>0,
				'expiration'=>0,
				'vip'=>0,
			];
			$User = db('users'); 
			if($User->where("username='{$userInfo['username']}' or email='{$userInfo['email']}'")->find())
			{
				$this->error('用户名或邮箱已经存在');
			}
			else
			{
				$User->insert($userInfo);
				$this->success('注册成功','index/User/login');
			}
		}
		else
		{
			//echo "GET";
			return $this->fetch();
		}
    }
	
	public function history(){
        if(!$userInfo = chickLogin(db('users')))
		{
			$this->error('未登录','index/User/login');
			exit;
		}
		
		$history = db('history');
		if(!input('?param.list') || input('param.list')<=0)
		{
			$historyList = $history->where('uid',$userInfo['id'])->order('start_time desc')->limit(20)->select();
			$list['previous']=0;
		}
		else
		{
			$historyList = $history->where('uid',$userInfo['id'])->limit(input('param.list')*20,20)->select();
			$list['previous']=input('param.list')-1;
		}
		
		if(input('param.list')+1*20 > $history->where('uid',$userInfo['id'])->count()) //如果分页+1*每页数量大于总数则没有下一页
		{
			$list['next']=0;
		}else
		{
			$list['next']=input('param.list')+1;
		}
		
		$this->assign('historyList',$historyList);
		$this->assign('list',$list);
		$this->assign('userInfo',$userInfo);
		return $this->fetch();
	}
	
	public function activate(){
		$user = db('users');
		if(!$userInfo = chickLogin($user))
		{
			$this->error('未登录','User/login');
			exit;
		}
		
		if(Request::instance()->isPost())
		{
			$cdkey = db('cdkey');
			if(!$cdkeyInfo = $cdkey->where(['value'=>input('post.value'),'status'=>0])->find())
			{
				$this->error('密钥不存在或者密钥不是未使用状态!');
				exit;
			}
			
			if($cdkeyInfo['type'] == 1) //普通的套餐类型
			{
				if($userInfo['plan'] != $cdkeyInfo['plan'] && $userInfo['expiration'] > time()) //套餐不同且未到期
				{
					$this->error('您的账户已经有一个在有效期内的套餐,并且和您购买的卡密所属套餐不同,请等待到期后再次激活此卡密!');
					exit;
				}
				
				if(!$planInfo = db('plans')->where('id',$cdkeyInfo['plan'])->find()){
					$this->error('没有找到对应此密钥的套餐信息,请联系管理员!');
					exit;
				}
				
				if($userInfo['plan'] == $cdkeyInfo['plan'] && $userInfo['expiration'] > time()) //套餐相同但未到期
				{
					$userInfo['expiration'] = $userInfo['expiration'] + $cdkeyInfo['expiration'];
					$successInfo = '续期成功';
				}
				else
				{
					$userInfo['expiration'] = time() + $cdkeyInfo['expiration'];
					$successInfo = '激活成功';
				}
				
				unset($userInfo['percent']); //删除用户数据的百分比
				$userInfo['plan'] = $planInfo['id'];
				$userInfo['plan_name'] = $planInfo['name'];
				$userInfo['maxtime'] = $planInfo['maxtime'];
				$userInfo['maxboot'] = $planInfo['maxboot'];
				$userInfo['maxnum'] = $planInfo['maxnum'];
				$userInfo['remainder'] = $planInfo['maxnum'];
				$userInfo['vip'] = $planInfo['vip'];
				$user->where('id',$userInfo['id'])->update($userInfo);
			}
			elseif($cdkeyInfo['type'] == 2) //临时补充包
			{
				$userInfo['remainder'] = $userInfo['remainder'] + $cdkeyInfo['num'];
				$successInfo = '相应的临时补充包已经应用到您的账户,请在次日0时使用,逾期失效!';
				$user->where('id',$userInfo['id'])->update($userInfo);
			}
			elseif($cdkeyInfo['type'] == 3) //套餐补充包
			{
				$userInfo['maxnum'] = $userInfo['maxnum'] + $cdkeyInfo['num'];
				$userInfo['remainder'] = $userInfo['remainder'] + $cdkeyInfo['num'];
				$successInfo = '套餐补充包以应用到您的账户,请注意套餐到期时间,套餐补充包将随着套餐的到期而失效!';
				$user->where('id',$userInfo['id'])->update($userInfo);
			}
			elseif($cdkeyInfo['type'] == 4) //vip权限
			{
				$apiData = [
					'uid'=>$userInfo['id'],
					'api_key'=>cpwd($userInfo['passwd'].time()),
					'expiration'=>time() + $cdkeyInfo['expiration'],
					'status'=>1,
				];
				db('api')->insert($apiData);
				$successInfo = 'api已经成功应用到您的账户.';
			}
			elseif($cdkeyInfo['type'] == 5) //并发补充包
			{
				$userInfo['maxboot'] = $userInfo['maxboot'] + $cdkeyInfo['num'];
				$successInfo = '并发补充包已经应用到您的账户,请注意套餐到期时间,并发补充包将随着套餐的到期而失效!';
				$user->where('id',$userInfo['id'])->update($userInfo);
			}
			else
			{
				$this->error('密钥信息有误,请联系管理员!');
				exit;
			}
			
			//更新密钥使用信息
			$cdkeyInfo['status'] = 1;
			$cdkeyInfo['uid'] =$userInfo['id'];
			$cdkeyInfo['use_time'] =time();
			$cdkey->where('id',$cdkeyInfo['id'])->update($cdkeyInfo);
			$this->success($successInfo,'Index/Hub/index');
			

		}
		else
		{
			$this->assign('userInfo',$userInfo);
			return $this->fetch();
		}
	}
	
	public function info(){
		
		$user = db('users'); //用户表
        if(!$userInfo = chickLogin($user))
		{
			$this->error('未登录','User/login');
			exit;
		}
		$vcode = db('verification_code');
		if(input('?param.mailCode')) //获取邮箱验证码
		{
			$code = getCode(6);
			$codeData = [
				'uid'=>$userInfo['id'],
				'email'=>$userInfo['email'],
				'value'=>$code,
				'type'=>2,
				'expiration'=>time()+3800,
			];
			$vcode->insert($codeData);
			$this->success('验证码已经发送到您的账户原邮箱内,请注意查收!');
			exit;
		}
		elseif(input('?param.pwdCode')) //获取账户验证码
		{
			$code = getCode(6);
			$codeData = [
				'uid'=>$userInfo['id'],
				'email'=>$userInfo['email'],
				'value'=>$code,
				'type'=>3,
				'expiration'=>time()+3800,
			];
			$vcode->insert($codeData);
			$this->success('验证码已经发送到您的邮箱内,请注意查收!');
			exit;
		}
		elseif(input('?param.remail')) //修改邮箱
		{
			$result = $this->validate(input('param.'),'User.reemail');
			if(true !== $result)
			{
				$this->error($result);
				exit;
			}
			if(!$codeInfo = $vcode->where(['uid'=>$userInfo['id'],'value'=>input('param.code'),'email'=>$userInfo['email'],'type'=>2])->find())
			{
				$this->error('未查询到验证码信息!');
				exit;
			}
			elseif($codeInfo['expiration'] < time())
			{
				$vcode->where('id',$codeInfo['id'])->delete();
				$this->error('验证码已过期,请重新获取!');
				exit;
			}
			else
			{
				unset($userInfo['percent']);
				$userInfo['email'] = input('param.newemail');
				$user->where('id',$userInfo['id'])->update($userInfo);
				$vcode->where('id',$codeInfo['id'])->delete();
				$this->success('账户信息修改成功!');
				exit;
			}
		}
		elseif(input('?param.repwd')) //修改密码
		{
			$result = $this->validate(input('param.'),'User.repwd');
			if(true !== $result)
			{
				$this->error($result);
				exit;
			}
			if(!$codeInfo = $vcode->where(['uid'=>$userInfo['id'],'value'=>input('param.code'),'email'=>$userInfo['email'],'type'=>3])->find())
			{
				$this->error('未查询到验证码信息!');
				exit;
			}
			elseif($codeInfo['expiration'] < time())
			{
				$vcode->where('id',$codeInfo['id'])->delete();
				$this->error('验证码已过期,请重新获取');
				exit;
			}
			else
			{
				unset($userInfo['percent']);
				$userInfo['passwd'] = cpwd(input('param.newpwd'));
				$user->where('id',$userInfo['id'])->update($userInfo);
				$vcode->where('id',$codeInfo['id'])->delete();
				$this->success('账户信息修改成功!');
				exit;
			}
		}
		else
		{
			$this->assign('userInfo',$userInfo);
			return $this->fetch();
		}

	}
	
}
