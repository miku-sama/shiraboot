<?php
namespace app\admin\controller;

use think\Controller;
use think\Request;

class Server extends Controller
{
	public function __construct(){
		parent::__construct();
		if(!function_exists('ssh2_connect'))
		{
			$this->assign('test','<div class="alert alert-danger">面板所在的服务器不支持ssh2函数,这意味着所有直连的服务器无法正常工作,并可能导致一个错误!</div>');
		}else
		{
			$this->assign('test','');
		}
    }
	
    public function index()
    {
		$user=db('users');
		if(!$userInfo=isAdmin($user))
		{
			$this->error('没有查询到管理员信息','/Index/User/login');
			exit;
		}
		$server = db('server');
		$serverInfo = $server->select();
		$this->assign('userInfo',$userInfo);
		$this->assign('serverInfo',$serverInfo);
		return $this->fetch();
    }
	
    public function add()
    {
		if(!$userInfo=isAdmin(db('users')))
		{
			$this->error('没有查询到管理员信息','/Index/User/login');
			exit;
		}
		if(Request::instance()->isPost())
		{
			$server = db('server');
			if($serverInfo = $server->where('host',input('host'))->find())
			{
				$this->error('此节点信息已经存在!');
				exit;
			}
			if(input('post.type')==1)
			{
				if(!input('?post.name') || !input('?post.passwd') || !input('?post.mode') || !input('?post.host') || !input('?post.username') || !input('?post.tool_dir') || !input('?post.port'))
				{
					$this->error('信息填写不完整!');
					exit;
				}
			}elseif(input('post.type')==2)
			{
				if(!input('?post.name') || !input('?post.passwd') || !input('?post.mode') || !input('?post.host'))
				{
					$this->error('信息填写不完整!');
					exit;
				}
			}
			else
			{
				$this->error('信息填写不完整!');
				exit;
			}

			$serverInfo = [
				'name'=>input('post.name'),
				'type'=>input('post.type'),
				'vip'=>input('post.vip'),
				'host'=>input('post.host'),
				'port'=>input('post.port'),
				'username'=>input('post.username'),
				'passwd'=>input('post.passwd'),
				'tool_dir'=>input('post.tool_dir'),
				'mode'=>json_encode($_POST['mode']), //input取不到数组
				'maximum'=>input('post.maximum'),
				'note'=>input('post.note'),
			];
			$server->insert($serverInfo);
			$this->success('节点添加成功',"Index/index");
		}
		else
		{
			$config = db('config');
			$methodList = [
				'reflect' =>json_decode($config->where('name','reflect')->find()['value'],true),
				'usually' =>json_decode($config->where('name','usually')->find()['value'],true),
				'application' =>json_decode($config->where('name','application')->find()['value'],true),
			];
			$this->assign('methodList',$methodList);
			$this->assign('userInfo',$userInfo);
			return $this->fetch();
		}
    }
	
	public function edit()
	{
		if(!$userInfo=isAdmin(db('users')))
		{
			$this->error('没有查询到管理员信息','/Index/User/login');
			exit;
		}
		
		if(!input('?param.id'))
		{
			$this->error('参数不正确');
			exit;
		}
		$server = db('server');
		if(!$serverInfo = $server->where('id',input('id'))->find())
		{
			$this->error('没有找到服务器信息');
			exit;
		}
		
		if(Request::instance()->isPost())
		{
			if(input('post.type')==1)
			{
				if(!input('?post.name') || !input('?post.passwd') || !input('?post.mode') || !input('?post.host') || !input('?post.username') || !input('?post.tool_dir') || !input('?post.port'))
				{
					$this->error('信息填写不完整!');
					exit;
				}
			}elseif(input('post.type')==2)
			{
				if(!input('?post.name') || !input('?post.passwd') || !input('?post.mode') || !input('?post.host'))
				{
					$this->error('信息填写不完整!');
					exit;
				}
			}
			else
			{
				$this->error('信息填写不完整!');
				exit;
			}

			$serverInfo = [
				'name'=>input('post.name'),
				'type'=>input('post.type'),
				'vip'=>input('post.vip'),
				'host'=>input('post.host'),
				'port'=>input('post.port'),
				'username'=>input('post.username'),
				'passwd'=>input('post.passwd'),
				'tool_dir'=>input('post.tool_dir'),
				'mode'=>json_encode($_POST['mode']), //input取不到数组
				'maximum'=>input('post.maximum'),
				'note'=>input('post.note'),
			];
			$server->where('id',input('id'))->update($serverInfo);
			$this->success('节点信息修改成功',"Index/index");
			
		}
		else
		{
			$config = db('config');
			$methodList = [
				'reflect' =>json_decode($config->where('name','reflect')->find()['value'],true),
				'usually' =>json_decode($config->where('name','usually')->find()['value'],true),
				'application' =>json_decode($config->where('name','application')->find()['value'],true),
			];
			$this->assign('methodList',$methodList);
			$this->assign('userInfo',$userInfo);
			$this->assign('serverInfo',$serverInfo);
			return $this->fetch();
		}
	}
	
	public function del(){
		
		if(!$userInfo=isAdmin(db('users')))
		{
			$this->error('没有查询到管理员信息','/Index/User/login');
			exit;
		}
		
		if(!input('?param.id'))
		{
			$this->error('参数不正确');
			exit;
		}
		$server = db('server');
		if(!$serverInfo = $server->where('id',input('id'))->find())
		{
			$this->error('没有找到服务器信息');
			exit;
		}

		if(input('param.do')=='yes' && input('?param.id'))
		{
			$server->where('id',input('param.id'))->delete();
			$this->success('删除成功','Index/index');
		}
		else
		{
			$this->assign('userInfo',$userInfo);
			$this->assign('serverInfo',$serverInfo);
			return $this->fetch();
		}
	}
}
