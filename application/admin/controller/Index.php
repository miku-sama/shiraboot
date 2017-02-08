<?php
namespace app\admin\controller;

use think\Controller;
use think\Request;

class Index extends Controller
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
}
