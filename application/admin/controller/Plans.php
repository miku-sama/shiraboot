<?php
namespace app\admin\controller;

use think\Controller;
use think\Request;

class Plans extends Controller
{
	public function index(){
		if(!$userInfo=isAdmin(db('users')))
		{
			$this->error('没有查询到管理员信息','/Index/User/login');
			exit;
		}
		$plans = db('plans');
		if(Request::instance()->isPost())
		{
			if(!input('post.name') || !input('post.maxtime') || !input('post.maxnum') || !input('?post.vip') || !input('?post.status') || !input('post.maxboot') || !input('post.day') || !input('post.price'))
			{
				$this->error('缺少必要参数!');
				exit;
			}
			$planInfo = [
				'name'=>input('post.name'),
				'price'=>input('post.price'),
				'cycle'=>input('post.day') * 86400,
				'maxtime'=>input('post.maxtime'),
				'maxnum'=>input('post.maxnum'),
				'maxboot'=>input('post.maxboot'),
				'status'=>input('post.status'),
				'vip'=>input('post.vip'),
			];
			$plans->insert($planInfo);
			$this->success('套餐添加成功!');
		}
		$this->assign('planList',$plans->select());
		$this->assign('userInfo',$userInfo);
		return $this->fetch();
	}
	
	public function edit(){
		if(!$userInfo=isAdmin(db('users')))
		{
			$this->error('没有查询到管理员信息','/Index/User/login');
			exit;
		}
		
		$plans = db('plans');
		if(!input('param.id') || !$planInfo = $plans->where('id',input('id'))->find())
		{
			$this->error('未查询到套餐信息或参数不正确.');
			exit;
		}
		
		if(Request::instance()->isPost())
		{
			if(!input('post.name') || !input('post.maxtime') || !input('post.maxnum') || !input('?post.vip') || !input('?post.status') || !input('post.maxboot') || !input('post.day') || !input('post.price'))
			{
				$this->error('缺少必要参数!');
				exit;
			}
			$planInfo = [
				'name'=>input('post.name'),
				'price'=>input('post.price'),
				'cycle'=>input('post.day') * 86400,
				'maxtime'=>input('post.maxtime'),
				'maxnum'=>input('post.maxnum'),
				'maxboot'=>input('post.maxboot'),
				'status'=>input('post.status'),
				'vip'=>input('post.vip'),
			];
			$plans->where('id',input('param.id'))->update($planInfo);
			$this->success('套餐修改成功!','Admin/Plans/index');
		}
		else
		{
			$this->assign('planInfo',$planInfo);
			$this->assign('userInfo',$userInfo);
			return $this->fetch();
		}
	}
	
	public function del(){
		if(!$userInfo=isAdmin(db('users')))
		{
			$this->error('没有查询到管理员信息','/Index/User/login');
			exit;
		}
		
		$plans = db('plans');
		if(!input('param.id') || !$plans->where('id',input('id'))->find())
		{
			$this->error('未查询到套餐信息或参数不正确.');
			exit;
		}
		if(db('cdkey')->where(['plan'=>input('param.id'),'status'=>0])-find()){
			$this->error('此套餐还有未使用的密钥,不能删除(想删除可以先删除此套餐未使用的密钥,但不推荐这么做!).');
		}
		$plans->where('id',input('param.id'))->delete();
		$this->success('套餐删除成功!','Admin/Plans/index');
	}
}