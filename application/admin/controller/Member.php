<?php
namespace app\admin\controller;

use think\Controller;
use think\Request;

class Member extends Controller
{
	public function index(){
		$user=db('users');
		if(!$userInfo=isAdmin($user))
		{
			$this->error('没有查询到管理员信息','/Index/User/login');
			exit;
		}
		$plans = db('plans');
		if(Request::instance()->isPost())
		{
			if(!input('post.username') || !input('post.passwd') || !input('post.email') || !input('post.status') || !input('post.type'))
			{
				$this->error('信息填写不完整(用户名,密码,邮箱,用户状态,用户类型为必填项!)');
				exit;
			}
			else
			{
				$username = input('post.username');
				$email = input('post.email');
				if($user->where("username = '{$username}' or email='{$email}'")->find())
				{
					$this->error('已经有相同的会员存在!');
					exit;
				}
			}
			$userData = [ //部分必填用户信息
				'username'=>input('post.username'),
				'passwd'=>input('post.passwd'),
				'email'=>input('post.email'),
				'register_time'=>time(),
				'status'=>input('post.status'),
				'type'=>input('post.type'),
			];
			if(!input('post.plan'))
			{
				if(!$planInfo = $plans->where('id',input('post.plan'))->find())
				{
					$this->error('套餐信息不存在!');
					exit;
				}
				$userData['plan'] = $planInfo['id'];
				$userData['plan_name'] = $planInfo['name'];
				$userData['maxtime'] = $planInfo['maxtime'];
				$userData['maxnum'] = $planInfo['maxnum'];
				$userData['maxboot'] = $planInfo['maxboot'];
				$userData['remainder'] = $planInfo['maxnum'];
				$userData['expiration'] = input('post.expiration') * 86400 + time() ;
				$userData['vip'] = input('post.vip');
			}
			else
			{
				$userData['plan'] = 0;
				$userData['plan_name'] = '未购买套餐';
				$userData['maxtime'] = 0;
				$userData['maxnum'] = 0;
				$userData['maxboot'] = 0;
				$userData['remainder'] = 0;
				$userData['expiration'] = 0 ;
				$userData['vip'] = 0;
			}
			$user->insert($data);
			$this->success('成功添加一条会员信息!');
		}
		else
		{
			$userList = $user->select();
			$this->assign('userInfo',$userInfo);
			$this->assign('planInfo',$plans->select());
			$this->assign('userList',$userList);
			return $this->fetch();
		}

	}
	
	public function edit(){
		$user=db('users');
		if(!$userInfo=isAdmin($user))
		{
			$this->error('没有查询到管理员信息','/Index/User/login');
			exit;
		}
		
		if(!input('?param.id') || !$userEdit = $user->where('id',input('param.id'))->find())
		{
			$this->error('参数有误或会员信息不存在!');
			exit;
		}
		$plans = db('plans');
		$planInfo = $plans->select();
		if(Request::instance()->isPost())
		{
			if($userEdit['expiration'] > time() && $userEdit['plan'] != input('param.plan') && input('?param.plan')) //用户套餐未到期且提交信息不一样时
			{
				$this->error('此用户的套餐未到期,禁止变更套餐类型!');
				exit;
			}
			elseif($userEdit['expiration'] <= time() && $userEdit['plan'] != input('param.plan') && input('?param.day')) //用户套餐到期想要变更套餐时
			{
				$userEdit['plan'] = input('param.plan'); //套餐id
				if(!$planInfo = $plans->where('id',input('param.plan'))->find()) //避免修改时套餐被其它管理员删除
				{
					$this->error('套餐不存在!');
					exit;
				}
				$userEdit['plan_name'] = $planInfo['name']; //套餐名
				$userEdit['maxtime'] = $planInfo['maxtime']; //最大时间
				$userEdit['maxboot'] = $planInfo['maxboot']; //最大并发
				$userEdit['remainder'] = $planInfo['maxnum']; //重置当天次数
				$userEdit['expiration'] = time() + input('param.day')*86400; //天数
				
				
			}
			elseif($userEdit['expiration'] <= time() && $userEdit['plan'] == input('param.plan') && input('?param.day')) //用户套餐到期后续期
			{
				$userEdit['expiration'] = input('param.day')*86400 + time(); //天数
			}
			elseif(input('?param.day')) //用户套餐未到期续期
			{
				$userEdit['expiration'] = $userEdit['expiration'] + input('param.day')*86400; //天数
			}
			else
			{
				$this->error('您的参数有误!');
				exit;
			}
			if(input('?param.email') && $userEdit['email'] != input('param.email') && input('param.passwd') != null) //邮箱修改
			{
				$userEdit['email'] = input('param.email');
			}
			if(input("?param.passwd") && input('param.passwd') != null) //密码更改
			{
				$userEdit['passwd'] = cpwd(input("param.passwd"));
			}
			
			$userEdit['vip'] = input('param.vip'); //设置用户是否为vip
			$userEdit['status'] = input('param.status'); //设置用户状态
			
			$user->where('id',input('param.id'))->update($userEdit);
			$this->success('会员信息修改成功!');
		}
		else
		{
			$this->assign('planInfo',$planInfo);
			$this->assign('userInfo',$userInfo);
			$this->assign('userEdit',$userEdit);
			return $this->fetch();
		}
	}
}