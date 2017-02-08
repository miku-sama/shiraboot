<?php
namespace app\admin\controller;

use think\Controller;
use think\Request;

class Wlist extends Controller
{
	public function index(){
		if(!$userInfo=isAdmin(db('users')))
		{
			$this->error('没有查询到管理员信息','/Index/User/login');
			exit;
		}
		
		$wip = db('white_list');
		if(Request::instance()->isPost())
		{
			if($wip->where('ip',input('post.ip'))->find())
			{
				$this->error('此ip已经存在');
				exit;
			}
			$ipData = [
				'ip'=>input('post.ip'),
				'note'=>input('post.note'),
				'time'=>time(),
			];
			$wip->insert($ipData);
			$this->success('成功添加一条ip');
		}
		else
		{
			$this->assign('wList',$wip->select());
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
		$wip = db('white_list');
		if(!$wip->where('id',input('param.id'))->find())
		{
			$this->error('没有查询到此ip信息.');
		}
		else
		{
			$wip->where('id',input('param.id'))->delete();
			$this->success('成功删除一条ip');
		}
	}
}